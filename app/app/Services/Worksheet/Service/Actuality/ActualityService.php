<?php

namespace App\Services\Worksheet\Service\Actuality;

use App\Models\Worksheet\Service\WSMService;
use App\Services\Car\CarGeneral\CarGeneralService;

class ActualityService
{
    public function __construct(public WSMService $service)
    {
        
    }



    public static function init(WSMService $service)
    {
        return new self($service);
    }



    public function check()
    {
        $car = $this->service->car->carable;
        
        $carStock = match ($car::class){
            'App\Models\Car' => $this->getNewCar(),
            'App\Models\ClientCar' => $this->getClientCar(),
            'App\Models\UsedCar' => $this->getUsedCar(),
        };
        
        return $carStock->contains('vin', $car->vin) ? 1 : 0;
    }



    public function getNewCar()
    {
        $cars = $this->service->worksheet->reserves()->with('car')->get()->map(function($item){
            return CarGeneralService::make($item->car);
        });
        
        return $cars;
    }



    public function getClientCar()
    {
        $cars = $this->service->worksheet->client->cars->map(function($item){
            return CarGeneralService::make($item);
        });

        return $cars;
    }



    public function getUsedCar()
    {
        throw new \Exception('Модуль резерв БУ авто пока ещё не сделан');
        
        $cars = $this->service->worksheet->client->cars->map(function($item){
            return CarGeneralService::make($item);
        });

        return $cars;
    }
}