<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\ClientUnion;
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
        $mas = [];
        $unions = ClientUnion::get();
        foreach($unions as $item)
            foreach($unions as $sub)
                if($item->client_id == $sub->parent && $item->parent == $sub->client_id)
                    $mas[] = ['client_id' => $sub->client_id, 'parent' => $sub->parent];
                    //ClientUnion::where('client_id',$sub->client)->where('parent', $sub->parent)->delete();
        echo 1;
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
