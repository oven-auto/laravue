<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Color;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Models\MarkColor;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::get();

        return response()->json([
            'data' => $colors->map(function($item) {
                return ['id' => $item->id, 'name' => $item->name, 'code' => $item->web];
            }),
            'success' => 1
        ]);
    }
}
