<?php

namespace App\Http\Controllers\Api\v1\Back\Car;

use App\Http\Controllers\Controller;
use App\Repositories\Car\Car\CarRepository;
use Illuminate\Http\Request;

class CarCountController extends Controller
{
    public function __construct()
    {
        $this->middleware('carfilter')->only('count');
    }



    public function count(Request $request, CarRepository $repo)
    {
        $res = $repo->count($request->all());
        
        return response()->json([
            'data' => [
                'general' => [
                    'count'     => $res->count       ?? 0,
                    'base'      => $res->base        ?? 0,
                    'option'    => $res->option      ?? 0,
                    'over'      => $res->overprice   ?? 0,
                    'tuning'    => $res->tuning      ?? 0,
                    'discount'  => $res->discount    ?? 0,
                    'full'      => array_sum([
                        $res->base, 
                        $res->option, 
                        $res->overprice, 
                        $res->tuning]
                    ) - $res->discount 
                ],
                'report' => [
                    'count'     => $res->owner       ?? 0,
                    'disable'   => $res->disable     ?? 0,
                    'owner'     => $res->owner       ?? 0,
                    'green'     => $res->green       ?? 0,
                    'yellow'    => $res->yellow      ?? 0,
                ],
                'factoring' => [
                    'count'         => $res->factoring_count,
                    'sum'           => $res->factoring_sum,
                    'detailing'     => $res->factoring_detailing,
                    'full'          => $res->factoring_sum + $res->factoring_detailing,
                    'collector'     => $res->factoring_collector,
                ],
                'ransom' => [
                    'count'         => $res->ransom_count,
                    'sum'           => $res->ransom_sum,
                    'detailing'     => $res->ransom_detailing,
                    'full'          => $res->ransom_sum + $res->ransom_detailing,
                    'collector'     => $res->ransom_collector,
                ]
            ],
            'success' => 1
        ]);
    }
}
