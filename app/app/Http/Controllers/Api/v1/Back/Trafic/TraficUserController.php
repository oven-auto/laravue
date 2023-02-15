<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\User\UserRepository;

class TraficUserController extends Controller
{
    public function index($structure_id, $appeal_id, UserRepository $service)
    {
        $data = $service->getListWithCoutTrafic($structure_id, $appeal_id);

        return \response()->json([
            'data' =>  $data,
            'success' => 1,
        ]);
    }
}
