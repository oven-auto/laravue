<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;
use \App\Http\Resources\Trafic\TraficEditCollection;
use \App\Http\Resources\Trafic\TraficSaveResource;
use Auth;
use Socket;

class TraficController extends Controller
{
    public $service;

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
        return (new TraficSaveResource($trafic))
            ->additional(['message' => 'Трафик создан']);
    }

    public function edit($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        return (new TraficSaveResource($trafic))
            ->additional(['message' => 'Трафик открыт']);
    }

    public function update($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        $this->service->save($trafic, $request->all());
        \App\Events\TraficEvent::dispatch($trafic);
        return (new TraficSaveResource($trafic))
            ->additional([
                'message' => 'Трафик изменен',
                //'socket' => $service_port
        ]);
    }

    public function close($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->find($trafic);
        $result = $trafic->close();

        if($result)
            return (new TraficSaveResource($trafic))
                ->additional(['message' => 'Трафик упущен']);

        throw new \Exception('Нельзя упускать трафик без ответственного');
    }

    public function delete($trafic)
    {
        $trafic = Trafic::withTrashed()->find($trafic);

        if ($trafic->trashed()) {
            throw new \Exception('Трафик уже помечен как удаленный');
        }

        $trafic->delete();
        return (new TraficSaveResource($trafic))
            ->additional(['message' => 'Трафик удален']);
    }
}
