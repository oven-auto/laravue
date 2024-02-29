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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function test()
    {
dd(1);
        // $token = env('TELEGRAM_KEY');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        file_put_contents(__DIR__ . '/message.txt', print_r($data, true));
        //$bot = new \TelegramBot\Api\Client($token);

        // $bot->command('start', function ($message) use ($bot) {
        //     $answer = 'Добро пожаловать!';
        //     $bot->sendMessage($message->getChat()->getId(), $answer);
        // });

        //$bot->run();
        // Http::
        // $ch = curl_init();
        // curl_setopt_array(
        //     $ch,
        //     array(
        //         CURLOPT_URL => 'https://api.telegram.org/bot' . $token . "/sendMessage?chat_id={$chatId}&text=HELLO",
        //         //CURLOPT_POST => TRUE,
        //         CURLOPT_RETURNTRANSFER => TRUE,
        //         CURLOPT_TIMEOUT => 10,
        //         // CURLOPT_POSTFIELDS => array(
        //         //     'chat_id' => $chatId,
        //         //     'text' => 'HELLO',
        //         // ),
        //     )
        // );
        // curl_exec($ch);
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
