<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryFactory;

class CountryFactorySelectController extends Controller
{
    public function index()
    {
        $data = CountryFactory::get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
