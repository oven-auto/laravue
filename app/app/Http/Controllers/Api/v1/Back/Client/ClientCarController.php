<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use \App\Models\Client;
use App\Http\Requests\Client\ClientCarRequest;
use App\Http\Resources\Client\Car\ClientCarCollection;
use \App\Models\ClientCar;

class ClientCarController extends Controller
{
    /**
     * Метод на получение всех машин клиента
     * @param Client $client Client
     * @return ClientCarCollection
     */
    public function index(Client $client) : ClientCarCollection
    {
        return new ClientCarCollection($client->cars);
    }

    /**
     * Метод на создание машины клиента
     * @param Client $client Client
     * @param ClientCarRequest $request данные о машине
     * @return ClientCarCollection
     */
    public function store(Client $client, ClientCarRequest $request) : ClientCarCollection
    {
        $data = $request->input();
        $data['actual'] = 1;
        $data['author_id'] = auth()->user()->id;
        $data['editor_id'] = auth()->user()->id;
        $client->cars()->create($data);
        return (new ClientCarCollection($client->cars))
            ->additional(['message' => 'Машина добавлена']);
    }

    /**
     * Метод на изменение машины клиента
     * @param ClientCar $car ClientCar
     * @param ClientCarRequest $request данные о машине
     * @return ClientCarCollection
     */
    public function update(ClientCar $car, ClientCarRequest $request) : ClientCarCollection
    {
        $car->fill($request->input())->save();
        return (new ClientCarCollection($car->client->cars))
            ->additional(['message' => 'Машина изменена']);
    }

    /**
     * Метод на изменение актуальности машины клиента, делает машину более не актуальной
     * @param ClientCar $car ClientCar
     * @return ClientCarCollection
     */
    public function destroy(ClientCar $car) : ClientCarCollection
    {
        $car->actual = 0;
        $car->save();
        return (new ClientCarCollection($car->client->cars))
            ->additional(['message' => 'Машина более не является актуальной']);
    }

    /**
     * Метод на изменение машины клиента
     * @param Client $client Client
     * @return \Illuminate\Http\JsonResponse
     */
    public function amount(Client $client) : \Illuminate\Http\JsonResponse
    {
        $amountClientCar = $client->cars->where('actual',1)->count();
        return response()->json([
            'data' => $amountClientCar,
            'success' => 1
        ]);
    }

    /**
     * Метод получения конкретной машины клиента
     * @param ClientCar $car ClientCar
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ClientCar $car) : \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $car,
            'success' => 1
        ]);
    }
}
