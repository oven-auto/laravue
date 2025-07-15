<?php

namespace App\Http\Controllers\Api\v1\Back\Credit;

use App\Http\Controllers\Controller;
use App\Models\CreditStatus;

class StatusController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => CreditStatus::get(),
            'success' => 1,
        ]);
    }
}
