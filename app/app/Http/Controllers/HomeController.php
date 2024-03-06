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

        $command = 'curl -k -F "url='.$url.'" -F "certificate=@cert/cert2.pem" "https://api.telegram.org/bot'.$token.'/setWebhook"';

        exec($command, $out, $res);

        dump($out);
    }



    /**
     * УДАЛИТЬ ВЕБХУК ТЕЛЕГРАМ
     */
    public function del()
    {
        $token = env('TELEGRAM_KEY');

        $telegram = new Api($token);

        $res = $telegram->removeWebhook();
    }



    /**
     * БОТ ТЕЛЕГРАМ
     */
    public function bot()
    {
        $offset = \App\Models\TelegramLongpollingOffset::firstOrNew();

        $token = env('TELEGRAM_KEY');

        $telegram = new Api($token);

        $data = $telegram->getUpdates([
            'offset' => $offset->value ?? '',
        ]);

        if(end($data))
        {
            $newOffset = end($data)->update_id + 1;
            $offset->value = $newOffset;
            $offset->save();
        }

        if(count($data))
        {
            foreach($data as $item)
            {
                $text = $item->message->text;

                switch ($text) {
                    case '/start' :
                        $res = $telegram->sendMessage([
                            'chat_id' => $item->message->chat->id,
                            'text' => "Привет я бот ОвенАвто. \n\n".
                                "Выбери нужную команду: \n\n".
                                "/start = приветствие \n".
                                "/register = регистрация \n"
                            ,
                            'parse_mode' => 'HTML',
                            'disable_web_page_preview' => true,
                        ]);
                        break;
                    case '/help':
                        $res = $telegram->sendMessage([
                            'chat_id' => $item->message->chat->id,
                            'text' =>
                                "Выбери нужную команду: \n\n".
                                "/start = приветствие \n".
                                "/register = регистрация \n"
                            ,
                            'parse_mode' => 'HTML',
                            'disable_web_page_preview' => true,
                        ]);
                        break;
                    case '/register' :

                        $res = $telegram->sendMessage([
                            'chat_id' => $item->message->chat->id,
                            'text' => "Введите свой номер телефона (11 символов), и тогда CRM Сопка сможет отправлять Вам уведомления",
                            'parse_mode' => 'HTML',
                            'disable_web_page_preview' => true,
                        ]);
                        break;
                    default:
                        $res = $telegram->sendMessage([
                            'chat_id' => $item->message->chat->id,
                            'text' => "Не флуди! А то забаню, и удалю из всех рабочих листов. \nНе знаешь, что спросить напиши /help",
                            'parse_mode' => 'HTML',
                            'disable_web_page_preview' => true,
                        ]);
                }
            }
        }
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
