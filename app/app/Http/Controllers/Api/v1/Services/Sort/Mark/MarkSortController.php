<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Mark;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Services\SortService\SortChangeService;

class MarkSortController extends Controller
{
    public function index(Request $request, SortChangeService $service)
    {
        $data = $request->all();

        $result = $service->changeSort(new Mark, $data);

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
