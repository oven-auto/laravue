<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve;

use App\Http\Controllers\Controller;
use App\Http\Resources\Worksheet\Reserve\ReserveList\ReserveCollection;
use App\Repositories\Worksheet\Modules\Reserve\ReserveRepository;
use Illuminate\Http\Request;

class ReserveListController extends Controller
{
    public function index(ReserveRepository $repo, Request $request)
    {
        $reserves = $repo->paginate($request->all());

        return new ReserveCollection($reserves);
    }



    public function count(ReserveRepository $repo, Request $request)
    {
        $count = $repo->counter($request->all());

        return response()->json([
            'count' => $count,
            'success' => 1,
            'message' => '',
        ]);
    }
}
