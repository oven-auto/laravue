<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientEventCountController extends Controller
{
    public function __invoke(Request $request, \App\Repositories\Client\ClientEventRepository $repo)
    {
        $count = $repo->counter($request->input());
        return response()->json([
            'data' => $count,
            'success' => 1
        ]);
    }
}
