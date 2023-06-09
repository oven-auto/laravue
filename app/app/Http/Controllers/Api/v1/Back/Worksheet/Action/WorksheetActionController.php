<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Action;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Action\ActionStatusRequest;
use App\Http\Requests\Worksheet\Action\StoreCommentRequest;
use App\Http\Resources\Worksheet\Action\ActionStatusResource;
use App\Http\Resources\Worksheet\Action\CommentSaveResource;
use \App\Http\Requests\Worksheet\Action\StoreActionRequest;
use \App\Services\Worksheet\ActionSave;

class WorksheetActionController extends Controller
{
    public function store(StoreActionRequest $request, ActionSave $actionSave)
    {
        $actionSave->saveActionFasade($request->input());

        return new \App\Http\Resources\Worksheet\Action\ActionSaveResource($actionSave);
    }

    public function comment(StoreCommentRequest $request, ActionSave $actionSave)
    {
        $actionSave->saveCommentFasade($request->input());

        return new CommentSaveResource($actionSave);
    }


    public function status(ActionStatusRequest $request, ActionSave $actionSave)
    {
        $actionSave->closeActionFasade($request->input());

        return new ActionStatusResource($actionSave);
    }
}
