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
     */
    public function index(int $worksheet = 0, Request $request)
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
     */
    public function store(Worksheet $worksheet, RedemptionStoreRequest $request)
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
     */
    public function saveprice(WSMRedemptionCar $redemption, Request $request)
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
     */
    public function update(WSMRedemptionCar $redemption, Request $request)
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
     */
    public function storelink(WSMRedemptionCar $redemption, Request $request)
    {
        $link = $this->repo->saveLink($redemption, $request->all());

        return response()->json([
            'data' => new LinkResource($link),
            'success' => 1,
            'message' => 'Ссылка добавлена'
        ]);
    }

    public function buy(WSMRedemptionCar $redemption)
    {
        $this->repo->buyCar($redemption);

        return response()->json([
            'data' => new RedemptionResource($redemption),
            'success' => 1,
            'message' => 'Оприходован на склад'
        ]);
    }

    /**
     * ЗАКРЫТЬ ОЦЕНКУ
     */
    public function close(WSMRedemptionCar $redemption)
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
     */
    public function links(WSMRedemptionCar $redemption)
    {
        return response()->json([
            'data' => $redemption->links,
            'success' => 1
        ]);
    }

    /**
     * ПОЛУЧИТЬ КОЛ-ВО ОЦЕНОК ПО ПААМЕТРАМ
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
