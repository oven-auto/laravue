<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve;

use App\Http\Controllers\Controller;
use App\Http\Resources\Worksheet\Reserve\Contract\ContractListCollection;
use App\Repositories\Worksheet\Modules\Reserve\ReserveContractRepository;
use Illuminate\Http\Request;

class ContractListController extends Controller
{
    public function index(Request $request, ReserveContractRepository $repo)
    {
        $contracts = $repo->paginate($request->all());

        return new ContractListCollection($contracts);
    }



    public function count(Request $request, ReserveContractRepository $repo)
    {
        $count = $repo->counter($request->all());

        return response()->json([
            'count' => $count,
            'success' => 1,
            'message' => '',
        ]);
    }
}
