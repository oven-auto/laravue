<?php

namespace App\Classes\Telegram;

Class Scenario
{
    public $telegram;

    public function __construct()
    {
        $this->telegram = Telegram::init();
    }

    private static function getNameSpace()
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getNamespaceName();
    }

    public function handler()
    {
        $messages = $this->telegram->getUpdates();

        foreach($messages as $item)
        {
            $text = $item->message->text;

            $scene = $this->sceneFactory($text, $item);

            if($scene)
                $scene->sendCommand();
        }
    }

    private function sceneFactory(string $string, $item)
    {
        $isCommand = strpos($string, '/');

        if($isCommand === 0)
        {
            $className = ucfirst(trim($string, '/'));

            $class = self::getNameSpace().'\\Scenes\\'.$className.'Scene';

            if(class_exists($class))
            {
                $this->saveCommand($item, $string);
                return new $class($item);
            }
            else
            {
                $this->saveCommand($item, '/error');
                return self::errorScene($item);
            }
        }
        elseif($isCommand === false)
        {
            $prevAction = \App\Models\TelegramConnection::where('user_id', $item->message->from->id)->first();

            if($prevAction && $prevAction->last_command)
            {
                $className = ucfirst(trim($prevAction->last_command, '/'));

                $class = self::getNameSpace().'\\Scenes\\'.$className.'Scene';

                if(class_exists($class))
                {
                    $prevAction->state++;
                    $prevAction->save();
                    return new $class($item);
                }
            }
        }

        $this->saveCommand($item, '/error');
        return self::errorScene($item);
    }

    private static function errorScene($item)
    {
        return new \App\Classes\Telegram\Scenes\ErrorScene($item);
    }

    public function saveCommand($item, $command)
    {
        $obj = \App\Models\TelegramConnection::updateOrCreate(['user_id' => $item->message->from->id],[
            'chat_id' => $item->message->chat->id,
            'user_id' => $item->message->from->id,
            'last_command' => $command,
            'state' => 0,
            'storage' => null
        ]);
    }
}
