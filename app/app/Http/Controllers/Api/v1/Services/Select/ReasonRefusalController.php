<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\ReasonRefusal;

class ReasonRefusalController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => ReasonRefusal::get()->map(function ($item) {
                return [
                    'name' => $item->name,
                    'id' => $item->id
                ];
            }),
            'success' => 1,
        ]);
    }
}
