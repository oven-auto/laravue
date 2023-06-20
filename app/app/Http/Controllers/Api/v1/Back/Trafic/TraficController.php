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
        $start = microtime(true);
        $memory = memory_get_usage();
        $result = $this->service->paginate($request->all());
        $time = microtime(true) - $start;
        $memory = memory_get_usage() - $memory;

        return (new TraficEditCollection($result))
            ->additional(['time' => round($time,2).'s', 'memory' => round($memory/1024/1024,2).'МБ']);
    }

    public function store(Trafic $trafic, Request $request)
    {
        $this->service->save($trafic, $request->all());
        \App\Events\TraficEvent::dispatch($trafic, self::EVENT_STATUS['create']);
        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::EVENT_STATUS['create']]);
    }

    public function edit($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::EVENT_STATUS['open']]);
    }

    public function update($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        $this->service->save($trafic, $request->all());
        \App\Events\TraficEvent::dispatch($trafic, self::EVENT_STATUS['update']);
        return (new TraficSaveResource($trafic))
            ->additional([
                'message' => self::EVENT_STATUS['update'],
        ]);
    }

    public function close($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        $result = $trafic->close();

        \App\Events\TraficEvent::dispatch($trafic, self::EVENT_STATUS['close']);

        if($result)
            return (new TraficSaveResource($trafic))
                ->additional(['message' => self::EVENT_STATUS['close']]);

        throw new \Exception('Нельзя упускать трафик без ответственного');
    }

    public function delete($trafic)
    {
        $trafic = Trafic::withTrashed()->find($trafic);

        if ($trafic->trashed()) {
            throw new \Exception('Трафик уже помечен как удаленный');
        }

        $trafic->delete();
        \App\Events\TraficEvent::dispatch($trafic, self::EVENT_STATUS['delete']);

        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::EVENT_STATUS['delete']]);
    }
}
