<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Worksheet\WorksheetStoreRequest;
use App\Models\Trafic;
use App\Services\Worksheet\WorksheetService;
use App\Http\Resources\Worksheet\WorksheetCreateResource;

class WorksheetController extends Controller
{
    private $service = '';

    public function __construct(WorksheetService $service)
    {
        $this->service = $service;
    }

    public function store(WorksheetStoreRequest $request)
    {
        $worksheet = $this->service->create($request->trafic_id);
        return new WorksheetCreateResource($worksheet);
    }
}
