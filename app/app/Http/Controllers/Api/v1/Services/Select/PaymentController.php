<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => \App\Models\Payment::select(['id', 'name'])->get(),
            'success' => 1,
        ]);
    }
}
