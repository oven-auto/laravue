<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class TraficUserController extends Controller
{
    public function index($structure_id)
    {
        $users = User::ByStructure($structure_id)->get();

        $data = [];

        foreach($users as $itemUser)
            $data[] = [
                'id'=>$itemUser->id,
                'name' => $itemUser->cut_name
            ];

        return \response()->json([
            'data' =>  $data,
            'success' => 1,
        ]);
    }
}
