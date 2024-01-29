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
        //users from worksheet
        $currentUsers = $worksheet->executors;
        //append author to users
        $currentUsers->push($worksheet->author);
        //append to users people from subaction
        $currentUsers = $currentUsers->merge($users);
        //delete dublicate from users collection
        $currentUsers = $currentUsers->unique();
        //
        $filtered = $currentUsers->reject(fn($item) => $worksheet->reporters->contains('id', $item->id));

        $worksheet->executors()->sync($filtered);
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



    /**
     * ОТЧИТАТЬСЯ ЗА РЛ
     * @param int|Worksheet $worksheet
     * @param int $user
     * @return void
     */
    public function report(int|Worksheet $worksheet) : void
    {
        $user = auth()->user();

        if(is_numeric($worksheet))
            $worksheet = Worksheet::findOrFail($worksheet);

        if($user->id === $worksheet->author_id)
            throw new \Exception('Вы автор рабочего листа, и не имеете возможности отчитываться');

        if($worksheet->reporters->contains('id', $user->id))
            throw new \Exception('Вы уже находитесь в списке отчитавшихся этого рабочего листа');

        if(!$worksheet->executors->contains('id', $user->id))
            throw new \Exception('Вы не можете отчитаться за рабочий лист, тк не являетесь его участником');

        $worksheet->reporters()->attach($user->id);

        Comment::add(self::commentModel($worksheet, $user), 'report');

        self::detach($worksheet, $user);
    }



    /**
     * СНЯТЬ ОТЧЕТ ПОЛЬЗОВАТЕЛЯ С РАБОЧЕГО ЛИСТА
     * @param int|Worksheet $worksheet
     * @param int|User $user
     * @return void
     */
    public function deport(int|Worksheet $worksheet, int|User $user) : void
    {
        if(is_numeric($worksheet))
            $worksheet = Worksheet::findOrFail($worksheet);

        if(is_numeric($user))
            $user = User::findOrFail($user);

        if(!$worksheet->reporters->contains('id', $user->id))
            throw new \Exception('Пользователя нет в списке отчитавшихся');

        $worksheet->reporters()->detach($user->id);

        Comment::add(self::commentModel($worksheet, $user), 'deport');

        self::attach($worksheet, collect([$user]));
    }



    private static function commentModel($worksheet, $user) : WorksheetExecutor
    {
        $executor = new WorksheetExecutor();

        $executor->fill(['worksheet_id' => $worksheet->id, 'user_id' => $user->id]);

        return $executor;
    }
}
