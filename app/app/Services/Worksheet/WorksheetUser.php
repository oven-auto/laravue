<?php

namespace App\Services\Worksheet;
use App\Models\User;
use App\Models\Worksheet;
use App\Models\WorksheetExecutor;
use Illuminate\Support\Collection;
use App\Services\Comment\Comment;

/**
 * Класс для добавления/удаления ответственных лиц из РЛ
 */
Class WorksheetUser
{
    /**
    * Добавить ответственных лиц в РЛ
    * @param Worksheet $worksheet РЛ в который хотим добавить ответственных лицо
    * @param Collection $users Пользователи которых хотим сделать ответственными лицами
    */
    public static function attach(Worksheet $worksheet, Collection $users) : void
    {
        $currentUsers = $worksheet->executors;

        $currentUsers->push($worksheet->author);

        foreach($users as $item)
            if(!$currentUsers->contains('id', $item->id))
            {
                $worksheet->executors()->attach($item);
                Comment::add(self::commentModel($worksheet, $item), 'attach');
            }
    }

    /**
    * Удалить ответственного из РЛ
    * @param Worksheet $worksheet РЛ в который хотим удаить ответственное лицо
    * @param User $users Пользователь которого хотим удалить
    */
    public static function detach(Worksheet $worksheet, User $user) : void
    {
        Comment::add(self::commentModel($worksheet, $user), 'detach');

        $worksheet->executors()->detach($user->id);
    }

    private static function commentModel($worksheet, $user) : WorksheetExecutor
    {
        $executor = new WorksheetExecutor();

        $executor->fill(['worksheet_id' => $worksheet->id, 'user_id' => $user->id]);

        return $executor;
    }
}
