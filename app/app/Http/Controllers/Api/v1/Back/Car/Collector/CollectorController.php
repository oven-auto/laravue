<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Collector;

use App\Http\Controllers\Controller;
use App\Http\Resources\Car\Collector\CollectorSaveResource;
use App\Models\Collector;
use Illuminate\Http\Request;

class CollectorController extends Controller
{
    public function index(Request $request)
    {
        $query = Collector::query();

        if ($request->has('trash') && $request->get('trash') > 0)
            $query->onlyTrashed();

        $collectors = $query->get();

        return response()->json([
            'data' => $collectors,
            'success' => 1,
        ]);
    }



    public function store(Collector $collector, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $result = $collector->fill($validated)->save();

        return (new CollectorSaveResource($collector))->additional(['message' => 'Залогодатель создан']);
    }



    public function update(Collector $collector, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $result = $collector->fill($validated)->save();

        return (new CollectorSaveResource($collector))->additional(['message' => 'Залогодатель изменен']);
    }



    public function show(Collector $collector, Request $request)
    {
        return (new CollectorSaveResource($collector));
    }



    public function destroy(Collector $collector)
    {
        $collector->delete();

        return response()->json([
            'message' => 'Залогодатель удален',
            'success' => 1,
        ]);
    }



    public function revert(Collector $collector)
    {
        $collector->restore();

        return response()->json([
            'message' => 'Залогодатель актуален',
            'success' => 1,
        ]);
    }
}
