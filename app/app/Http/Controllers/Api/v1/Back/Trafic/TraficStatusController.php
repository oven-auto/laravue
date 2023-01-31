<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TraficStatusController extends Controller
{
    public function index()
    {
        $data = \App\Models\TraficStatus::get();
        $result = $data->map(function($item){
            return [
                'name' => $item->description,
                'id' => $item->id,
            ];
        });

        return \response()->json([
            'data' => $result,
            'success' => '1',
        ]);
    }
}
