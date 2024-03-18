<?php

namespace App\Classes\Telegram\Notice;

use \App\Models\ClientEventStatus;

Class TraficNotice extends AbstractNotice
{
    public $message = '';

    /**
     * MESSAGE ON WAITING
     */
    public function waiting()
    {
        $user = $this->model->author ? $this->model->author->cut_name : 'Система';

        $arr[] = self::boldStr('Обращение №'.$this->model->id).'.';
        $arr[] = 'Клиент: '.$this->model->client_name;
        $arr[] = $user.': Регистрирую новое обращение.';
        $arr[] = $this->model->appeal->name.' ('.$this->model->salon->name.')';
        $arr[] = self::italicStr('Обработайте ожидающий трафик.');

        $resultStr = implode("\n", $arr);

        $this->message = $resultStr;
    }



    /**
     * MESSAGE ON REPORT
     */
    public function assign()
    {
        $user = $this->model->author ? $this->model->author->cut_name : 'Система';

        $arr[] = self::boldStr('Обращение №'.$this->model->id).'.';
        $arr[] = 'Клиент: '.$this->model->client_name;
        $arr[] = $user.': Назначаю Вас ответственным.';
        $arr[] = $this->model->appeal->name.' ('.$this->model->salon->name.')';
        $arr[] = self::italicStr('Обработайте назначенный трафик.');

        $resultStr = implode("\n", $arr);

        $this->message = $resultStr;
    }



    /**
     * MESSAGE ON CLOSE
     */
    public function confirm()
    {
        $user = $this->model->author ? $this->model->author->cut_name : 'Система';

        $arr[] = self::boldStr('Обращение №'.$this->model->id).'.';
        $arr[] = 'Клиент: '.$this->model->client_name;
        $arr[] = $this->model->status->description.' '.date('d.m.Y (H:i)');
        $arr[] = self::italicStr('Теперь Вы можете назначить новое обращение на сотрудника.');

        $resultStr = implode("\n", $arr);

        $this->message = $resultStr;
    }
}
