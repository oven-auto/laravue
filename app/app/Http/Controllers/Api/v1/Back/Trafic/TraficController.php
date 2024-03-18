<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;
use \App\Http\Resources\Trafic\TraficEditCollection;
use \App\Http\Resources\Trafic\TraficSaveResource;
use \App\Services\Comment\Comment;

class TraficController extends Controller
{
    public $service;
    public $notice;

    private const EVENT_STATUS = [
        'open'   => 'Трафик открыт',
        'create' => 'Трафик создан',
        'update' => 'Трафик изменен',
        'close' =>  'Трафик упущен',
        'delete' => 'Трафик удален'
    ];



    public function __construct(TraficRepository $service, \App\Classes\Telegram\Notice\TelegramNotice $notice)
    {
        $this->service = $service;
        $this->notice = $notice;
    }



    public function index(Request $request)
    {
        $result = $this->service->paginate($request->all());

        return new TraficEditCollection($result);
    }



    public function store(Trafic $trafic, Request $request)
    {
        $this->service->save($trafic, $request->all());

        //\App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['create']);

        Comment::add($trafic, 'create');

        return (new TraficSaveResource($trafic))
            ->additional([
                'message' => Trafic::NOTICES['create'],
            ]);
    }



    public function edit($trafic, Request $request)
    {
        $trafic = Trafic::withTrashed()->linksCount()->filesCount()->find($trafic);

        if(!$trafic->isMy())
            Comment::add($trafic, 'show');

        return (new TraficSaveResource($trafic))
            ->additional(['message' => Trafic::NOTICES['open']]);
    }



    public function update($trafic, Request $request)
    {
        $trafic = Trafic::linksCount()->filesCount()->find($trafic);

        $this->service->save($trafic, $request->all());

        //\App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['update']);

        Comment::add($trafic, 'update');

        return (new TraficSaveResource($trafic))
            ->additional([
                'message' => Trafic::NOTICES['update'],
        ]);
    }



    public function close($trafic, Request $request)
    {
        $trafic = Trafic::find($trafic);

        $result = $trafic->close();

        Comment::add($trafic, 'close');

        //\App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['close']);


        return (new TraficSaveResource($trafic))
            ->additional(['message' => Trafic::NOTICES['close']]);
    }



    public function delete($trafic, Request $request)
    {
        $trafic = Trafic::find($trafic);

        Comment::add($trafic, 'delete');

        $trafic->delete();

        //\App\Events\TraficEvent::dispatch($trafic, Trafic::NOTICES['delete']);

        return (new TraficSaveResource($trafic))
            ->additional(['message' => Trafic::NOTICES['delete']]);
    }
}
