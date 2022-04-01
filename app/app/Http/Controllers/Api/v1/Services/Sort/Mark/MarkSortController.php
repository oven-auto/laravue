<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Mark;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;

class MarkSortController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $activeMark = Mark::find($data['active']['id']);
        $secondMark = Mark::find($data['second']['id']);

        $sortOld = $activeMark->sort;
        $sortNew = $secondMark->sort;

        $activeMark->sort = $sortNew;
        $secondMark->sort = $sortOld;

        $activeMark->save();
        $secondMark->save();

        $data = [
            $activeMark->id=>$activeMark->sort,
            $secondMark->id=>$secondMark->sort
        ];
            return response()->json([
            'status'=>1,
            'data'=>$data
        ]);
    }
}
