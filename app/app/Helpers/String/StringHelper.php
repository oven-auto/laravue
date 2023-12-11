<?php

namespace App\Helpers\String;

Class StringHelper
{
    public static function phoneMask($value)
    {
        $phone = '';

        if($value)
            $phone = sprintf("+%s (%s) %s-%s-%s",
                mb_substr($value, 0, 1),
                mb_substr($value, 1, 3),
                mb_substr($value, 4, 3),
                mb_substr($value, 7, 2),
                mb_substr($value, 9)
            );
        return $phone;
    }
}
