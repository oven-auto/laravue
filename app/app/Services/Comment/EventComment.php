<?php

namespace App\Services\Comment;

use App\Models\ClientEventLink;
use App\Models\Interfaces\CommentInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

Class EventComment extends CommentService
{
    public static function addUsers(CommentInterface $model, Collection $users)
    {
        self::customMessage($model, 'Добавлены новые участники события '. implode(', ', $users->map(function($item){
            return $item->cut_name;
        })->toArray()));
    }

    public static function delUser(CommentInterface $model, User $user)
    {
        self::customMessage($model, 'Удален участник события '.$user->cut_name);
    }

    public static function reportlUser(CommentInterface $model, User $user)
    {
        self::customMessage($model, 'Сотрудник отчитался и больше не является участником события');
    }

    public static function deportUser(CommentInterface $model, User $user)
    {
        if($user->id == $model->event->author_id)
            self::customMessage($model, 'Сотрудник '.$user->cut_name.' исключен из отчитавшихся и добавлен в участники события.');
        else
            self::customMessage($model, 'Отчет об исполнении события отозван.');
    }

    public static function appendFile(CommentInterface $model)
    {
        self::customMessage($model, 'Добавлены фаилы');
    }

    public static function deleteFile(CommentInterface $model)
    {
        self::customMessage($model, 'Удалены фаилы');
    }

    public static function appendLink(CommentInterface $model, ClientEventLink $link)
    {
        self::customMessage($model, 'Добавлена ссылка');
    }

    public static function deleteLink(CommentInterface $model, ClientEventLink $link)
    {
        self::customMessage($model, 'Удалена ссылка ');
    }
}
