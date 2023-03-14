<?php

namespace App\Helpers\Url;

Class WebUrl
{
    public static function make_link($val)
    {
        if(file_exists(public_path('storage/'.$val)))
    	    return asset('storage'.$val) . '?' . date('dmyhm');
    }
}
