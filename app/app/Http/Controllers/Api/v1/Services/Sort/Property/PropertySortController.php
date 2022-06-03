<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Services\SortService\SortChangeService;

class PropertySortController extends Controller
{
    public function index(Request $request, SortChangeService $service)
    {
        $data = $request->all();

        $result = $service->changeSort(new Property, $data);

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
