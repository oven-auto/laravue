<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $query = Color::with('brand');

        if($request->has('brand_id'))
            $query->where('brand_id', $request->brand_id);
        if($request->has('code'))
            $query->where('colors.code', 'like', '%'.$request->get('code').'%');
        if($request->has('name'))
            $query->where('colors.name', 'like', '%'.$request->get('name').'%');

        $colors = $query->get();

        if($colors->count())
            return response()->json([
                'status' => 1,
                'data' => $colors,
                'count' => $colors->count()
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
