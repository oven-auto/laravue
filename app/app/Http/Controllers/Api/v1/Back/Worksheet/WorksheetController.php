<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Classes\LadaDNM\DNMFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetStoreRequest;
use App\Http\Resources\Worksheet\WorksheetListCollection;
use App\Models\Worksheet;
use App\Repositories\Worksheet\WorksheetRepository;
use App\Http\Resources\Worksheet\WorksheetCreateResource;
use Illuminate\Http\Request;
use App\Services\Comment\Comment;

class WorksheetController extends Controller
{
    private $service;

    public function __construct(WorksheetRepository $service)
    {
        $this->service = $service;
    }



    /**
     * СПИСОК РЛ
     */
    public function index(Request $request, WorksheetRepository $repo)
    {
        $worksheets = $repo->paginate($request->all(), 20);

        return new WorksheetListCollection($worksheets);
    }



    /**
     * СОЗДАТЬ РЛ
     */
    public function store(WorksheetStoreRequest $request)
    {
        $worksheet = $this->service->createFromTrafic($request->trafic_id);

        Comment::add($worksheet->last_action, 'create');

        return new WorksheetCreateResource($worksheet);
    }



    /**
     * ОТКРЫТЬ РЛ
     */
    public function show($worksheet)
    {
        $worksheet = Worksheet::linksCount()->filesCount()->find($worksheet);

        Comment::add($worksheet->last_action, 'show');

        return new \App\Http\Resources\Worksheet\WorksheetSaveResource($worksheet);
    }



    /**
     * ПОМЕТИТЬ РЛ КАК ЗАКРЫТЫЙ
     */
    public function close(Worksheet $worksheet)
    {
        $this->service->close($worksheet);

        return response()->json([
            'success' => 1,
            'message' => 'Рабочий лист ' . $worksheet->status->name,
            'status' => $worksheet->status->name,
            'status_id' => $worksheet->status->id,
        ]);
    }



    /**
     * ВЕРНУТЬ РЛ В РАБОТУ
     */
    public function revert(Worksheet $worksheet)
    {
        $this->service->revert($worksheet);

        return response()->json([
            'success' => 1,
            'message' => 'Рабочий лист ' . $worksheet->status->name,
        ]);
    }
}
