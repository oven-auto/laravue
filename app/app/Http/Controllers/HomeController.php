<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;

use App\Classes\LadaDNM\DNM;
use App\Classes\LadaDNM\DNMClient;
use App\Classes\LadaDNM\DNMFactory;
use App\Classes\LadaDNM\DNMWorksheet;
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



    public function test($id = 3632)
    {
        // $client = \App\Models\Client::find($id);
        // $dnm = new DNMClient($client);
        // $dnm->save();

        // $worksheet = Worksheet::find(7);
        // $worksheetDnm = new DNMWorksheet($worksheet);
        // $worksheetDnm->save();
        // Log::channel('potok')->error('test');
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
