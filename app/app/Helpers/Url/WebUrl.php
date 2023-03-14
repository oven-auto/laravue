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

    public static function plugCar()
    {
        return asset('/images/somecar.png');
    }

    public static function plugCarOrImage($val)
    {
        $var = self::make_link($val);
        if($var)
            return $var;
        return self::plugCar();
    }
}
