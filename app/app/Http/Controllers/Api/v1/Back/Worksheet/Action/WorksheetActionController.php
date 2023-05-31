<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Action;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorksheetActionController extends Controller
{
    public function store(Request $request)
    {
        $worksheetAction = \App\Models\WorksheetAction::create([
            'worksheet_id' => $request->worksheet_id,
            'begin_at' => $request->begin_at,
            'end_at' => $request->end_at,
            'task_id' => $request->task_id,
            'author_id' => auth()->user()->id,
            'status' => 'work',
        ]);

        $worksheetAction->last_comment()->create([
            'author_id' => auth()->user()->id,
            'text' => $request->text,
        ]);

        $worksheetAction->fresh();

        return response()->json([
            'data' => [
                'worksheet_id' => $worksheetAction->worksheet_id,
                'action_id' => $worksheetAction->id,
                'begin_at' => $worksheetAction->begin_date,
                'end_at' => $worksheetAction->end_date,
                'task' => $worksheetAction->task->name,
                'is_working' => $worksheetAction->isWorking(),
                'is_waiting' => $worksheetAction->isWaiting(),
                'status_msg' => $worksheetAction->statusMsg(),
                'last_comment' => [
                    'created_at' => $worksheetAction->last_comment->created_at ? $worksheetAction->last_comment->created_at->format('d.m.Y (H:i)') : '',
                    'text' => $worksheetAction->last_comment->text,
                    'author' => $worksheetAction->last_comment->author->cut_name
                ],
            ],
            'success' => 1,
            'message' => 'Действие создано'
        ]);
    }

    public function status(Request $request)
    {
        $message = '';
        $action = \App\Models\WorksheetAction::findOrFail($request->action_id);
        if($action->status !== 'work')
            throw new \Exception('Данное действие не имеет статус "в работе", значит его нельзя подтвердить');

        if($request->status == 'confirm') {
            $action->status = 'confirm';
            $message.="подтверждено";
        }

        if($request->status == 'abort') {
            $action->status = 'abort';
            $message.="отменено";
        }

        $action->save();
        return response()->json([
            'data' => [
                'is_working' => $action->isWorking(),
                'status_msg' => $action->statusMsg(),
            ],
            'success' => 1,
            'message' => 'Действие '.$message
        ]);
    }

    public function comment(Request $request)
    {
        $action = \App\Models\WorksheetAction::findOrFail($request->action_id);
        $action->last_comment()->create([
            'author_id' => auth()->user()->id,
            'text' => $request->text,
        ]);
        $action->load('last_comment');
        return response()->json([
            'data' => [
                'created_at' => $action->last_comment->created_at ? $action->last_comment->created_at->format('d.m.Y (H:i)') : '',
                'text' => $action->last_comment->text,
                'author' => $action->last_comment->author->cut_name
            ],
            'success' => 1,
            'message' => 'Комментарий добавлен'
        ]);
    }
}
