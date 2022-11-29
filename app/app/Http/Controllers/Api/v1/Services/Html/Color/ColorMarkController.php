<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Color;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarkColor;

class ColorMarkController extends Controller
{
    public function index(Request $request)
    {
        $query = MarkColor::select('mark_colors.*')->with('color')
            ->leftJoin('colors','colors.id','=','mark_colors.color_id');

        if($request->has('mark_id'))
            $query->where('mark_id', $request->get('mark_id'));

        if($request->has('complectation_id'))
            $query->rightJoin('complectation_colors', 'complectation_colors.mark_color_id', '=', 'mark_colors.id')
                ->where('complectation_colors.complectation_id', $request->get('complectation_id'));

        $markcolors = $query->get();

        return \App\Http\Resources\Complectation\ColorComplectationResource::collection($markcolors);
    }
}
