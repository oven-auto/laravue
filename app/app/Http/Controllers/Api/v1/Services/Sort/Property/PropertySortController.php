<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class PropertySortController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $activeProperty = Property::find($data['active']['id']);
        $secondProperty = Property::find($data['second']['id']);

        $sortOld = $activeProperty->sort;
        $sortNew = $secondProperty->sort;

        $activeProperty->sort = $sortNew;
        $secondProperty->sort = $sortOld;

        $activeProperty->save();
        $secondProperty->save();

        $data = [
            $activeProperty->id=>$activeProperty->sort,
            $secondProperty->id=>$secondProperty->sort
        ];
            return response()->json([
            'status'=>1,
            'data'=>$data
        ]);
    }
}
