<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryType;

class DeliveryTypeSelectController extends Controller
{
    public function index()
    {
        $data = DeliveryType::get();
        return response()->json([
            'data' => $data,
            'success' => 1
        ]);
    }
}
