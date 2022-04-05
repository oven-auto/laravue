<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverTypeSelectController extends Controller
{
    public function index()
    {
        $types = \App\Models\DriverType::get();
        return response()->json([
            'status' => 1,
            'data' => $types
        ]);
    }
}
