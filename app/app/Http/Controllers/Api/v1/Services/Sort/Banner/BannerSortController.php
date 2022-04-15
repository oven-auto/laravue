<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Banner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Services\SortService\SortChangeService;

class BannerSortController extends Controller
{
    public function index(Request $request, SortChangeService $service)
    {
        $data = $request->all();

        $result = $service->changeSort(new Banner, $data);

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
