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
        $data['brand_id'] = $car->brand_id;
        $data['mark_id'] = $car->mark_id;
        $data['complectation_id'] = $car->complectation_id;
        $data['color_id'] = $car->color->id;
        $data['vin'] = $car->vin;
        $data['year'] = $car->year;
        $data['device_price'] = $car->device_price;
        $data['packs'] = $car->packs->pluck('id');
        $data['devices'] = $car->devices->pluck('id');

        return $data;
    }
}
