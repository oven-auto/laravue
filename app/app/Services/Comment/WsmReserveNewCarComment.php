<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;
use App\Models\WsmReserveComment;

Class WsmReserveNewCarComment extends AbstractComment
{
    public function __construct(WsmReserveComment $redemption)
    {
        $this->data = [
            'author_id' => auth()->user()->id,
            'redemption_car_id' => $redemption->id,
        ];
    }



    public function store(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => 'Создан новый резерв.',
            'type' => 1,
        ]);
    }
}