<?php

namespace App\Classes\Telegram\Scenes;

use App\Classes\Telegram\AbstractScene;
use App\Classes\Telegram\SceneInterface;
use App\Classes\Telegram\Traits\ConstantToFunction;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

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

        $this->options['reply_markup'] = json_encode(array(
            'inline_keyboard' => array(
                array(
                    array(
                        'text' => 'мой трафик',
                        'callback_data' => '/mytrafic',
                    ),
                    array(
                        'text' => 'мои события',
                        'callback_data' => '/myevent',
                    ),
                    array(
                        'text' => 'мои рабочие листы',
                        'callback_data' => '/myworksheet',
                    ),
                )
            ),
        ));
        //$this->options['reply_parameters'] = '666';
        if($this->user)
            $this->telegram->sendMessage($this->chatId, $str, $this->options);
    }
}
