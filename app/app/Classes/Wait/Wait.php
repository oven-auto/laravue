<?php

namespace App\Classes\Wait;

use App\Models\DealerColorImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Class Wait
{
    public static function setWaitColor()
    {
        $count = DealerColorImage::select(DB::raw('count(id) as count'))->first()->toArray()['count'];
        $sleep = 0;
        if($count > 0 && $count < 3)
            $sleep = 1;
        elseif($count > 3 && $count < 6)
            $sleep = 3;
        elseif($count > 6 && $count < 9)
            $sleep = 4;
        elseif($count > 9 && $count < 12)
            $sleep = 5;
        elseif($count > 12 && $count < 15)
            $sleep = 6;
        elseif($count > 15 && $count < 18)
            $sleep = 7;
        elseif($count > 15)
            $sleep = 8;

        sleep($sleep);
        Log::alert('Внимание Большая Задержка'. $sleep);
    }
}