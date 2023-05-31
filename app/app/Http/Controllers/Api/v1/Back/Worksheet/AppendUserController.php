<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class AppendUserController extends Controller
{
    public function append(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $ids = $worksheet->executors->pluck('id')->toArray();
        $ids = array_merge($ids, $request->get('user_ids'));

        $worksheet->executors()->sync($ids);
        $worksheet->load('executors');

        return response()->json([
            'success' => 1,
            'executors' => $worksheet->executors->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->cut_name
                ];
            }),
            'message' => 'Пользователи добавлены'
        ]);
    }

    public function destroy(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $worksheet->executors()->detach($request->get('user_id'));
        return response()->json([
            'success' => 1,
            'message' => 'Пользователь удален'
        ]);
    }
}
