<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use \App\Repositories\Trafic\AppealRepository;

class TraficAppealController extends Controller
{
    public function index($id, AppealRepository $service)
    {
        $data = $service->getAppealByCompanyStructure($id);

        return response()->json([
            'data' => $data,
            'success' => 1,
        ]);
    }
}
