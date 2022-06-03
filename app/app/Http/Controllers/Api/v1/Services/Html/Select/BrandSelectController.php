<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandSelectController extends Controller
{
    public function index()
    {
        $data = Brand::get();
        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }
}
