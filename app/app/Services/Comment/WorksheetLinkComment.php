<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;

Class WorksheetLinkComment extends AbstractComment
{
    public function __construct(CommentInterface $model)
    {
        $this->data = [
            'author_id' => auth()->user()->id,
            'action_id' => $model->worksheet->last_action->id,
            'type' => 0,
        ];
    }

    public function create(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => 'Ссылка добавлена.',
            'type' => 1
        ]);
    }

    public function delete(CommentInterface $model)
    {
        return array_merge($this->data, [
            'text' => 'Ссылка удалена.',
            'type' => 1
        ]);
    }
}
