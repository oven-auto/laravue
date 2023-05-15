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
        array_push($ids, $request->get('user_id'));
        $worksheet->executors()->sync($ids);
        return response()->json([
            'success' => 1,
        ]);
    }

    public function destroy(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $worksheet->executors()->detach($request->get('user_id'));
        return response()->json([
            'success' => 1,
        ]);
    }
}
