<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetAppendExecutorRequest;
use App\Http\Requests\Worksheet\WorksheetDeleteExecutorRequest;
use App\Models\Worksheet;
use App\Services\Worksheet\WorksheetUser;
use Illuminate\Http\Request;

class AppendUserController extends Controller
{
    public function append(WorksheetAppendExecutorRequest $request)
    {
        $users = WorksheetUser::attach($request->worksheet_id, $request->user_ids);

        return response()->json([
            'success' => 1,
            'executors' => $users->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->cut_name
                ];
            }),
            'message' => 'Пользователи добавлены',
        ]);
    }

    public function destroy(WorksheetDeleteExecutorRequest $request)
    {
        WorksheetUser::detach($request->worksheet_id, $request->user_id);

        return response()->json([
            'success' => 1,
            'message' => 'Пользователь удален'
        ]);
    }
}
