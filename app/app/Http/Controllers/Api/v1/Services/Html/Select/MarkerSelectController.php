<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marker;

class MarkerSelectController extends Controller
{
    public function index()
    {
        $data = Marker::pluck('name', 'id');
            return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
