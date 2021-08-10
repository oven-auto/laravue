<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryFactory;

class CountryFactoryController extends Controller
{
    public function index()
    {
        $countryfactories = CountryFactory::get();
        if($countryfactories->count())
            return response()->json([
                'status' => 1,
                'data' => $countryfactories,
                'count' => $countryfactories->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного места сборки'
        ]);
    }

    public function edit(CountryFactory $countryfactory)
    {
        return response()->json([
            'status' => 1,
            'countryfactory' => $countryfactory
        ]);
    }

    public function store(CountryFactory $countryfactory, Request $request)
    {
        $countryfactory->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'countryfactory' => $countryfactory,
            'message' => 'Место сборки создано'
        ]);
    }

    public function update(CountryFactory $countryfactory, Request $request)
    {
        $countryfactory->fill($request->input())->save();
        return response()->json([
            'status' => 1,
            'countryfactory' => $countryfactory,
            'message' => 'Место сборки изменено'
        ]);
    }
}
