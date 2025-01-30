<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;
use App\Models\WsmReserveNewCar;

Class WsmReserveNewCarComment extends AbstractComment
{
    public function __construct(WsmReserveNewCar $reserve)
    {
        $this->data = [
            'author_id' => auth()->user()->id,
            'reserve_id' => $reserve->id,
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