<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Modules\Redemption\RedemptionStoreRequest;
use App\Http\Resources\Worksheet\Link\LinkResource;
use App\Http\Resources\Worksheet\Modules\RedemptionCollection;
use App\Http\Resources\Worksheet\Modules\RedemptionListResource;
use App\Http\Resources\Worksheet\Modules\RedemptionResource;
use App\Models\Worksheet;
use App\Models\WSMRedemptionCar;
use App\Repositories\Worksheet\Modules\Redemption\RedemptionRepository;
use Illuminate\Http\Request;
use App\Services\GetShortCutFromURL\GetShortCutFromURL;

class RedemptionController extends Controller
{
    protected $repo;

    public function __construct(RedemptionRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * ПОЛУЧИТЬ СПИСОК ОЦЕНОК
     * @param int $worksheet
     * @param Request $request
     * @return mixed RedemptionCollection|RedemptionListResource
     */
    public function index(Request $request, int $worksheet = 0 )
    {
        if($worksheet)
        {
            $redemptions = $this->repo->get($worksheet);
            return new RedemptionCollection($redemptions);
        }
        else
        {
            $redemptions = $this->repo->paginate($request->all());
            return (RedemptionListResource::collection($redemptions))->additional(['success' => 1]);
        }
    }



    /**
     * СОЗДАТЬ ОЦЕНКУ
     * @param Worksheet $worksheet
     * @param RedemptionStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Worksheet $worksheet, RedemptionStoreRequest $request) : \Illuminate\Http\JsonResponse
    {
        $redemption = $this->repo->store($worksheet, $request->all());

        return response()->json([
            'data' => new RedemptionResource($redemption),
            'success' => 1,
            'message' => 'Оценка создана'
        ]);
    }



    /**
     * СОХРАНИТЬ СВОДНЫЕ ДАННЫЕ ОЦЕНКИ (РАСЧЕТНАЯ ЦЕНА, ПРЕДЛОЖЕНИЕ, ФАКТ)
     * @param WSMRedemptionCar $redemption
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveprice(WSMRedemptionCar $redemption, Request $request) : \Illuminate\Http\JsonResponse
    {
        $this->repo->save($redemption, $request->all());

        $redemption->fresh();

        return response()->json([
            'data' => new RedemptionResource($redemption),
            'success' => 1,
            'message' => 'Оценка изменена'
        ]);
    }



    /**
     * ИЗМЕНИТЬ ОЦЕНКУ
     * @param Worksheet $worksheet
     * @param RedemptionStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WSMRedemptionCar $redemption, Request $request) : \Illuminate\Http\JsonResponse
    {
        $this->repo->update($redemption, $request->all());

        return response()->json([
            'data' => new RedemptionResource($redemption),
            'success' => 1,
            'message' => 'Оценка изменена'
        ]);
    }



    /**
     * СОХРАНИТЬ ССЫЛКУ
     * @param Worksheet $worksheet
     * @param RedemptionStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storelink(WSMRedemptionCar $redemption, Request $request) : \Illuminate\Http\JsonResponse
    {
        $link = $this->repo->saveLink($redemption, $request->all());

        return response()->json([
            'data' => new LinkResource($link),
            'success' => 1,
            'message' => 'Ссылка добавлена'
        ]);
    }



    /**
     * ПЕРЕНЕСТИ АВТОМОБИЛЬ ОЦЕНКИ НА СКЛАД, И ЗАКРЫТЬ ОЦЕНКУ, ПРИСВОИТЬ СТАТУС НА СКЛАДЕ
     * @param Worksheet $worksheet
     * @return \Illuminate\Http\JsonResponse
     */
    public function buy(WSMRedemptionCar $redemption) : \Illuminate\Http\JsonResponse
    {
        $this->repo->buyCar($redemption);

        return response()->json([
            'data' => new RedemptionResource($redemption),
            'success' => 1,
            'message' => 'Оприходован на склад'
        ]);
    }



    /**
     * ЗАКРЫТЬ ОЦЕНКУ СО СТАТУСОМ ЗАКРЫТО, АВТОМОБИЛЬ НЕ ВЫКУПЛЕН
     * @param Worksheet $worksheet
     * @return \Illuminate\Http\JsonResponse
     */
    public function close(WSMRedemptionCar $redemption) : \Illuminate\Http\JsonResponse
    {
        $this->repo->close($redemption);

        return response()->json([
            'data' => new RedemptionResource($redemption),
            'success' => 1,
            'message' => 'Оценка завершена'
        ]);
    }



    /**
     * ПОЛУЧИТЬ СПИСОК ВСЕХ ССЫЛОК ЗАДАНОЙ ОЦЕНКИ
     * @param Worksheet $worksheet
     * @return \Illuminate\Http\JsonResponse
     */
    public function links(WSMRedemptionCar $redemption)
    {
        return response()->json([
            'data' => $redemption->links,
            'success' => 1
        ]);
    }



    /**
     * ПОЛУЧИТЬ КОЛ-ВО ОЦЕНОК ПО ПАРАМЕТРАМ
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function counter(Request $request)
    {
        $count = $this->repo->count($request->all());

        return response()->json([
            'data' => $count,
            'success' => 1,
        ]);
    }
}
