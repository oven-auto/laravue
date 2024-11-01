<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Complectation;

use App\Http\Controllers\Controller;
use App\Http\Resources\Car\Complectation\ComplectationListResource;
use App\Http\Resources\Car\Factory\FactorySaveResource;
use App\Models\Complectation;
use App\Repositories\Car\Complectation\ComplectationRepository;
use Illuminate\Http\Request;

class ComplectationListController extends Controller
{
    private $repo;

    public function __construct(ComplectationRepository $repo)
    {
        $this->repo = $repo;
    }
    /**
     * @OA\Get(
     *  path="/complectationlist",
     *  tags={"Журнал комплектации"},
     *  operationId="getcomplectationlist",
     *  summary="Журнал комплектации",
     *  description="Журнал комплектации",
     *  @OA\Response(
     *      response=200,
     *      description="OK"
     *  )
     * )
     */
    public function index(Request $request)
    {
        $complectations = $this->repo->list($request->all());
        
        return response()->json([
            'data' => ComplectationListResource::collection($complectations),
            'success' => 1,
        ]);
    }



    public function count(Request $request)
    {
        $count = $this->repo->count($request->all());

        return response()->json([
            'count' =>$count,
            'success' => 1,
        ]);
    }
}
