<?php

namespace App\Classes\Telegram\Notice;

use \App\Models\ClientEventStatus;

Class ClientEventStatusNotice extends AbstractNotice
{
    public $message = '';

    public function executor()
    {
        $user = auth()->user() ? auth()->user()->cut_name : 'Система';

        $arr[] = self::boldStr('Коммуникация №'.$this->model->id).'.';
        $arr[] = 'Клиент: '.$this->model->event->client->full_name;
        $arr[] = $user.': Добавляю Вас в качестве участника.';
        $arr[] = 'Тема: '.$this->model->event->title.'.';
        $arr[] = 'Категория: '.$this->model->event->group->name;
        $arr[] = 'Начало '.$this->model->date_at->format('d.m.Y').' ('.$this->model->begin.')';
        $arr[] = self::italicStr('Теперь Вы отслеживаете это событие.');

        $resultStr = implode("\n", $arr);

        $this->message = $resultStr;
    }
}
