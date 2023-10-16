<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use \App\Models\Client;
use App\Http\Requests\Client\ClientCarRequest;
use App\Http\Resources\Client\Car\ClientCarCollection;
use App\Http\Resources\Client\Car\ClientCarEditResource;
use \App\Models\ClientCar;

class ClientCarController extends Controller
{
    private $repo;

    public function __construct(\App\Repositories\Client\ClientCarRepository $repo)
    {
        $this->repo = $repo;
    }

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
        $this->repo->store($client, $request->input());

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
        $this->repo->update($car, $request->input());

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
        $this->repo->hide($car);

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
        return response()->json([
            'data' => $this->repo->amountClientCar($client),
            'success' => 1
        ]);
    }

    /**
     * Метод получения конкретной машины клиента
     * @param ClientCar $car ClientCar
     * @return ClientCarEditResource
     */
    public function show(ClientCar $car) : ClientCarEditResource
    {
        return new ClientCarEditResource($car);
    }
}
