<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;

class CollectorController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => \App\Models\Collector::select(['id', 'name'])->get(),
            'success' => 1,
        ]);
    }
}
