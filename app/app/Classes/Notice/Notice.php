<?php

namespace App\Classes\Notice;

class Notice
{
    private static $init;

    private static $messages = [];

    private function __construct() {}



    public static function getInstance()
    {
        if (is_null(self::$init)) {

            self::$init = new self();
        }

        return self::$init;
    }



    public static function setMessage($message)
    {
        array_push(self::$messages, $message);
    }



    public static function getMessages()
    {
        return implode('\\n\\r', self::$messages);
    }
}
