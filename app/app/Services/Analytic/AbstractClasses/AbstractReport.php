<?php

namespace App\Services\Analytic\AbstractClasses;

use Carbon\Carbon;

abstract class AbstractReport
{
    abstract public function handle(array $intervals, array $data);

    public function convertInterval(array $interval)
    {
        $arr = [];

        foreach($interval as $item)
        {
            $date_1 = Carbon::createFromFormat('d.m.Y', $item[0])->format('Y-m-d');
            $date_2 = isset($item[1]) ? Carbon::createFromFormat('d.m.Y', $item[1])->format('Y-m-d') : $date_1;
            $arr[] = [$date_1, $date_2];
        }
        
        return $arr;
    }
}