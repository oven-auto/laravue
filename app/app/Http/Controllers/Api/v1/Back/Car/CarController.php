<?php

namespace App\Http\Controllers\Api\v1\Back\Car;

use App\Classes\Notice\Notice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Car\OverPrice\OverPriceRequest;
use App\Http\Requests\Car\CarCreateRequest;
use App\Http\Resources\Car\Car\CarItemResource;
use App\Http\Resources\Car\Car\CarListCollection;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Repositories\Car\Car\CarRepository;

class CarController extends Controller
{
    private $repo;

    public function __construct(CarRepository $repo)
    {
        $this->repo = $repo;
        
        $this->middleware('carfilter')->only('index');
    }



    /**
     * @OA\Get(
     *      path="/cars",
     *      operationId="carsList",
     *      tags={"Новый автомобиль"},
     *      summary="Список новых автомобилей",
     *      description="Список новых автомобилейв",
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CarFilter",
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="OK"
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $cars = $this->repo->paginate($request->all());
        
        return new CarListCollection($cars);
    }




    public function store(CarCreateRequest $request)
    {
        $car = $this->repo->store($request->validated());

        return (new CarItemResource($car))
            ->additional(['message' => Notice::getMessages()]);
    }



    /**
     * @OA\Patch(
     *      path="/cars/{id}",
     *      operationId="updateCar",
     *      tags={"Новый автомобиль"},
     *      summary="Изменить новый автомобиль",
     *      description="Изменить новый автомобиль",
     *      @OA\Parameter(
     *          name="id",
     *          description="Идентификатор кузова",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CarCreateRequest",
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="OK"
     *      )
     * )
     */
    public function update(Car $car, CarCreateRequest $request)
    {
        $this->repo->update($car, $request->validated());

        return (new CarItemResource($car))
            ->additional(['message' => Notice::getMessages()]);
    }



    /**
     * @OA\Get(
     *      path="/cars/{id}",
     *      operationId="getCar",
     *      tags={"Новый автомобиль"},
     *      summary="Открыть карточку нового автомобиля",
     *      description="Открыть карточку нового автомобиля",
     *      @OA\Parameter(
     *          name="id",
     *          description="Идентификатор автомобиля",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     * )
     */
    public function show(Car $car)
    {
        return new CarItemResource($car);
    }



        /**
     * @OA\Post(
     *      path="/cars/overprice/{id}",
     *      operationId="storeCarOverPrice",
     *      tags={"Новый автомобиль"},
     *      summary="Добавить/Изменить стоимость переоценки автомобиля (воздух)",
     *      description="Добавить/Изменить стоимость переоценки автомобиля (воздух)",
     *      @OA\RequestBody(
     *          required=true,
     *          description="price",
     *          @OA\JsonContent(
     *              required={"price"},
     *              @OA\Property(property="price", type="integer", format="integer", example="10000")
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          description="Идентификатор автомобиля",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     * )
     */
    public function makeOverPrice(Car $car, OverPriceRequest $request)
    {
        $this->repo->saveOverPrice($car, $request->price);

        return (new CarItemResource($car))
            ->additional(['message' => 'Дооценка зарегестрирована']);
    }



    /**
     * @OA\Get(
     *      path="/cars/overprice/{id}",
     *      operationId="getCarOverPrice",
     *      tags={"Новый автомобиль"},
     *      summary="Получить стоимость переоценки автомобиля (воздух)",
     *      description="Получить стоимость переоценки автомобиля (воздух)",
     *      @OA\Parameter(
     *          name="id",
     *          description="Идентификатор автомобиля",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     * )
     */
    public function getOverPrice(Car $car)
    {
        return response()->json([
            'data' => [
                'price' => $car->over_price->id ? $car->over_price->price : '',
                'author' => $car->over_price->id ? $car->over_price->author->cut_name : '',
                'date' => $car->over_price->id ? $car->over_price->updated_at->format('d.m.Y (H:i)') : '',
            ],
            'success' => 1,
        ]);
    }



    /**
     * @OA\Get(
     *      path="/cars/{id}/history",
     *      operationId="getCarHistory",
     *      tags={"Новый автомобиль"},
     *      summary="Получить историю автомобиля",
     *      description="Получить историю автомобиля",
     *      @OA\Parameter(
     *          name="id",
     *          description="Идентификатор автомобиля",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     * )
     */
    public function history(Car $car)
    {
        return response()->json([
            'data' => $car->history->map(function($item){
                return [
                    'author' => $item->author->cut_name,
                    'created_at' => $item->created_at->format('d.m.Y (H:i)'),
                    'comment' => $item->comment,
                    'type' => $item->type,
                ];
            }),
            'success' => 1,
        ]);
    }



        /**
     * @OA\Get(
     *      path="/cars/{id}/tuning",
     *      operationId="getCarTuning",
     *      tags={"Новый автомобиль"},
     *      summary="Получить установленный тюнинг на автомобиле",
     *      description="Получить установленный тюнинг на автомобиле",
     *      @OA\Parameter(
     *          name="id",
     *          description="Идентификатор автомобиля",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     * )
     */
    public function tuning(Car $car)
    {
        return response()->json([
            'data' => $car->tuning->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'trash' => $item->deleted_at ? 1 : 0,
                ];
            }),
            'success' => 1,
        ]);
    }
}
