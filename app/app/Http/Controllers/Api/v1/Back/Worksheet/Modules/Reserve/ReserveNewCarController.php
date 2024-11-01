<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Reserve\ReserveInWorksheetRequest;
use App\Http\Requests\Worksheet\Reserve\ReserveStoreRequest;
use App\Http\Requests\Worksheet\Reserve\ReserveUpdateRequest;
use App\Http\Resources\Worksheet\Reserve\ReserveSaveResource;
use App\Models\WsmReserveNewCar;
use App\Repositories\Worksheet\Modules\Reserve\ReserveRepository;
use Illuminate\Http\Request;

class ReserveNewCarController extends Controller
{
    private $repo;

    public function __construct(ReserveRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(ReserveInWorksheetRequest $request)
    {
        $reserves = $this->repo->getReservesInWorksheet($request->worksheet_id);

        return response()->json([
            'data' => ReserveSaveResource::collection($reserves),
            'success' => 1,
        ]);
    }



    public function store(ReserveStoreRequest $request)
    {
        $reserve = $this->repo->createReserve($request->validated());

        return response()->json([
            'data' =>  new ReserveSaveResource($reserve),
            'success' => 1,
            'message' => 'Резерв создан',
        ]);
    }



    public function update(WsmReserveNewCar $reserve, ReserveUpdateRequest $request)
    {
        $this->repo->changeCarInReserve($reserve, $request->validated());

        return response()->json([
            'data' =>  new ReserveSaveResource($reserve),
            'success' => 1,
            'message' => 'Резерв изменен',
        ]);
    }



    public function destroy(WsmReserveNewCar $reserve)
    {
        $this->repo->deleteReserve($reserve);

        return response()->json([
            'message' =>  'Резерв снят',
            'success' => 1
        ]);
    }



    public function setdate(WsmReserveNewCar $reserve, Request $request)
    {
        $validated = $request->validate([
            'date_at' => 'required|date_format:d.m.Y',
            'type' => 'required|in:sale,issue',
            'decorator_id' => 'required'
        ]);

        $this->repo->saveDealDate($reserve, $validated);

        return response()->json([
            'data' =>  new ReserveSaveResource($reserve),
            'message' => 'Данные успешно добавлены',
            'success' => 1
        ]);
    }



    /**
     * @OA\Delete(
     *  path="/worksheet/modules/reserves/setdates/{id}",
     *  tags={"Резерв нового автомобиля"},
     *  operationId="deleteDealDate",
     *  summary="Удалить дату сделки",
     *  description="Удалить дату сделки",
     *  @OA\Parameter(
     *      name="id",
     *      description="Идентификатор резерва",
     *      required=true,
     *      in="path",
     *      @OA\Schema(
     *          type="integer"
     *      )
     *  ),
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request Body Description",
     *      @OA\JsonContent(
     *           @OA\Property(
     *              property="type", 
     *              type="string", 
     *              format="string", 
     *              description="Тип даты: sale|issue",
     *              enum={"sale","issue"}
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="OK"
     *  )
     * )
     */
    public function deletedate(WsmReserveNewCar $reserve, Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:sale,issue',
        ]);

        $this->repo->deleteDealDate($reserve, $validated);

        return response()->json([
            'data' =>  new ReserveSaveResource($reserve),
            'success' => 1
        ]);
    }
}
