<?php

namespace App\Http\Controllers\Api\v1\Services\Sort\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceType;

class DeviceTypeSortController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $activeDevice = DeviceType::find($data['active']['id']);
        $secondDevice = DeviceType::find($data['second']['id']);

        $sortOld = $activeDevice->sort;
        $sortNew = $secondDevice->sort;

        $activeDevice->sort = $sortNew;
        $secondDevice->sort = $sortOld;

        $activeDevice->save();
        $secondDevice->save();

        $data = [
            $activeDevice->id=>$activeDevice->sort,
            $secondDevice->id=>$secondDevice->sort
        ];
            return response()->json([
            'status'=>1,
            'data'=>$data
        ]);
    }
}
