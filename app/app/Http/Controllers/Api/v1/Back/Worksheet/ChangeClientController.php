<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetAppendSubClientRequest;
use App\Http\Resources\Worksheet\WorksheetChangeClientResource;
use App\Services\Worksheet\WorksheetChangeClient;

class ChangeClientController extends Controller
{
    public function change(WorksheetAppendSubClientRequest $request)
    {
        $worksheet = WorksheetChangeClient::change($request->worksheet_id, $request->client_id);

        return new WorksheetChangeClientResource($worksheet);
    }
}
