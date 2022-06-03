<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Color;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarkColor;

class ColorMarkController extends Controller
{
    public function index(Request $request)
    {
        $time = '?'. date('dmyhms');

        $query = MarkColor::with('color');

        if($request->has('mark_id'))
            $query->where('mark_id', $request->get('mark_id'));

        if($request->has('complectation_id'))
            $query->rightJoin('complectation_colors', 'complectation_colors.mark_color_id', '=', 'mark_colors.id')
                ->where('complectation_colors.complectation_id', $request->get('complectation_id'));

        $markcolors = $query->get();

        foreach($markcolors as $item) {
            $item->image = asset('storage'.$item->image) . $time;
        }

        if($markcolors->count())
            return response()->json([
                'status' => 1,
                'data' => $markcolors,
                'count' => $markcolors->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного предустановленного цвета для этой модели'
        ]);
    }
}
