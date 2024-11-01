<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Trafic;
use App\Services\Client\CheckClient;
use App\Services\Worksheet\WorksheetCheck;
use Illuminate\Http\Request;

class CheckClientController extends Controller
{
    public function check(Request $request)
    {
        $data = CheckClient::check($request->all());

        return response()->json([
            'success' => 1,
            'data' => $data,
            'result' => $data['client'] ? 1 : 0,
        ]);
    }
}
