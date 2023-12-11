<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetAppendExecutorRequest;
use App\Http\Requests\Worksheet\WorksheetDeleteExecutorRequest;
use App\Models\Worksheet;
use App\Models\User;
use App\Services\Worksheet\WorksheetUser;
use Illuminate\Http\Request;

class AppendUserController extends Controller
{
    public function append(WorksheetAppendExecutorRequest $request)
    {
        $users = User::whereIn('id', $request->user_ids)->get();

        WorksheetUser::attach(Worksheet::findOrfail($request->worksheet_id), $users);

        return response()->json([
            'success' => 1,
            'message' => 'Пользователи добавлены',
        ]);
    }

    public function destroy(WorksheetDeleteExecutorRequest $request)
    {
        WorksheetUser::detach(
            Worksheet::findOrfail($request->worksheet_id),
            User::findOrFail($request->user_id)
        );

        return response()->json([
            'success' => 1,
            'message' => 'Пользователь удален'
        ]);
    }
}
