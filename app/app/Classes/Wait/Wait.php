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

        if($count > 0 && $count < 3)
            sleep(0);
        elseif($count > 3 && $count < 6)
            sleep(3);
        elseif($count > 6 && $count < 9)
            sleep(4);
        elseif($count > 9)
            sleep(5);
        Log::alert('Внимание Большая Задержка');
    }
}