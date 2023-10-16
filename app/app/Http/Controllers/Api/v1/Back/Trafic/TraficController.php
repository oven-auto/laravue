<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;
use \App\Http\Resources\Trafic\TraficEditCollection;
use \App\Http\Resources\Trafic\TraficSaveResource;

class TraficController extends Controller
{
    public $service;

    private const EVENT_STATUS = [
        'open'   => 'Трафик открыт',
        'create' => 'Трафик создан',
        'update' => 'Трафик изменен',
        'close' =>  'Трафик упущен',
        'delete' => 'Трафик удален'
    ];

    public function __construct(TraficRepository $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $result = $this->service->paginate($request->all());

        return new TraficEditCollection($result);
    }

    public function store(Trafic $trafic, Request $request)
    {
        $this->service->save($trafic, $request->all());
        \App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['create']);

        return (new TraficSaveResource($trafic))
            ->additional([
                'message' => Trafic::NOTICES['create'],
            ]);
    }

    public function edit($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        if(!$trafic->isMy())
            \App\Services\Comment\CommentService::customMessage($trafic, Trafic::NOTICES['open']);
        return (new TraficSaveResource($trafic))
            ->additional(['message' => Trafic::NOTICES['open']]);
    }

    public function update($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        $this->service->save($trafic, $request->all());
        \App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['update']);

        return (new TraficSaveResource($trafic))
            ->additional([
                'message' => Trafic::NOTICES['update'],
        ]);
    }

    public function close($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        $result = $trafic->close();

        \App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['close']);

        if($result)
        {

            return (new TraficSaveResource($trafic))
                ->additional(['message' => Trafic::NOTICES['close']]);
        }

        throw new \Exception('Нельзя упускать трафик без ответственного');
    }

    public function delete($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);

        if ($trafic->trashed()) {
            throw new \Exception('Трафик уже помечен как удаленный');
        }

        $trafic->delete();

        \App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['delete']);

        return (new TraficSaveResource($trafic))
            ->additional(['message' => Trafic::NOTICES['delete']]);
    }
}
