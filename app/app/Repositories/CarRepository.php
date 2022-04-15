<?php

namespace App\Repositories;

use App\Models\Car;
use Storage;
use DB;

Class CarRepository {

    const CAR_COL = [
        'brand_id', 'mark_id', 'complectation_id', 'mark_color_id', 'year', 'device_price', 'vin'
    ];

    public function save(Car $car, $data = [])
    {
        $result = [];
        try {
            $result = DB::transaction(function () use ($data, $car) {
                $mainData = array_filter($data, function ($key) {
                    if (\array_key_exists($key, array_flip(self::CAR_COL))) {
                        return true;
                    }
                }, ARRAY_FILTER_USE_KEY);

                if (isset($data['color_id'])) {
                    $mainData['mark_color_id'] = $data['color_id'];
                }

                $car->fill($mainData)->save();
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

                return ['status' => true];
            });
        } catch(\Exception $e) {
            return $result = [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
        return $result;
    }

    public function getCarArray(Car $car)
    {
        $data = $car->toArray();
        $data['packs'] = $car->packs->pluck('id');
        $data['devices'] = $car->devices->pluck('id');
        $data['color_id'] = $car->color->id;
        $data['marker_id'] = $car->marker->marker_id;
        $data['delivery_stage_id'] = $car->delivery->delivery_stage_id;
        $data['delivery_type_id'] = $car->delivery->delivery_type_id;
        $data['production_at'] = $car->production->production_at;
        return $data;
    }
}
