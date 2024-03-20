<?php

namespace App\Classes\Telegram\Scenes;

use App\Classes\Telegram\AbstractScene;
use App\Classes\Telegram\SceneInterface;
use App\Classes\Telegram\Traits\ConstantToFunction;

Class RegisterScene extends AbstractScene implements SceneInterface
{
    use ConstantToFunction;

    public  $options = [
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ];

    public  $answer = [
        0 => "Введите свой номер телефона (11 цифр), и тогда CRM Сопка сможет отправлять тебе уведомления.",
        1 => "Отлично, осталось совсем чуть чуть, отправь мне 4-значный код из CRM Сопка, что бы я точно знал что это ты.",
        2 => "Ну вот и все, я тебя запомнил, и теперь смогу присылать тебе уведомления из CRM Сопка.",
        'error' => "Ты зарегестрирован. Если хочешь, что бы я ещё, что-то сделал, то введи команду. Список команд можно узнать введя /help",
    ];

    public  $storageState = [
        1 => 'phone',
        2 => 'code',
    ];

    public  $rules = [
        'phone' => 'length:11|numeric|exist:users.phone',
        'code' => 'length:4|numeric|exist:users.tg_token',
    ];

    private  $stateCount = 2;

    public function bysinesLogic()
    {
        $phone = $this->getStorage('phone');

        $user = \App\Models\User::where('phone', 'LIKE', '%'.substr($phone,1).'%')->first();

        $user->telegram_connection_id = $this->connection->id;

        $user->save();
    }
}
