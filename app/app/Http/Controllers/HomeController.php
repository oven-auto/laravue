<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;

use App\Classes\Vin\Vin;
use App\Models\ClientUnion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Http;
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



    public function get()
    {
        $token = env('TELEGRAM_KEY');
        $telegram = new Api($token);
        $res = $telegram->getWebhookInfo();
        dump(date('d-m-Y H:i'));
        dump($res);
    }



    public function set()
    {
        $token = env('TELEGRAM_KEY');
        $telegram = new Api($token);

        $res = $telegram->setWebhook([
            'certificate' => 'cert/sert1.pem',
            'url' => 'https://62.182.31.140/test'
        ]);

        dump($res);
    }


    public function bot()
    {
        dump('tg-bot');
        $tmpdata = json_decode(file_get_contents('php://input'),true);
        $arrdataapi = print_r($tmpdata, true);
        file_put_contents('apidata.txt', 'Данные от бота: $arrdataapi', FILE_APPEND);
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

    }

    public function test()
    {

        // $user = \App\Models\User::find(47);
        // $user->name = 1;
        // $user->save();

        // $token = env('TELEGRAM_KEY');
        // $telegram = new Api($token);
        // $result = $telegram->getWebhookUpdates();

        // $text = $result["message"]["text"]; //Текст сообщения
        // $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
        // $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
        // $keyboard = [["Последние статьи"],["Картинка"],["Гифка"]]; //Клавиатура

        // if($text){
        //     if ($text == "/start") {
        //        $reply = "Добро пожаловать в бота!";
        //        $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        //        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        //    }
        // }
        // else{
        //     $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
        // }
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
