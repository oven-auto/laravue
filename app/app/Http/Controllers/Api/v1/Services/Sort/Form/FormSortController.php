<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Services\SortService\SortChangeService;

class FormSortController extends Controller
{
    public function index(Request $request, SortChangeService $service)
    {
        $data = $request->all();

        $result = $service->changeSort(new Form, $data);

        return response()->json([
            'status'=>1,
            'data'=>$result
        ]);
    }
}
