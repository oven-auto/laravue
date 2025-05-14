<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Classes\Telegram\SystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trafic\TraficCreateRequest;
use Illuminate\Http\Request;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;
use \App\Http\Resources\Trafic\TraficEditCollection;
use \App\Http\Resources\Trafic\TraficSaveResource;
use App\Jobs\TelegramJob;
use App\Models\TelegramConnection;
use \App\Services\Comment\Comment;

class TraficController extends Controller
{
    public $repo;

    private const NOTICES = [
        'open'   => 'Трафик открыт',
        'create' => 'Трафик создан',
        'update' => 'Трафик изменен',
        'close' =>  'Трафик упущен',
        'delete' => 'Трафик удален'
    ];



    public function __construct(TraficRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Request $request)
    {
        $result = $this->repo->paginate($request->all());

        return new TraficEditCollection($result);
    }



    public function store(Trafic $trafic, TraficCreateRequest $request)
    {
        SystemMessage::send('Вызван метод контролера. Создан трафик.');

        $this->repo->save($trafic, $request->all());

        return (new TraficSaveResource($trafic))
            ->additional([ 'message' => self::NOTICES['create'],]);
    }



    public function edit(Trafic $trafic)
    {
        if (!$trafic->isMy())
            Comment::add($trafic, 'show');
        
        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::NOTICES['open']]);
    }



    public function update(Trafic $trafic,  TraficCreateRequest $request)
    {   
        SystemMessage::send('Вызван метод контролера. Изменить трафик.');

        $this->repo->save($trafic, $request->all());

        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::NOTICES['update'],]);
    }



    public function close(Trafic $trafic)
    {
        $this->repo->close($trafic);

        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::NOTICES['close']]);
    }



    public function delete(Trafic $trafic)
    {
        $this->repo->delete($trafic);

        return (new TraficSaveResource($trafic))
            ->additional(['message' => self::NOTICES['delete']]);
    }
}
