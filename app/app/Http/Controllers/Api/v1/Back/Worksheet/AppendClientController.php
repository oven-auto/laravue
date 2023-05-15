<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class AppendClientController extends Controller
{
    public function append(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $ids = $worksheet->subclients->pluck('id')->toArray();
        array_push($ids, $request->get('client_id'));
        $worksheet->subclients()->sync($ids);
        return response()->json([
            'success' => 1,
        ]);
    }

    public function destroy(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $worksheet->subclients()->detach($request->get('client_id'));
        return response()->json([
            'success' => 1,
        ]);
    }
}
