<?php

namespace App\Classes\Telegram\Scenes;

use App\Classes\Telegram\AbstractScene;
use App\Classes\Telegram\SceneInterface;
use App\Classes\Telegram\Traits\ConstantToFunction;

Class HelpScene extends AbstractScene implements SceneInterface
{
    use ConstantToFunction;

    public $options = [
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ];

    public $answer = [
        0 => "<b>Выбери нужную команду:</b> \n\n".
            "/start = приветствие \n".
            "/register = регистрация \n",
        'error' => "Не флуди, а то забаню. Если хочешь спросить, спроси. Список команд можно узнать введя /help",
    ];

    private  $stateCount = 0;

    public function bysinesLogic()
    {
        $str[] = "<b>Журнал задач</b>\n\n";
        $str[] = "/mytrafic - мой трафик\n";
        $str[] = "/myevent - мои события\n";
        $str[] = "/myworksheet - мои рабочие листы\n";
        $str = join("", $str);
        if($this->user)
            $this->telegram->sendMessage($this->chatId, $str, $this->options);
    }
}
