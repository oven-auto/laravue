<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::select([
                'cars.mark_id',
                'marks.name as model',
                'brands.name as brand',
                DB::raw('CAST(SUM(IF(cars.status = "in_stock", 1, 0)) as integer) as in_stock'),
                DB::raw('CAST(SUM(IF(cars.status = "in_shipment", 1, 0)) as integer) as in_shipment'),
                DB::raw('CAST(SUM(IF(cars.status = "in_request", 1, 0))  as integer) as in_request'),
                DB::raw('CAST(SUM(IF(cars.status = "in_ready", 1, 0)) as integer) as in_ready'),
                DB::raw('CAST(SUM(IF(cars.status = "in_build", 1, 0)) as integer) as in_build'),
                DB::raw('CAST(SUM(IF(cars.status = "in_plan", 1, 0)) as integer) as in_plan'),
                DB::raw('CAST(SUM(IF(cars.status = "in_order", 1, 0)) as integer) as in_order'),
                DB::raw('CAST(SUM(IF(cars.status = "in_application", 1, 0)) as integer) as in_application'),
                DB::raw('count(cars.id) as total'),
            ])
            ->leftJoin('marks', 'marks.id', 'cars.mark_id')
            ->leftJoin('brands', 'brands.id', 'cars.brand_id')
            ->leftJoin('car_states', 'car_states.status', 'cars.status')
            ->groupBy('cars.mark_id');            

        $res = $query->orderBy('cars.brand_id')->orderBy('cars.mark_id')->get();
        
        $brand = null;
        
        $res->each(function($item) use(&$brand, &$res){
            if($brand != $item->brand)
            {
                $brand = $item->brand;
                $res->push([
                    'mark_id'           => 0,
                    'model'             => 'Итого '.$brand,
                    'brand'             => $brand,
                    'in_stock'          => $res->where('brand', $item->brand)->sum('in_stock'),
                    'in_shipment'       => $res->where('brand', $item->brand)->sum('in_shipment'),
                    'in_request'        => $res->where('brand', $item->brand)->sum('in_request'),
                    'in_ready'          => $res->where('brand', $item->brand)->sum('in_ready'),
                    'in_build'          => $res->where('brand', $item->brand)->sum('in_build'),
                    'in_plan'           => $res->where('brand', $item->brand)->sum('in_plan'),
                    'in_order'          => $res->where('brand', $item->brand)->sum('in_order'),
                    'in_application'    => $res->where('brand', $item->brand)->sum('in_application'),
                    'total'             => $res->where('brand', $item->brand)->sum('total'),
                ]);
            }
        });

        $res->push([
            'mark_id'           => 0,
            'model'             => 0,
            'brand'             => 'Итого',
            'in_stock'          => $res->where('mark_id', '>', 0)->sum('in_stock'),
            'in_shipment'       => $res->where('mark_id', '>', 0)->sum('in_shipment'),
            'in_request'        => $res->where('mark_id', '>', 0)->sum('in_request'),
            'in_ready'          => $res->where('mark_id', '>', 0)->sum('in_ready'),
            'in_build'          => $res->where('mark_id', '>', 0)->sum('in_build'),
            'in_plan'           => $res->where('mark_id', '>', 0)->sum('in_plan'),
            'in_order'          => $res->where('mark_id', '>', 0)->sum('in_order'),
            'in_application'    => $res->where('mark_id', '>', 0)->sum('in_application'),
            'total'             => $res->where('mark_id', '>', 0)->sum('total'),
        ]);

        return response()->json([
            'data' => $res->groupBy('brand'),
            'success' => 1,            
        ]);
    }
}
