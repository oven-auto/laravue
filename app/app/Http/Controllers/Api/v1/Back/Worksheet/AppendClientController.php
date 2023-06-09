<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\WorksheetAppendSubClientRequest;
use App\Models\Client;
use App\Services\Worksheet\WorksheetClient;

class AppendClientController extends Controller
{
    public function append(WorksheetAppendSubClientRequest $request)
    {
        WorksheetClient::attach($request->worksheet_id, $request->client_id);
        WorksheetClient::makeUnionInWorksheet($request->worksheet_id, $request->client_id);

        $subClient = Client::find($request->get('client_id'));

        return response()->json([
            'success' => 1,
            'client' => [
                'id' => $subClient->id,
                'name' => $subClient->full_name,
            ],
            'message' => 'Клиент добавлен',

        ]);
    }

    public function destroy(WorksheetAppendSubClientRequest $request)
    {
        WorksheetClient::detach($request->worksheet_id, $request->client_id);

        return response()->json([
            'success' => 1,
            'message' => 'Клиент удален'
        ]);
    }
}
