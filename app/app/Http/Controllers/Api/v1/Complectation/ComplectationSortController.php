<?php

namespace App\Http\Controllers\Api\v1\Complectation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class ComplectationSortController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $activeComplectation = Complectation::find($data['active']['id']);
        $secondComplectation = Complectation::find($data['second']['id']);

        $sortOld = $activeComplectation->sort;
        $sortNew = $secondComplectation->sort;

        $activeComplectation->sort = $sortNew;
        $secondComplectation->sort = $sortOld;

        $activeComplectation->save();
        $secondComplectation->save();

        $data = [
            $activeComplectation->id=>$activeComplectation->sort,
            $secondComplectation->id=>$secondComplectation->sort
        ];
        return response()->json([
            'status'=>1,
            'data'=>$data
        ]);
    }
}
