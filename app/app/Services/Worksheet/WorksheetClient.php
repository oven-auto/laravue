<?php

namespace App\Services\Worksheet;
use App\Models\Client;
use App\Models\WorksheetSubClient;
use App\Repositories\Client\ClientUnionRepository;

Class WorksheetClient
{
    public static function attach($worksheet_id, $client_id)
    {
        $isIn = WorksheetSubClient::where('worksheet_id', $worksheet_id)
            ->where('client_id', $client_id)
            ->count();

        if($isIn)
            throw new \Exception('Клиент уже добавлен');

        if(!$isIn)
        {
            WorksheetSubClient::create([
                'worksheet_id' => $worksheet_id,
                'client_id' => $client_id
            ]);

            $client = Client::find($client_id);

            Comment::commentAppendClient($worksheet_id, $client, 'Добавлено новое контактное лицо');
        }
    }

    public static function detach($worksheet_id, $client_id)
    {
        $isIn = WorksheetSubClient::where('worksheet_id', $worksheet_id)
            ->where('client_id', $client_id)
            ->count();
        if($isIn)
        {
            WorksheetSubClient::where('worksheet_id', $worksheet_id)
                ->where('client_id', $client_id)
                ->delete();
            $client = Client::find($client_id);
            Comment::commentAppendClient($worksheet_id, $client, 'Удалено контактное лицо');
        }
    }

    public static function makeUnionInWorksheet($worksheet_id, $client_id)
    {
        $client = Client::select('clients.*')
            ->leftJoin('worksheets', 'worksheets.client_id','clients.id')
            ->where('worksheets.id', $worksheet_id)
            ->first();

        $union = new ClientUnionRepository();
        $union->addUnion($client, $client_id);
    }
}
