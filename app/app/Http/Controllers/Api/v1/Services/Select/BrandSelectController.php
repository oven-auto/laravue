<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandSelectController extends Controller
{
    public function all()
    {
        $result = \Cache::remember('list:brand', config('cache', 'period'), function() {
            return Brand::select('name', 'id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }



    public function dealer()
    {
        $result = \Cache::remember('list:dillerbrand', config('cache', 'period'), function() {
            return Brand::where('diller', 1)->select('name', 'id')->get();
        });
        
        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }
}
