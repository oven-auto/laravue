<?php

namespace App\Repositories\Client;

use App\Models\ClientCar;
use App\Models\Client;

Class ClientCarRepository
{
    /**
     * Создать машину клиента
     * @param Client $client App\Models\Client
     * @param array $data Данные о машине
     * @return void
     */
    public function store(Client $client, $data = []) : void
    {
        $data['actual'] = 1;
        $data['author_id'] = auth()->user()->id;
        $data['editor_id'] = auth()->user()->id;
        $client->cars()->create($data);
    }

    /**
     * Изменить машину клиента
     * @param ClientCar $car App\Models\ClientCar
     * @param array $data Данные о машине
     * @return void
     */
    public function update(ClientCar $car, $data = []) : void
    {
        $data['editor_id'] = auth()->user()->id;
        $car->fill($data)->save();
    }

    /**
     * Спрятать машину клиента
     * @param ClientCar $car App\Models\ClientCar
     * @return void
     */
    public function hide(ClientCar $car) : void
    {
        $car->actual = 0;
        $car->save();
    }

    /**
     * Количество машин клиента
     * @param Client $client App\Models\Client
     * @return int
     */
    public function amountClientCar($client) : int
    {
        $amountClientCar = $client->cars->where('actual',1)->count();
        return $amountClientCar;
    }
}

