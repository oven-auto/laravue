<?php

namespace App\Http\Controllers\Api\v1\Back\Client\Union;

use App\Http\Controllers\Controller;
use App\Repositories\Client\ClientRepository;
use Illuminate\Http\Request;

class SearchClientController extends Controller
{
    public function search(Request $request, ClientRepository $repo)
    {
        $clients = $repo->paginate($request->input(), 50);
        return response()->json([
            'data' => $clients->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->full_name,
                    'attribute' => $item->client_type_id == 1 ? $item->phones->map(function($item){
                        return \StrHelp::phoneMask($item->phone);
                    }) : [$item->inn->number],
                ];
            }),
            'success' => 1
        ]);
    }
}
