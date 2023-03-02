<?php

namespace App\Repositories\Car;

use App\Models\Car;
use Storage;
use DB;
use App\Http\Filters\CarFilter;

Class CarRepository {

    const CAR_COL = [
        'brand_id', 'mark_id', 'complectation_id', 'mark_color_id', 'year', 'device_price', 'vin', 'device_cost', 'purchase'
    ];

    public function filter($data = [], $paginate = 50)
    {
        $query = Car::select('cars.*')->relationList()
            ->with(['packs:code','marker.name', 'marker.moderator',])
            ->leftJoin('car_deliveries','car_deliveries.car_id','cars.id');
        $filter = app()->make(CarFilter::class, ['queryParams' => array_filter($data)]);
        $cars = $query->filter($filter)->paginate($paginate);
        return $cars;
    }

    public function save(Car $car, $data = [])
    {

                $mainData = array_filter($data, function ($key) {
                    if (\array_key_exists($key, array_flip(self::CAR_COL))) {
                        return true;
                    }
                }, ARRAY_FILTER_USE_KEY);

                if (isset($data['color_id'])) {
                    $mainData['mark_color_id'] = $data['color_id'];
                }

                DB::enableQueryLog();
                $car->fill($mainData)->save();
                dd(DB::getQueryLog());

                $car->packs()->sync($data['packs']);
                $car->devices()->sync($data['devices']);
                $car->marker()->updateOrCreate(
                    ['car_id' => $car->id],
                    ['marker_id' => $data['marker_id']]
                );
                $car->delivery()->updateOrCreate(
                    ['car_id' => $car->id],
                    [
                        'delivery_stage_id' => $data['delivery_stage_id'],
                        'delivery_type_id' => $data['delivery_type_id']
                    ]
                );
                $car->production()->updateOrCreate(
                    ['car_id' => $car->id],
                    ['production_at' => $data['production_at']]
                );
                // if(isset($data['client']['id']))
                //     $car->worksheet()->updateOrCreate(
                //         ['car_id' => $car->id],
                //         ['client_id' => $data['client']['id']]
                //     );
                // else
                //     $car->worksheet()->delete();

                return ['status' => true];


    }

    public function delete($car)
    {
        $vin = $car->vin;
        $car->fixedprice()->updateOrCreate(
            [
                'car_id'=>$car->id
            ],
            [
                'body_price'=>$car->complectation->price,
                'packs_price' => $car->price->pack_price,
                'equipments_price' => $car->device_price
            ]
        );
        $car->delete();
        return response()->json([
            'message' => 'Автомобиль  '.$vin.' помещен в архив',
            'status' => 1
        ]);
    }

    public function getTrashById($id)
    {
        $car = Car::withTrashed()
            ->with(['brand','complectation','devices','packs','mark','color','price','fixedprice','complectation'])
            ->find($id);
        return $car;
    }
}
