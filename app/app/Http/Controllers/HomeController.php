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
        $phone = '79091231396';
        $phone = sprintf("+%s (%s) %s-%s-%s",
            substr($phone, 0, 1),
            substr($phone, 1, 3),
            substr($phone, 4, 3),
            substr($phone, 7, 2),
            substr($phone, 9)
        );
        echo $phone;
    }

}
