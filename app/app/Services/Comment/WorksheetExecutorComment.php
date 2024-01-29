<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;
use App\Models\USer;

Class WorksheetExecutorComment extends AbstractComment
{
    public function __construct(CommentInterface $model)
    {
        $action_id = \DB::table('worksheet_actions')
            ->select('id')
            ->where('worksheet_id', $model->worksheet_id)
            ->orderBy('worksheet_actions.id', 'DESC')
            ->limit(1)
            ->get()
            ->first()
            ->id;

        $this->data = [
            'author_id' => auth()->user()->id,
            'action_id' => $action_id,
            'type' => 0,
        ];
    }



    public function attach(CommentInterface $model)
    {
        $user = User::find($model->user_id);
        return array_merge($this->data, [
            'text' => 'Добавлен ответственный '.$user->cut_name,
            'type' => 1
        ]);
    }



    public function detach(CommentInterface $model)
    {
        $user = User::find($model->user_id);
        return array_merge($this->data, [
            'text' => 'Удален ответственный '.$user->cut_name,
            'type' => 1
        ]);
    }



    public function report(CommentInterface $model)
    {
        $user = User::find($model->user_id);
        return array_merge($this->data, [
            'text' => 'Пользователь отчитался за рабочий лист '.$user->cut_name,
            'type' => 1
        ]);
    }



    public function deport(CommentInterface $model)
    {
        $user = User::find($model->user_id);
        return array_merge($this->data, [
            'text' => 'Отчет пользователя '.$user->cut_name. ' отменен',
            'type' => 1
        ]);
    }
}
