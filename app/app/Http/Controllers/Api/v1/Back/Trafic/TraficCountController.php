<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Trafic\TraficRepository;

class TraficCountController extends Controller
{
    public $service;

    public function __construct(TraficRepository $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $count = $this->service->counter($request->all());

        return response()->json([
            'data' => $count,
            'success' => 1,
            'message' => ''
        ]);
    }
}
