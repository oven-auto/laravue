<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChangeClientController extends Controller
{
    public function change(Request $request)
    {
        $worksheetId = $request->get('worksheet_id');//id рабочего листа
        $clientId = $request->get('client_id');//id контактного лица

        if(!$worksheetId || !$clientId)
            throw new \Exception('Параметры заданы не верно');

        $worksheet = \App\Models\Worksheet::findOrFail($worksheetId);

        $oldClientId = $worksheet->client->id;

        $worksheet->client_id = $clientId;
        $worksheet->save();


        $arr = $worksheet->subclients->pluck('id')->toArray();
        array_push($arr, $oldClientId);
        $arr = array_filter($arr, function($item) use ($clientId){
            if($item != $clientId)
                return $item;
        });
        $worksheet->subclients()->sync($arr);
        $worksheet->fresh();

        $worksheet->load('client');
        $worksheet->load('subclients');

        return response()->json([
            'data' => [
                'client' => [
                    'id' => $worksheet->client->id,
                    'name' => $worksheet->client->full_name,
                ],
                'subclient' => $worksheet->subclients->map(function($item){
                    return [
                        'id' => $item->id,
                        'name' => $item->full_name,
                    ];
                }),
            ],
            'success' => 1,
            'message' => 'Клиент изменен'
        ]);
    }
}
