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
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request)
    {
        $clients = $this->repo->filter($request->input(), 50);
        return new ClientListCollection($clients);
    }

    /**
     * Получить данные клиента, по указанному id, id превратится в модель Client в middleware
     * @param Client $client  Client
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function edit(Client $client)
    {
        return new ClientEditResource($client);
    }

    /**
     * Создать клиента из полученного request
     * @param Client $client  Client
     * @param Request $request  Request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(Client $client, ClientStoreRequest $request)
    {
        $this->repo->save($client, $request->all());
        return new ClientEditResource($client);
    }

    /**
     * Изменить клиента, по полученому id, данные взять из полученного request
     * @param Client $client  Client
     * @param Request $request  Request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Client $client, Request $request)
    {
        $this->repo->save($client, $request->all());
        return new ClientEditResource($client);
    }
}
