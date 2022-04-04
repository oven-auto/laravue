<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Complectation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;
use App\Services\SortService\SortChangeService;

class ComplectationSortController extends Controller
{
    public function index(Request $request, SortChangeService $service)
    {
        $data = $request->all();

        $result = $service->changeSort(new Complectation, $data);

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
