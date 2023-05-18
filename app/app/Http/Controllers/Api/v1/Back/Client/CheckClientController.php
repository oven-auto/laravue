<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class CheckClientController extends Controller
{
    public function check(Request $request)
    {
        $query = Client::select('clients.*');

        if($request->has('phone'))
            $query->leftJoin('client_phones','client_phones.client_id','clients.id')
                ->where('client_phones.phone', $request->phone);

        if($request->has('inn'))
            $query->leftJoin('client_inns','client_inns.client_id','clients.id')
                ->where('client_inns.number', $request->inn);

        $client = $query->first();

        $data = [];

        if($client)
            $data = [
                'type' => $client->type->name,
                'name' => $client->full_name,
                'id' => $client->id,
                'attribute' => $request->phone ? \StrHelp::phoneMask($request->phone) : $request->inn,
                'last_worksheet' => [
                    'id' => $client->latest_worksheet->id,
                    'created_at' => $client->latest_worksheet->created_date,
                    'salon' => $client->latest_worksheet->company->name,
                    'structure' => $client->latest_worksheet->structure->name,
                    'appeal' => $client->latest_worksheet->appeal->name,
                ],
            ];

        return response()->json([
            'success' => 1,
            'data' => $data,
            'result' => $client ? 1 : 0,
        ]);
    }
}
