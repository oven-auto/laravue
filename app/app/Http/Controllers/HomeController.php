<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

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
        // $phone = '79091231396';
        // $phone = sprintf("+%s (%s) %s-%s-%s",
        //     substr($phone, 0, 1),
        //     substr($phone, 1, 3),
        //     substr($phone, 4, 3),
        //     substr($phone, 7, 2),
        //     substr($phone, 9)
        // );
        // echo $phone;

        $date = date('Y-m-d H:i');
        echo("Current date: $date".PHP_EOL);
        echo '<br>';
        echo("+ 1 Day: ".$this->addDay($date));
        echo '<br>';
        echo("+ 1 Week: ".$this->addWeek($date));
        echo '<br>';
        echo("+ 1 Month: ".$this->addMonth($date));
        echo '<br>';
        echo("+ 1 Year: ".$this->addYear($date));

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
