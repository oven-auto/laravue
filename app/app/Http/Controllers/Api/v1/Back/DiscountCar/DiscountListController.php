<?php

namespace App\Http\Controllers\Api\v1\Back\DiscountCar;

use App\Http\Controllers\Controller;
use App\Http\Resources\Worksheet\Reserve\SaleReserveItemResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountListController extends Controller
{
    public function index(Request $request)
    {
        $discounts = Discount::WorksheetRelation()->get();

        return response()->json([
            'data' => $discounts->map(function ($item) {
                return [
                    'discount' => new SaleReserveItemResource($item),
                    'worksheet_id' => $item->id,
                ];
            }),
        ]);
    }



    public function show(Discount $discount) {}



    public function update(Discount $discount) {}
}
