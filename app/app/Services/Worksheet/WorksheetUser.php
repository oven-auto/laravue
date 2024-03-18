<?php

namespace App\Services\Worksheet;
use App\Models\User;
use App\Models\Worksheet;
use App\Models\WorksheetExecutor;
use Illuminate\Support\Collection;
use App\Services\Comment\Comment;
use App\Classes\Telegram\Notice\TelegramNotice;

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
    public static function attach(Worksheet $worksheet, Collection $users, $comment = true) : void
    {
        $currentUsers = $worksheet->executors;

        $currentUsers->push($worksheet->author);

        $filtered = [];

        foreach($users as $item)
            if(!$currentUsers->contains('id', $item->id) && !$worksheet->reporters->contains('id', $item->id))
                $filtered[] = $item->id;

        $comment ? Comment::add(self::commentModel($worksheet, new User()), 'attach',  $users) : '';

        $worksheet->attachMethod('executors', $filtered);

        $mas = [];
        foreach($filtered as $item)
            if($item != auth()->user()->id)
                $mas[] = $item;

        TelegramNotice::run($worksheet)->executor()->send($mas);
    }

    /**
    * Удалить ответственного из РЛ
    * @param Worksheet $worksheet РЛ в который хотим удаить ответственное лицо
    * @param User $users Пользователь которого хотим удалить
    */
    public static function detach(Worksheet $worksheet, User $user) : void
    {
        //detach user from executor list
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

        $worksheet->attachMethod('reporters', [$user->id]);

        TelegramNotice::run($worksheet)->report()->send([$worksheet->author_id]);

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
        //delete user from reporter list
        $worksheet->reporters()->detach($user->id);
        //update reporters
        $worksheet->load('reporters');
        //make comment
        Comment::add(self::commentModel($worksheet, $user), 'deport');
        //append user to executor list
        self::attach($worksheet, collect([$user]), false);
    }



    private static function commentModel($worksheet, $user) : WorksheetExecutor
    {
        $executor = new WorksheetExecutor();

        $executor->fill(['worksheet_id' => $worksheet->id, 'user_id' => $user->id]);

        return $executor;
    }
}
