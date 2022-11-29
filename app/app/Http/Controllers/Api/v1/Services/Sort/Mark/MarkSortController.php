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
        $result = $service->changeSort(new Mark, $request->all());

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
