<?php

namespace App\Services\Worksheet;
use App\Models\Client;
use App\Models\Worksheet;

class WorksheetChangeClient
{
    public static function change($worksheet_id, $client_id)
    {
        $worksheet = Worksheet::find($worksheet_id);

        $oldClient = $worksheet->client_id;
        $worksheet->client_id = $client_id;
        $worksheet->save();

        WorksheetClient::attach($worksheet->id, $oldClient);

        WorksheetClient::detach($worksheet->id, $client_id);

        $oldClient = Client::find($oldClient);
        Comment::commentAppendClient($worksheet_id, $oldClient, 'Старый клиент');

        $newClient = Client::find($client_id);
        Comment::commentAppendClient($worksheet_id, $newClient, 'Назначен новый клиент');

        return $worksheet;
    }
}
