<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Action\CommentListRequest;
use App\Http\Resources\Worksheet\Action\CommentListResource;
use App\Repositories\Worksheet\CommentHistory\HistoryRepository;

class CommentListController extends Controller
{
    public function list(CommentListRequest $request, HistoryRepository $history)
    {
        $comments[] = $history->getWorksheetHistory(\App\Models\WorksheetActionComment::class, $request->all());

        $comments[] = $history->getWorksheetHistory(\App\Models\SubActionComment::class, $request->all());

        $collect = collect();

        foreach($comments as $item)
            $collect = $collect->merge($item);

        $collect = $collect->sortBy([['created_at'], ['id']])->reverse()->values();

        // $collect = $collect->sortBy(function($item, $key){
        //     $x = $item->id * 0.1;
        //     $sort = 10 + ($x - floor($x));
        //     return $item->created_at->addSeconds($sort);
        // })->reverse()->values();

        return new CommentListResource($collect);
    }
}
