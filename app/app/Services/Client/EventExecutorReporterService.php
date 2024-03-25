<?php

namespace App\Services\Client;

use App\Helpers\Array\ArrayHelper;
use App\Models\ClientEventStatus;
use App\Classes\Telegram\Notice\TelegramNotice;
use App\Exceptions\Client\EventExcecutorAppendException;
use App\Models\User;
use App\Services\Comment\EventComment;
use App\Exceptions\Client\EventExcecutorDetachException;
use App\Exceptions\Client\EventReporterAttachException;
use App\Exceptions\Client\EventReporterIsAuthorException;
use App\Exceptions\Client\EventReporterNotException;

Class EventExecutorReporterService
{
    /**
     * ДОБАВИТЬ ИСПОЛНИТЕЛЕЙ В СОБЫТИЕ
     * @param ClientEventStatus $event
     * @param array|int $users
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function append(ClientEventStatus $event, array|int $users = null) : \Illuminate\Database\Eloquent\Collection
    {
        //Check request users on empty
        $users ?? throw new EventExcecutorAppendException();
        //if users int change on array
        $users = is_numeric($users) ? [$users] : $users;
        //uniq users to append
        $neededUserId = ArrayHelper::except($users, $event->executors->pluck('id')->toArray());
        //append users to event
        $event->executors()->attach($neededUserId);
        //send telegram notice
        TelegramNotice::run($event)->executor()->send(ArrayHelper::except($neededUserId, $event->event->author_id));
        //collect executors
        $executors = User::whereIn('id', $neededUserId)->get();
        //make comment
        EventComment::addUsers($event, $executors);

        return $executors;
    }



    /**
     * УДАЛИТЬ ИСПОЛНИТЕЛЕЙ В СОБЫТИЕ
     * @param ClientEventStatus $event
     * @param int $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function detach(ClientEventStatus $event, int $user) : \Illuminate\Database\Eloquent\Collection
    {
        //Ошибка если пользователь автор
        $event->event->author_id != $user ?: throw new EventExcecutorDetachException();
        //Убрать из списка отчитавшихся
        $event->executors()->detach($user);
        //Получить пользователя
        $users = User::where('id', $user)->get();
        //Отправить коммент
        $users->each(function($item) use ($event) {
            EventComment::delUser($event, $item);
        });

        return $users;
    }



    /**
     * ОТЧИТАТЬСЯ ЗА СОБЫТИЕ
     * @param ClientEventStatus $event
     * @param int $user
     * @return User
     */
    public function report(ClientEventStatus $event, int $user) : User
    {
        //Ошибка если уже отчитан
        !$event->reporters->contains('id', $user) ?: throw new EventReporterAttachException();
        //Ошибка если автор
        $event->event->author_id != $user ?: throw new EventReporterIsAuthorException();
        //Цепляем к отчитавшимся за событие
        $event->reporters()->attach($user);
        //отцепляем от списка исполнителей
        $this->detach($event, $user);
        //Получаем модель пользователя
        $user = User::find($user);
        //Отправляем коммент
        EventComment::reportlUser($event, $user);
        //Отправляем уведомление автору события
        TelegramNotice::run($event)->report()->send([$event->event->author_id]);

        return $user;
    }



    /**
     * ОТМЕНА ОТЧЕТА ЗА СОБЫТИЕ
     * @param ClientEventStatus $event
     * @param int $user
     * @return User
     */
    public function deport(ClientEventStatus $event, int $user) : User
    {
        //Ошибка если нет в списке отчитавшихся
        $event->reporters->contains('id', $user) ?: throw new EventReporterNotException();
        //Отцепляем от списка отчитавшихся
        $event->reporters()->detach($user);
        //Переносим в список  исполнителей
        $this->append($event, $user);
        //Получаем модель пользователя
        $user = User::findOrFail($user);
        //Отправляем коммент
        EventComment::deportUser($event, $user);

        return $user;
    }
}
