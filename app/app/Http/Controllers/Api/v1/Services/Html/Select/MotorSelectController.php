<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motor;

class MotorSelectController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        $query = Motor::fullData();
        if($request->has('brand_id'))
            $query->where('brand_id', $request->get('brand_id'));

        $motors = $query->orderBy('brand_id')
            ->orderBy('power')
            ->get();

        // foreach($motors as $item)
        //     $data[$item->id] = $item->name.' '
        //         .$item->size.''.$item->type->acronym
        //         .' ('.$item->power.'л.с.) '
        //         .$item->valve.'кл. '
        //         .$item->transmission->acronym.' '
        //         .$item->driver->acronym;
        return response()->json([
            'data' => $motors,
            'status' => 1
        ]);
    }
}
