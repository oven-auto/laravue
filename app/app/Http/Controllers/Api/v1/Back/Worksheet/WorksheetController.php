<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetStoreRequest;
use App\Http\Resources\WorksheetListCollection;
use App\Models\Worksheet;
use App\Repositories\Worksheet\WorksheetRepository;
use App\Http\Resources\Worksheet\WorksheetCreateResource;
use App\Services\Worksheet\WorksheetUser;
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
        // $worksheets = Worksheet::select('worksheets.*')
        //     ->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type'])
        //     ->leftJoin('worksheet_actions', function($join) {
        //         $join->on('worksheet_actions.worksheet_id','worksheets.id');
        //         $join->on('worksheet_actions.end_at', \DB::raw(
        //             '(SELECT wa.end_at FROM worksheet_actions as wa
        //             where wa.worksheet_id = worksheets.id
        //             order by wa.end_at DESC LIMIT 1)')
        //         );
        //     })
        //     ->orderBy('worksheet_actions.begin_at', 'DESC')
        //     ->paginate(20);
        return new WorksheetListCollection($worksheets);
    }

    public function store(WorksheetStoreRequest $request)
    {
        $worksheet = $this->service->createFromTrafic($request->trafic_id);
        return new WorksheetCreateResource($worksheet);
    }

    public function show(Worksheet $worksheet)
    {
        $userId = auth()->user()->id;
        WorksheetUser::attach($worksheet->id, [$userId]);
        return new \App\Http\Resources\Worksheet\WorksheetSaveResource($worksheet);
    }
}
