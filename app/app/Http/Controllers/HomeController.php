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
use App\Models\Trafic;
use App\Models\Tuning;
use App\Models\Worksheet;
use App\Models\WsmReserveNewCar;
use App\Repositories\Car\Car\CarRepository;
use App\Services\Car\CalculatePaidDate;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Icewind\SMB\BasicAuth;
use Icewind\SMB\ServerFactory;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
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



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(HttpRequest $request) {
        // if($request->has('reserve_id'))
        // {
        //     $reserve = WsmReserveNewCar::withTrashed()->find($request->reserve_id);

        //     $action = $request->action ?? 'visit';

        //     (new DNMEvent())->handler($reserve, $action);
        // }

        $reserve = WsmReserveNewCar::find(4298);

        dd($reserve->worksheet->subclients->first());        

        // $serverFactory = new ServerFactory();

        // $auth = new BasicAuth('root', 'oa', 'Vesta2020');

        // $server = $serverFactory->createServer('192.168.1.6', $auth);
        
        // $shares = $server->listShares();

        // foreach($shares as $itemShare)
        // {

        //     dump('-------------'.$itemShare->getName());
        //     $content = $itemShare->dir('');
        //     foreach($content as $item)
        //         if($item->isDirectory())
        //             dump($item->getSize(). ' - '.$item->getName());
        //     dd();
        // }
        
        // $car = Car::find(26);

        // $paid = $car->paid_date;

        // dd($paid->date_at->format('d'));
        // $trafic = Trafic::query()->limit(10)->orderBy('id','DESC')->get();
        
        // $links = DB::table('trafic_links')
        //     ->select('trafic_links.trafic_id')
        //     ->whereIn('trafic_links.trafic_id', $trafic->pluck('id'))
        //     ->groupBy('trafic_links.trafic_id')
        //     ->get();

        // dd($links);
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
