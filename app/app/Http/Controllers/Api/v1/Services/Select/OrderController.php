<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTerm;
use App\Models\DetailingCost;
use App\Models\OrderType;

class OrderController extends Controller
{
    public function types()
    {
        $result = \Cache::remember('order:types', config('cache', 'period'), function(){
            return OrderType::select('name','id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }



    public function deliveryterms()
    {
        $result = \Cache::remember('order:deliveryterms', config('cache', 'period'), function(){
            return DeliveryTerm::select('name','id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }



    public function detailingcosts()
    {
        $result = \Cache::remember('order:detailingcosts', config('cache', 'period'), function(){
            return DetailingCost::select('name','id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }
}
