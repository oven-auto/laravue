<?php

namespace App\Helpers\Url;

Class WebUrl
{
    public static function make_link($val)
    {
        if(file_exists(public_path('storage/'.$val)))
    	    return asset('storage'.$val) . '?' . date('dmyhm');
        return false;
    }

    public function plugCar()
    {
        return asset('/images/somecar.png');
    }

    public static function plugCarOrImage($val)
    {
        return self::make_link($val) ?? self::plugCar();
    }
}
