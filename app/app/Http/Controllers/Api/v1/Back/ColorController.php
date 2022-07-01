<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use \App\Http\Filters\ColorFilter;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        $query = Color::with('brand');
        $filter = app()->make(ColorFilter::class, ['queryParams' => array_filter($data)]);
        $colors = $query->filter($filter)->orderBy('brand_id')->orderBy('name')->get();

        if($colors->count())
            return response()->json([
                'status' => 1,
                'data' => $colors,
                'count' => $colors->count(),
                'message' => 'Найдено '.$colors->count().' цвета'
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного цвета'
        ]);
    }

    public function edit(Color $color)
    {
        return response()->json([
            'status' => 1,
            'color' => $color
        ]);
    }

    public function store(Color $color, Request $request)
    {
        $color->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'color' => $color,
            'message' => 'Цвет создан'
        ]);
    }

    public function update(Color $color, Request $request)
    {
        $color->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'color' => $color,
            'message' => 'Цвет изменен'
        ]);
    }
}
