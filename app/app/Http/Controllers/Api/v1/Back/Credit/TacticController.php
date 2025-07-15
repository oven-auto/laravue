<?php

namespace App\Http\Controllers\Api\v1\Back\Credit;

use App\Http\Controllers\Controller;
use App\Models\CreditTactic;

class TacticController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => CreditTactic::get(),
            'success' => 1,
        ]);
    }
}
