<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\ClientEventStatus;
use App\Services\Client\EventChangeClient;
use Illuminate\Http\Request;

class EventChangeClientController extends Controller
{
    /**
     * @param Request $request - [event_status_id, client_id]
     */
    public function __invoke(Request $request)
    {
        $client = EventChangeClient::change($request->event_status_id, $request->client_id);

        return response()->json([
            'name' => $client->full_name,
            'success' => 1,
            'message' => 'Клиент изменен',
        ]);
    }
}
