<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransmissionTypeSelectController extends Controller
{
    public function index()
    {
        $types = \App\Models\TransmissionType::get();
        return response()->json([
            'status' => 1,
            'data' => $types
        ]);
    }
}
