<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientListResource;
use App\Repositories\Client\ClientRepository;
use App\Http\Resources\Client\ClientListCollection;
use App\Http\Resources\Client\ClientEditResource;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\Client\ClientStoreRequest;

/**
 * Контролер упраления клиентами
 * @var ClientRepository repo ClientRepository
 */
class ClientController extends Controller
{
    /**
     * Свойство ссылка на репозиторий
     * @var ClientRepository repo ClientRepository
     */
    private $repo;

    private const EVENT_STATUS = [
        'open'   => 'Клиент открыт',
        'create' => 'Клиент создан',
        'update' => 'Клиент изменен',
        'close' =>  'Клиент упущен',
        'delete' => 'Клиент удален'
    ];

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
        $clients = $this->repo->paginate($request->input(), 30);

        return new ClientListCollection($clients);
    }

    public function show(Client $client)
    {
        return new \App\Http\Resources\Client\ClientCartResource($client);
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
            ->additional([
                'message' => 'Клиент изменен',
                'client' => new ClientListResource(($client))
            ]);
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
            ->additional(['message' => 'Клиент удален', 'result' => 1]);
    }
}
