<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\ClientRepository;
use App\Http\Resources\Client\ClientListCollection;
use App\Http\Resources\Client\ClientEditResource;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\Client\ClientStoreRequest;

class ClientController extends Controller
{
    /**
     * Свойство ссылка на репозиторий
     * @var ClientRepository repo
     */
    private $repo;

    /**
     * В конструкторе получить класс репозитория из сервиспровайдера, и присвоить его переменой repo
     * @param ClientRepository $repo  ClientRepository
     */
    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Список всех клиентов, через пагинацию
     * @param Request $request  Request
     * @return ClientListCollection
     */
    public function index(Request $request) : ClientListCollection
    {
        $clients = $this->repo->paginate($request->input());
        return new ClientListCollection($clients);
    }

    /**
     * Получить данные клиента, по указанному id, id превратится в модель Client в middleware
     * @param Client $client  Client
     * @return ClientEditResource
     */
    public function edit(Client $client) : ClientEditResource
    {
        return new ClientEditResource($client);
    }

    /**
     * Создать клиента из полученного request
     * @param Client $client  Client
     * @param Request $request  Request
     * @return ClientEditResource
     */
    public function store(Client $client, ClientStoreRequest $request) : ClientEditResource
    {
        $this->repo->save($client, $request->all());
        return (new ClientEditResource($client))
            ->additional(['message' => 'Клиент создан']);
    }

    /**
     * Изменить клиента, по полученому id, данные взять из полученного request
     * @param Client $client  Client
     * @param ClientStoreRequest $request  ClientStoreRequest
     * @return ClientEditResource
     */
    public function update(Client $client, ClientStoreRequest $request) : ClientEditResource
    {
        $this->repo->save($client, $request->all());
        return (new ClientEditResource($client))
            ->additional(['message' => 'Клиент изменен']);
    }

    /**
     * Удаление клиента
     * @param Client $client Client
     * @return ClientEditResource
     */
    public function destroy(Client $client) : ClientEditResource
    {
        $this->repo->delete($client);
        return (new ClientEditResource($client))
            ->additional(['message' => 'Клиент удален']);
    }
}
