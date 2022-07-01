<?php

namespace App\Http\Controllers\Api\v1\Services\Images;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceImage;

class DeviceImageController extends Controller
{
    public function index(Request $request)
    {
        $query = DeviceImage::query();
        if($request->has('id'))
            $query->where('device_id', $request->get('id'));
        $deviceImage = $query->first();
        return response()->json([
            'data' => $deviceImage->urlImage
        ]);
    }
}
