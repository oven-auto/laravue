<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BodyWork;

class BodyWorkSelectController extends Controller
{
    public function index()
    {
        $data = BodyWork::get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
