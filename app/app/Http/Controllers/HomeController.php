<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\ClientUnion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Cache\Store;

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
        $img = imagecreate(2480,3508);

        $degree = 0; // Угол поворота текста в градусах
        $y = 100; // Смещение сверху (координата y)
        $x = 200;

        $background_color = imagecolorallocate($img, 255, 255, 255);
        $font = public_path().'/fonts/dejavu-sans/DejaVuSans.ttf';

        $font_size = 24;
        $text = "ТЕСТ РУССКОГО";
        $color = imagecolorallocate($img, 0, 0, 0);
        imagettftext($img, $font_size, $degree, $x, $y, $color, $font, $text);

        $font_size = 50;
        $degree = 0; // Угол поворота текста в градусах
        $y = 500; // Смещение сверху (координата y)
        $x = 200;
        $text = "ТЕСТ СТРОКИ 22222222222222222222222222222222222222222222222222
        22222222222222222222222222222222222222222
        222222222222222222222222222222222222222222222222222222222222
        22222222222222222222222222222222222222";
        $color = imagecolorallocate($img, 150, 0, 0);
        imagettftext($img, $font_size, $degree, $x, $y, $color, $font, $text);

        imagearc($img, 100, 100, 200, 200,  0, 360, $color);

        imagepng($img, public_path()."/".time().".png");

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
