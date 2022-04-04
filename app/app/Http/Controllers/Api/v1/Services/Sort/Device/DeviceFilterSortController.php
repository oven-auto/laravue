<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceFilter;
use App\Services\SortService\SortChangeService;

class DeviceFilterSortController extends Controller
{
    public function index(Request $request, SortChangeService $service)
    {
        $data = $request->all();

        $result = $service->changeSort(new DeviceFilter, $data);

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
