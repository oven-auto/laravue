<?php

namespace App\Http\Controllers\Api\v1\Back\Car;

use App\Http\Controllers\Controller;
use App\Repositories\Car\Car\CarRepository;
use Illuminate\Http\Request;

class CarCountController extends Controller
{
    public function count(Request $request, CarRepository $repo)
    {
        $count = $repo->count($request->all());

        return response()->json([
            'data' => $count,
            'success' => 1
        ]);
    }
}
