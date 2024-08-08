<?php

namespace App\Http\Controllers\Api\v1\Services\Select;

use App\Http\Controllers\Controller;
use App\Models\BodyWork;
use App\Models\VehicleType;
use Illuminate\Http\Request;

class BodyWorkSelectController extends Controller
{
    public function index(Request $request)
    {
        $query = BodyWork::query()->select('name', 'id');

        if ($request->has('vehicle_id'))
            $query->leftJoin('vehicle_bodies', 'vehicle_bodies.bodywork_id', 'body_works.id')
                ->where('vehicle_bodies.vehicle_id', $request->vehicle_id);

        $result = $query->get();

        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }



    public function vehicletypes()
    {
        $result = \Cache::remember('list:vehicletype', config('cache', 'period'), function () {
            return VehicleType::select('name', 'id')->get();
        });

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }



    public function acronym()
    {
        $result = \Cache::remember('list:bodyacr', config('cache', 'period'), function () {
            return BodyWork::select('acronym', 'name')->where('main', 1)->get();
        });

        return response()->json([
            'data' => $result->map(function ($item) {
                return [
                    'name' => $item->name,
                    'acronym' => $item->acronym,
                ];
            }),
            'success' => 1
        ]);
    }
}
