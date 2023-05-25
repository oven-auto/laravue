<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class CheckClientController extends Controller
{
    public function check(Request $request)
    {
        $client = \App\Services\Client\CheckClient::check($request->all());

        return response()->json([
            'success' => 1,
            'data' => $client,
            'result' => $client ? 1 : 0,
        ]);
    }
}
