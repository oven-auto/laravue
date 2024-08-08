<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;

use App\Classes\LadaDNM\DNM;
use App\Classes\LadaDNM\DNMClient;
use App\Classes\LadaDNM\DNMClientService;
use App\Classes\LadaDNM\DNMFactory;
use App\Classes\LadaDNM\DNMWorksheet;
use App\Classes\LadaDNM\DNMWorksheetService;
use App\Classes\Vin\Vin;
use App\Helpers\String\StringHelper;
use App\Models\Client as ModelsClient;
use App\Models\ClientUnion;
use App\Models\MarkAlias;
use App\Models\Trafic;
use App\Models\Worksheet;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }



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
    public function bot(Request $request)
    {
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $events = \App\Models\ClientEvent::with(['lastStatus', 'executors'])->get();

        $events->each(function ($item) {
            $item->lastStatus->executors()->attach($item->executors->pluck('id')->toArray());
        });
    }



    public function test($id = 0)
    {
        // $client = \App\Models\Client::find($id);
        // $dnmClient = DNMClientService::saveClient($client);

        // $worksheet = Worksheet::find(5);
        // $dnmWorksheet = DNMWorksheetService::setWorksheet($worksheet);

        // $dnmWorksheet->setAppeal();
        // $dnmWorksheet->event->reject();
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
