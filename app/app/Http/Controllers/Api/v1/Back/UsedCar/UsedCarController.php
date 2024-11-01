<?php

namespace App\Http\Controllers\Api\v1\Back\UsedCar;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsedCar\UsedCarItemResource;
use App\Repositories\UsedCar\UsedCarRepository;
use Illuminate\Http\Request;

class UsedCarController extends Controller
{
    private $repo;

    public function __construct(UsedCarRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Request $request)
    {
        $cars = $this->repo->paginate($request->all());

        return response()->json([
            'data' => UsedCarItemResource::collection($cars),
            'success' => 1,
        ]);
    }
}
