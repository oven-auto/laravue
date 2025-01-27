<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;

use App\Classes\LadaDNM\DNM;
use App\Classes\LadaDNM\DNMClient;
use App\Classes\LadaDNM\DNMClientService;
use App\Classes\LadaDNM\DNMEvent;
use App\Classes\LadaDNM\DNMFactory;
use App\Classes\LadaDNM\DNMWorksheet;
use App\Classes\LadaDNM\DNMWorksheetService;
use App\Classes\ORM\ORMConnection;
use App\Classes\ORM\Trafc;
use App\Classes\Vin\Vin;
use App\Events\ClientCreateOrUpdateEvent;
use App\Events\DNMVisitEvent;
use App\Events\ReserveCreateEvent;
use App\Events\WorksheetCreateEvent;
use App\Helpers\String\StringHelper;
use App\Jobs\CreateDNMReserveJob;
use App\Jobs\TestJob;
use App\Listeners\DNMReserveCreateListener;
use App\Models\Car;
use App\Models\Client as ModelsClient;
use App\Models\ClientFile;
use App\Models\ClientUnion;
use App\Models\DealerColorImage;
use App\Models\DiscountModul;
use App\Models\MarkAlias;
use App\Classes\ORM\Trafic;
use App\Models\Tuning;
use App\Models\Worksheet;
use App\Models\WsmReserveNewCar;
use App\Repositories\Car\Car\CarRepository;
use App\Services\Car\CalculatePaidDate;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Icewind\SMB\BasicAuth;
use Icewind\SMB\ServerFactory;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use ReflectionClass;
use SplQueue;
use SplStack;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use ZipArchive;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}



    /**
     * ПОЛУЧИТЬ СОСТОЯНИЕ ВЕБХУКА ТЕЛЕГРАМ
     */
    public function get()
    {
        $token = env('TELEGRAM_KEY');

        $telegram = new Api($token);

        $res = $telegram->getWebhookInfo();

        dump($res);
    }



    /**
     * УСТАНОВИТЬ ВЕБХУК ТЕЛЕГРАМ
     */
    public function set()
    {
        $token = env('TELEGRAM_KEY');

        $url = 'https://62.182.31.140/telegram/bot';

        $command = 'curl -k -F "url=' . $url . '" -F "certificate=@cert/cert2.pem" "https://api.telegram.org/bot' . $token . '/setWebhook"';

        exec($command, $out, $res);

        dump($out);
    }



    /**
     * УДАЛИТЬ ВЕБХУК ТЕЛЕГРАМ
     */
    public function del()
    {
        // $token = env('TELEGRAM_KEY');

        // $telegram = new Api($token);

        // $res = $telegram->removeWebhook();
    }



    /**
     * БОТ ТЕЛЕГРАМ
     */
    public function bot(Request $request) {}



    private function priority(string $key) : int
    {
        return match($key){
            '(' => 0,
            ')' => 0,
            '+' => 1,
            '-' => 1,
            '*' => 2,
            '/' => 2,
            '^' => 3,
            '~' => 4,
            'default' => 0,
        };
    }



    public function isOperand(string $key) : bool
    {
        $operand = ['+', '-', '*', '/', '^', '~', '(', ')'];

        return in_array($key, $operand) ? 1 : 0;
    }



    public function isUnarMinus(string $key)
    {
        return $key == '~' ? 1 : 0;
    }



    public function toInfix(string $data) : SplQueue
    {
        $queue = new SplQueue();
        $tmpVal = '';
        
        for($i = 0; $i < mb_strlen($data); $i++)
        {
            if($this->isOperand($data[$i]))
            {
                if($tmpVal)
                    $queue->push($tmpVal);

                
                if(($queue->count() == 0 || $this->isOperand($data[$i-1])) && $data[$i] == '-')
                {
                    $queue->push(0);
                    $queue->push('-');
                    continue;
                }


                $queue->push($data[$i]);
                $tmpVal = '';
            }
            else
            {
                $tmpVal .= $data[$i];
            }

            if($i == mb_strlen($data)-1)
                if($tmpVal)
                    $queue->push($tmpVal);
        }

        return $queue;
    }



    public function postFix(SplQueue $data) : SplQueue
    {
        $currentPriority    = 0;
        $priority           = 0;
        $stackString        = new SplQueue();
        $stackOper          = new SplQueue();
        
        while($data->count())
        {   
            if(!$this->isOperand($data->bottom()))
                $stackString->push($data->shift());

            else
            {
                $currentPriority = $this->priority($data->bottom());                

                if($data->bottom() == ')')
                {
                    while($stackOper->count())
                    {
                        if($stackOper->top() == '(')
                            break;
                        $stackString->push($stackOper->pop());
                    }
                    dump($stackOper);
                    $priority = $currentPriority;
                    continue;
                }

                // if($currentPriority <= $priority)
                // {
                //     while($stackOper->count())
                //     {
                //         if($stackOper->top() == '(')
                //             break;
                //         $stackString->push($stackOper->pop());
                //     }
                // }

                $stackOper->push($data->shift());

                $priority = $currentPriority;
            }

            if($data->count()==0)
                while($stackOper->count())  
                    $stackString->push($stackOper->pop());
        }

        return $stackString;
    }



    public function printStack($stack)
    {
        $res = '';
        foreach($stack as $item)
            $res .= $item;
        dump($res);
    }



    public function calculate(SplQueue $queue) : int|float
    {
        $valStack = new SplStack();
        $y = 0;
        $x = 0;
        
        foreach($queue as $item)
        {
            if($item)
                if(!$this->isOperand($item))
                {
                    $valStack->push($item);
                }
                else
                {   
                    if($this->isUnarMinus($item))
                    {
                        $y = 0;
                        $x = $valStack->pop();
                    }
                    else
                    {
                        $y = $valStack->pop();
                        $x = $valStack->pop();
                    }

                    
                    dump($x . ' & '.$y);
                    $valStack->push(match($item){
                        '+' => $this->summation($x, $y),
                        '-' => $this->subtraction($x, $y),
                        '~' => $this->subtraction($x, $y),
                        '*' => $this->multiplication($x, $y),
                        '/' => $this->division($x, $y),
                        '^' => $this->exponentiation($x, $y),
                        'default' => throw new \Exception('Error'),
                    });
                }
        }

        return $valStack->top();
    }



    public function summation(int|float $x, int|float $y)  : int|float
    {
        return $x + $y;
    }



    public function subtraction(int|float $x, int|float $y) : int|float
    {
        return $x - $y;
    }



    public function multiplication(int|float $x, int|float $y) : int|float
    {
        return $x * $y;
    }



    public function division(int|float $x, int|float $y) : int|float
    {
        return $x / $y;
    }



    public function exponentiation(int|float $x, int|float $y) : int|float
    {
        return pow($x, $y);
    }



    public function index(Request $request) 
    {
        dd($_SERVER['REMOTE_ADDR']);
        $data = $request->has('data') ? $request->data : '1*(2+3)/2';
        dump('INFIX');
        $res = $this->toInfix($data);
        foreach($res as $item)
            echo $item;

        dump('POSTFIX');
        $res = $this->postFix($res);   
        foreach($res as $item)
            echo $item;

        //$res = $this->calculate($res);

        dump($res);
    }











    public function scan(ZipArchive $zip, string $path)
    {
        $dir = opendir($path);

        while($file = readdir($dir))
        {
            $current = $path.'/'.$file;

            if($file == '.' || $file == '..')
                continue;

            if(is_file($current))
                $zip->addFile($current, $current);
            elseif(is_dir($current))
                $this->scan($zip, $path.'/'.$file);
        }
    }



    public function test($id = 0)
    {
        // $client = \App\Models\Client::find($id);
        // $dnmClient = DNMClientService::saveClient($client);

        // $worksheet = Worksheet::find(5);
        // $dnmWorksheet = DNMWorksheetService::setWorksheet($worksheet);

        // $dnmWorksheet->setAppeal();
        // $dnmWorksheet->event->reject();

        //$reserve = \App\Models\WsmReserveNewCar::find(72);
        //dd($reserve);
        //ClientCreateOrUpdateEvent::dispatch($reserve->worksheet->client);
        //$reserve->refresh();
        //WorksheetCreateEvent::dispatch($reserve->worksheet);
        // $reserve->refresh();
        //ReserveCreateEvent::dispatch($reserve);
        // $reserve->refresh();
        //DNMVisitEvent::dispatch($reserve, 'internet');
        //DNMVisitEvent::dispatch($reserve, 'visit');
        //DNMVisitEvent::dispatch($reserve, 'call');
        //DNMVisitEvent::dispatch($reserve, 'visit');
        //DNMVisitEvent::dispatch($reserve, 'reject');
        //DNMVisitEvent::dispatch($reserve, 'testdrive');
        
        //DNMVisitEvent::dispatch($reserve, 'offer');
        //ClientCreateOrUpdateEvent::dispatch($reserve->worksheet->client);
        
        // ClientCreateOrUpdateEvent::dispatch($reserve->worksheet->client);

        // WorksheetCreateEvent::dispatch($reserve->worksheet);

        // ReserveCreateEvent::dispatch($reserve);
        
        // DNMVisitEvent::dispatch($reserve, 'visit');
        
        // DNMVisitEvent::dispatch($reserve, 'contract');
        
        // DNMVisitEvent::dispatch($reserve, 'reject');
    }



    public function rr()
    {
        $str = '1+2*5';
        $length = strlen($str);
        $val = '';
        $mas = [];

        for ($i = 0; $i < $length; $i++) {

            if (isset($str[$i])) {
                if (is_numeric($str[$i])) {
                    $val .= $str[$i];
                } else {
                    if ($val)
                        $mas[] = $val;
                    $mas[] = $str[$i];
                    $val = '';
                }

                if ($i == $length - 1 && $val) {
                    $mas[] = $val;
                }
            }
        }

        $valStack = [];
        $keyStack = [];
        foreach ($mas as $item) {
            if (is_numeric($item))
                array_push($valStack, $item);
            else {
                if (count($keyStack) == 0)
                    array_push($keyStack, $item);
                else {
                    if ($this->isSymbol($item)) {
                        $currentPriority = $this->getPriority($item);
                        foreach ($keyStack as $key)
                            if ($currentPriority >= $this->getPriority($key))
                                $valStack[] = 1;
                    }
                }
            }
        }
    }


    public function isSymbol($val)
    {
        $mas = ['+', '-', '*', '/'];

        if (array_search($val, $mas))
            return 1;
        return 0;
    }


    public function getPriority($key)
    {
        $priorityMas = [
            '(' => 0,
            ')' => 1,
            '+' => 2,
            '-' => 2,
            '*' => 3,
            '/' => 3,
        ];

        $keyPriority = $priorityMas[$key];

        return $keyPriority;
    }



    private function isAdditionOrSubtraction($val)
    {
        return ($val == '+' || $val == '-') ? 1 : 0;
    }



    public function getCMEDealerID()
    {
        $token = \App\Classes\SMExpert\Token::getInstance()->getToken();

        $url = 'https://lk.cm.expert/api/v1/dealers';

        $response = Http::withHeaders([
            'Authorization' => $token
        ])->get($url);

        dd($response->json());
    }

    public function addYear($date, $year = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$year year", strtotime($date)));
    }

    public function addMonth($date, $month = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$month month", strtotime($date)));
    }

    public function addDay($date, $day = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$day day", strtotime($date)));
    }

    public function addWeek($date, $week = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$week week", strtotime($date)));
    }
}
