<?php

namespace App\Http\Controllers\Api\v1\Pack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;

class PackPriceController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        $pack = Pack::find($data['id']);
        $pack->price = $data['price'];
        $pack->save();
        return response()->json([
            'status' => 1
        ]);
    }
}
