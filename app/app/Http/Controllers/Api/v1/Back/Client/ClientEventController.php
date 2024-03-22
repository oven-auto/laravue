<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientEvent;
use App\Models\ClientEventStatus;
use Illuminate\Http\Request;
use App\Http\Requests\Client\ClientEventRequest;

class ClientEventController extends Controller
{
    private $repo;

    public function __construct(\App\Repositories\Client\ClientEventRepository $repo)
    {
        $this->repo = $repo;
        $this->middleware('permission.clientevent:index')->only('index');
        $this->middleware('permission.clientevent:update')->only('update');
        $this->middleware('permission.clientevent:show')->only('show');
        $this->middleware('permission.clientevent:store' )->only('store');
    }

    /**
     * Cписок-пагинация всех коммуникаций
     * @param Request $request Request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(Request $request)
    {
        $data = $this->repo->paginate($request->input(), 20);

        return (new \App\Http\Resources\Client\EventIndexCollection($data));
    }

    /**
     * Открыть коммуникацию
     * @param ClientEvent $event ClientEvent
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($event)
    {
        $clientEventStatus = ClientEventStatus::with('event')->findOrFail($event);

        return (new \App\Http\Resources\Client\EventSaveResource($clientEventStatus));
    }

    /**
     * Создать коммуникацию
     * @param ClientEvent $event ClientEvent заглушка, в этом методе всегда пустая
     * @param ClientEventRequest $request ClientEventRequest
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(ClientEventStatus $clientEventStatus, ClientEventRequest $request)
    {
        $this->repo->save($clientEventStatus->event, $request->input());

        return (new \App\Http\Resources\Client\EventSaveResource($clientEventStatus->event->lastStatus))
            ->additional([
                'message' => 'Событие клиента создано',
                'event' => new \App\Http\Resources\Client\EventIndexResource($clientEventStatus->event->lastStatus)
            ]);
    }

    /**
     * Изменить коммуникацию
     * @param int $clientEventStatusId
     * @param ClientEventRequest $request ClientEventRequest
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(int $clientEventStatusId, Request $request)
    {
        $clientEventStatus = ClientEventStatus::with('event')->find($clientEventStatusId);

        $this->repo->save($clientEventStatus->event, $request->input());

        $clientEventStatus->load('event');

        return (new \App\Http\Resources\Client\EventSaveResource($clientEventStatus))
            ->additional([
                'message' => 'Событие клиента изменено',
                'event' => new \App\Http\Resources\Client\EventIndexResource($clientEventStatus),
            ]);
    }

    public function destroy(ClientEvent $event)
    {

    }
}
