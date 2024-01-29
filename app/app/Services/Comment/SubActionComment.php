<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;
use App\Models\SubAction;
use App\Models\User;

Class SubActionComment extends AbstractComment
{
    public function __construct(SubAction $subAction)
    {
        $this->data = [
            'author_id' => auth()->user()->id,
            'sub_action_id' => $subAction->id,
        ];
    }



    public function create(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => $model->title,
            'type' => 0,
        ]);
    }



    public function close(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => 'Завершена.',
            'type' => 0
        ]);
    }



    public function append_executors(CommentInterface $model, array $data = [])
    {
        if(isset($data['executors']))
            $users = User::whereIn('id', $data['executors'])->get()->map(fn($item) => $item->cut_name)->toArray();
            return array_merge($this->data, [
                'text' => 'Добавлены новые участники: '.join(' ', $users),
                'type' => 1,
            ]);
    }



    public function detach_executor(CommentInterface $model, array $data = [])
    {
        if(isset($data['executor']))
        {
            $user = User::find($data['executor']);
            if($user)
                return array_merge($this->data, [
                    'text' => 'Удален участник: '.$user->cut_name,
                    'type' => 1,
                ]);
        }
    }



    public function append_report(CommentInterface $model, array $data = [])
    {
        if(isset($data['reporter']))
        {
            $user = User::find($data['reporter']);
            if($user)
                return array_merge($this->data, [
                    'text' => 'Сотрудник отчитался и больше не отслеживаю задачу',
                    'type' => 0,
                ]);
        }
    }

    public function detach_report(CommentInterface $model, array $data = [])
    {
        if(isset($data['reporter']))
        {
            $user = User::find($data['reporter']);
            if($user)
                return array_merge($this->data, [
                    'text' => 'Отчет сотрудника '.$user->cut_name.' по задаче '.$model->id.' отменен',
                    'type' => 0
                ]);
        }
    }
}
