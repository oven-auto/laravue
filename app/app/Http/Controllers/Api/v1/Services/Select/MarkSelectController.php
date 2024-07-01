<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Models\MarkAlias;

class MarkSelectController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|numeric'
        ]);

        $result = \Cache::remember('list:mark', config('cache', 'period'), function () use ($validated) {
            return Mark::where('brand_id', $validated['brand_id'])->where('diller_status', 1)->select('name', 'id')->get();
        });

        return response()->json([
            'success' => 1,
            'data' => $result,
        ]);
    }



    public function all(Request $request, $result = [])
    {
        $validated = $request->validate([
            'brand_id' => 'sometimes|numeric'
        ]);

        if (!isset($validated['brand_id']))
            return response()->json([
                'success' => 1,
                'data' => $result,
            ]);

        $result = Mark::where('brand_id', $validated['brand_id'])->select('name', 'id')->get();

        return response()->json([
            'success' => 1,
            'data' => $result,
        ]);
    }



    public function getaliases()
    {
        return response()->json([
            'data' => MarkAlias::orderBy('name')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            }),
            'success' => 1,
        ]);
    }
}
