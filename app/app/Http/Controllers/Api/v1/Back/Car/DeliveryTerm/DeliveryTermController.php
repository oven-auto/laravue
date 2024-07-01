<?php

namespace App\Http\Controllers\Api\v1\Back\Car\DeliveryTerm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\OrderType\OrderTypeRequest;
use App\Repositories\Car\DeliveryTerm\DeliveryTermRepository;
use App\Http\Resources\Car\OrderType\OrderTypeCollection;
use App\Http\Resources\Car\OrderType\OrderTypeItemResource;
use App\Models\DeliveryTerm;
use Illuminate\Http\Request;

class DeliveryTermController extends Controller
{
    private $repo;

    public function __construct(DeliveryTermRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * ПОЛУЧИТЬ ВСЕ УСЛОВИЯ ДОСТАВКИ
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'trash' => 'sometimes'
        ]);

        $terms = $this->repo->get($validated);

        return new OrderTypeCollection($terms);
    }



    /**
     * СОЗДАТЬ УСЛОВИЕ ДОСТАВКИ
     */
    public function store(DeliveryTerm $deliveryterm, OrderTypeRequest $request)
    {
        $this->repo->save($deliveryterm, $request->validated());

        return (new OrderTypeItemResource($deliveryterm))
            ->additional(['message' => 'Условие доставки создано']);
    }


    
    /** 
     * ИЗМЕНИТЬ УСЛОВИЕ ДОСТАВКИ
    */
    public function update(DeliveryTerm $deliveryterm, OrderTypeRequest $request)
    {
        $this->repo->save($deliveryterm, $request->validated());

        return (new OrderTypeItemResource($deliveryterm))
            ->additional(['message' => 'Условие доставки изменено']);
    }



    /**
     * ПОЛУЧИТЬ УСЛОВИЕ ДОСТАВКИ
     */
    public function show(DeliveryTerm $deliveryterm)
    {
        return (new OrderTypeItemResource($deliveryterm));
    }



    /**
     * УДАЛИТЬ УСЛОВИЕ ДОСТАВКИ
     */
    public function delete(DeliveryTerm $deliveryterm)
    {
        $this->repo->delete($deliveryterm);

        return response()->json([
            'message' => 'Условие доставки удалено',
            'success' => 1,
        ]);
    }



    /**
     * ВОСТАНОВИТЬ УСЛОВИЕ ДОСТАВКИ
     */
    public function restore(DeliveryTerm $deliveryterm)
    {
        $this->repo->restore($deliveryterm);

        return response()->json([
            'message' => 'Условие доставки востановлено',
            'success' => 1,
        ]);
    }

}
