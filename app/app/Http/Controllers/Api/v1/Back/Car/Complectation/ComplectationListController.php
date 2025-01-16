<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Complectation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Complectation\ComplectationListRequest;
use App\Http\Resources\Car\Complectation\ComplectationListResource;
use App\Models\ComplectationPrice;
use App\Repositories\Car\Complectation\ComplectationRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     *  @OA\RequestBody(
     *     @OA\JsonContent(
     *         type="object",
     *         ref="#/components/schemas/ComplectationListRequest",
     *     )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="OK"
     *  )
     * )
     */
    public function index(ComplectationListRequest $request)
    {
        $complectations = $this->repo->list($request->all());
        
        $date = (new Carbon(ComplectationPrice::select(DB::raw('max(begin_at) as date'))->first()->date))->format('d.m.Y');

        return response()->json([
            'data' => ComplectationListResource::collection($complectations),
            'last_price' => $date,
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
