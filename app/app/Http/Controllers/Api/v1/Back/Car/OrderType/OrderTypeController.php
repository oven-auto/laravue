<?php

namespace App\Http\Controllers\Api\v1\Back\Car\OrderType;

use App\Http\Controllers\Controller;
use App\Models\OrderType;
use App\Http\Requests\Car\OrderType\OrderTypeRequest;
use App\Http\Resources\Car\OrderType\OrderTypeCollection;
use App\Http\Resources\Car\OrderType\OrderTypeItemResource;
use App\Repositories\Car\OrderType\OrderTypeRepository;
use Illuminate\Http\Request;

class OrderTypeController extends Controller
{
    private $repo;

    public function __construct(OrderTypeRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * ПОЛУЧИТЬ ВСЕ ТИПЫ ЗАКАЗА
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'trash' => 'sometimes'
        ]);

        $orderTypes = $this->repo->get($validated);

        return new OrderTypeCollection($orderTypes);
    }



    /**
     * СОЗДАТЬ ТИП ЗАКАЗА
     */
    public function store(OrderType $ordertype, OrderTypeRequest $request)
    {
        $this->repo->save($ordertype, $request->validated());

        return (new OrderTypeItemResource($ordertype))
            ->additional(['message' => 'Тип заказа создан']);
    }


    
    /** 
     * ИЗМЕНИТЬ ТИП ЗАКАЗА
    */
    public function update(OrderType $ordertype, OrderTypeRequest $request)
    {
        $this->repo->save($ordertype, $request->validated());

        return (new OrderTypeItemResource($ordertype))
            ->additional(['message' => 'Тип заказа изменен']);
    }



    /**
     * ПОЛУЧИТЬ ТИП ЗАКАЗА
     */
    public function show(OrderType $ordertype)
    {
        return (new OrderTypeItemResource($ordertype));
    }



    /**
     * УДАЛИТЬ ТИП ЗАКАЗА
     */
    public function delete(OrderType $ordertype)
    {
        $this->repo->delete($ordertype);

        return response()->json([
            'message' => 'Тип заказа удален',
            'success' => 1,
        ]);
    }



    /**
     * ВОСТАНОВИТЬ ТИП ЗАКАЗА
     */
    public function restore(OrderType $ordertype)
    {
        $this->repo->restore($ordertype);

        return response()->json([
            'message' => 'Тип заказа востановлен',
            'success' => 1,
        ]);
    }
}
