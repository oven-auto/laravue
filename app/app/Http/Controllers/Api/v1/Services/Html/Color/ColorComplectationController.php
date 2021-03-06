<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Color;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ColorComplectationController extends Controller
{
    public function index(Request $request)
    {
        $data = [];

        if($request->has('complectation_id')) {
            $complectation = Complectation::with(['colorPacks'])->find($request->get('complectation_id'));
            $colors = $complectation->colorPacks;
            if($colors) {
                foreach($colors as $item) {
                    $mas = ['id' => $item->id, 'pack_id' => $item->pivot->pack_id];
                    array_push($data, ($mas));
                }
            }
            return response()->json([
                'status' => 1,
                'data' => $data
            ]);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного цвета!'
        ]);
    }
}
