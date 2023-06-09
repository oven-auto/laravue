<?php

namespace App\Services\Worksheet;
use App\Models\User;
use App\Models\Worksheet;
use App\Models\WorksheetExecutor;

Class WorksheetUser
{
    private static function getExecutorsWorksheet(int $worksheet_id)
    {
        $users = User::select('users.*')
            ->leftJoin('worksheet_executors', 'worksheet_executors.user_id', 'users.id')
            ->where('worksheet_executors.worksheet_id', $worksheet_id)
            ->get();

        return $users;
    }

    public static function attach(int $worksheet_id, array $user_ids)
    {

        $worksheet = Worksheet::find($worksheet_id);

        $currentUsers = WorksheetExecutor::where('worksheet_id', $worksheet_id)->pluck('user_id')->toArray();
        array_push($currentUsers, $worksheet->author_id);

        foreach($user_ids as $item)
            if(!in_array($item,$currentUsers))
            {
                WorksheetExecutor::create([
                    'worksheet_id' => $worksheet_id,
                    'user_id' => $item
                ]);
                $user = User::find($item);
                Comment::commentAppendClient($worksheet_id, $user, 'Добавлен ответственный');
            }

        $users = self::getExecutorsWorksheet($worksheet_id);

        return $users;
    }

    public static function detach(int $worksheet_id, int $user_id)
    {
        $isIn = WorksheetExecutor::where('worksheet_id', $worksheet_id)
            ->where('user_id', $user_id)
            ->count();

        if($isIn)
        {
            WorksheetExecutor::where('worksheet_id', $worksheet_id)
                ->where('user_id', $user_id)
                ->delete();
            $user = User::find($user_id);
            Comment::commentAppendClient($worksheet_id, $user, 'Удален ответственный');
        }
    }
}
