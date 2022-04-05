<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::orderBy('sort')->get();
        if($properties->count())
            return response()->json([
                'status' => 1,
                'data' => $properties,
                'count' => $properties->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного характеристики'
        ]);
    }

    public function edit(Property $property)
    {
        return response()->json([
            'status' => 1,
            'property' => $property
        ]);
    }

    public function store(Property $property, Request $request)
    {
        $data = $request->all();
        $data['sort'] = Property::max('sort')+1;
        $property->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'property' => $property,
            'message' => 'Характеристика создана'
        ]);
    }

    public function update(Property $property, Request $request)
    {
        $property->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'property' => $property,
            'message' => 'Характеристика изменена'
        ]);
    }
}
