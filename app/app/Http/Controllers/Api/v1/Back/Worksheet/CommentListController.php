<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Action\CommentListRequest;
use App\Http\Resources\Worksheet\Action\CommentListResource;
use App\Services\Worksheet\Comment;

class CommentListController extends Controller
{
    public function list(CommentListRequest $request, Comment $service)
    {
        $comments = $service->getAllCommentInWorksheet($request->input());

        return new CommentListResource($comments);
    }
}
