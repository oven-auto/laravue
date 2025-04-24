<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use App\Services\Analytic\Report\FunnelService;
use Illuminate\Http\Request;

class SaleFunnelController extends Controller
{
    public function __construct(
        private FunnelService $service
    )
    {
        
    }



    /**
     * @OA\Get(
     *      path="/director/funnel",
     *      operationId="directorfunnel",
     *      tags={"Аналитика"},
     *      summary="Воронка продаж",
     *      description="Воронка продаж (intervals = [['01.04.2023', '31.12.2024'],['01.04.2023', '31.12.2024']])",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $result = $this->service->handle($request->intervals);

        $result = $this->service->format($result);

        return response()->json([
            'data' => $result,
            'success' => 1
        ]);
    }
}
