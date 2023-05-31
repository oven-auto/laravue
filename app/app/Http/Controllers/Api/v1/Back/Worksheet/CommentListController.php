<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentListController extends Controller
{
    public function list(Request $request)
    {
        if(!$request->has('worksheet_id'))
            throw new \Exception('Не указа идентификатор рабочего листа ');

        $start_memory = memory_get_usage();

        //$worksheet = \App\Models\Worksheet::with('actions')->findOrFail($request->get('worksheet_id'));

        $comments = \App\Models\WorksheetActionComment::select('worksheet_action_comments.*')
            ->with('author')
            ->leftJoin('worksheet_actions', 'worksheet_actions.id', 'worksheet_action_comments.action_id')
            ->where('worksheet_actions.worksheet_id', $request->get('worksheet_id'))
            ->get();

        $memory = memory_get_usage() - $start_memory;

        $data = $comments->map(function($item){
            return [
                'created_at' => $item->created_at->format('d.m.Y (H:i)'),
                'text' => $item->text,
                'writer' => $item->author->cut_name
            ];
        });

        return response()->json([
            'data' => $data,
            'success' => 1,
            'memory'=>number_format($memory/1024/1024,2, '.', '').' Mb'
        ]);
    }
}
