<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;

Class TraficFileComment extends AbstractComment
{
    public function __construct(CommentInterface $model)
    {
        $this->data = [
            'author_id' => auth()->user()->id,
            'trafic_id' => $model->trafic_id,
        ];
    }

    public function create(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => 'Файл добавлен.'
        ]);
    }

    public function delete(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => 'Файл удален.'
        ]);
    }
}
