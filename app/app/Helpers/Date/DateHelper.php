<?php

namespace App\Helpers\Date;

Class DateHelper
{
    public static function addYear($date, $year = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$year year", strtotime($date)));
    }

    public static function addMonth($date, $month = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$month month", strtotime($date)));
    }

    public static function addDay($date, $day = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$day day", strtotime($date)));
    }

    public static function addWeek($date, $week = 1)
    {
        return date('Y-m-d H:i:s', strtotime("+$week week", strtotime($date)));
    }

    public static function format($date, $format = 'd.m.Y')
    {
        return date($format, strtotime($date));
    }

    public static function russianMonth($index)
    {
        $arr = ['янв', 'февр', 'март', 'апр.', 'май', 'июнь', 'июль', 'авг', 'сент', 'окт', 'нояб', 'дек'];
        return $arr[$index];
    }
}
