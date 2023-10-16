<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetStoreRequest;
use App\Http\Resources\Worksheet\WorksheetListCollection;
use App\Models\Worksheet;
use App\Repositories\Worksheet\WorksheetRepository;
use App\Http\Resources\Worksheet\WorksheetCreateResource;
use App\Services\Worksheet\Comment;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{
    private $service;

    public function __construct(WorksheetRepository $service)
    {
        $this->service = $service;
    }

    public function index(Request $request, WorksheetRepository $repo)
    {
        $worksheets = $repo->paginate($request->all(), 20);
        return new WorksheetListCollection($worksheets);
    }

    public function store(WorksheetStoreRequest $request)
    {
        $worksheet = $this->service->createFromTrafic($request->trafic_id);
        return new WorksheetCreateResource($worksheet);
    }

    public function show(Worksheet $worksheet, \App\Services\Worksheet\WorksheetCommentOnShow $service)
    {
        $service->addShowComment($worksheet);
        return new \App\Http\Resources\Worksheet\WorksheetSaveResource($worksheet);
    }

    public function close(Worksheet $worksheet)
    {
        if($worksheet->status_id != 'check')
            throw new \Exception('Команда не может быть выполнена. Закрыть можно только рабочий лист, находящийся на проверке');
        if(!in_array($worksheet->last_action->task->slug, ['confirm','abort']))
            throw new \Exception('Последнее действие в рабочем листе не подразумевает его закрытие. Создайте закрывающее действие.');

        $worksheet->status_id = $worksheet->last_action->task->slug;
        $worksheet->close_at = now();
        $worksheet->inspector_id = auth()->user()->id;
        $worksheet->save();
        Comment::addComment($worksheet, "Рабочий лист ".$worksheet->status->name.' '.auth()->user()->cut_name);
        return response()->json([
            'success' => 1,
            'message' => 'Рабочий лист '.$worksheet->status->name,
            'status' => $worksheet->status->name,
            'status_id' => $worksheet->status->id,
        ]);
    }

    public function revert(Worksheet $worksheet)
    {
        if(in_array($worksheet->status_id, ['work']))
            throw new \Exception('Рабочий лист в работе, незачем его возвращать в работу.');

        $notice = '';
        if(in_array($worksheet->status_id, ['confirm','abort']))
            $notice = ', однако до этого рабочий лист был '.$worksheet->status->name. '. Удостовертесь в правильности данных и продолжайте работу, иначе закройте рабочий лист.';
        $worksheet->status_id = 'work';
        $worksheet->inspector_id = null;
        $worksheet->close_at = null;
        $worksheet->save();
        Comment::addComment($worksheet, "Рабочий лист ".$worksheet->status->name.' '.auth()->user()->cut_name);

        $dataTraficAction = [
            'task_id' => \App\Models\Task::where('slug','control')->first()->id,
            'worksheet_id' => $worksheet->id,
            'begin_at' => now(),
            'end_at' => now()->addMinutes(30),
            'author_id' => auth()->user()->id,
            'text' => 'Возобновить работу в данном рабочем листе',
        ];
        $actionService = new \App\Services\Worksheet\ActionSave();
        $actionService->saveActionFasade($dataTraficAction);

        return response()->json([
            'success' => 1,
            'message' => 'Рабочий лист '.$worksheet->status->name.$notice,
        ]);
    }
}
