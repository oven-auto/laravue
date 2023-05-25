<?php

namespace App\Http\Controllers\Api\v1\Back\ServiceProduct;

use App\Http\Controllers\Controller;
use App\Models\ServiceProduct;
use App\Http\Resources\ServiceProduct\ServiceProductCollection;
use App\Http\Resources\ServiceProduct\ServiceProductSaveResource;
use App\Http\Requests\ServiceProduct\ServiceProductCreate;
use Illuminate\Http\Request;

class ServiceProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission.developer:product_list')->only('index');
        $this->middleware('permission.developer:product_edit')->only('update');
        $this->middleware('permission.developer:product_delete')->only('delete');
        $this->middleware('permission.developer:product_show')->only('show');
        $this->middleware('permission.developer:product_add')->only('store');
    }

    /**
     * Список продуктов.
     *
     * @return ServiceProductCollection
     */
    public function index(Request $request)
    {
        $query = ServiceProduct::select('service_products.*')->with(['appeal','group'])->orderBy('appeal_id')->orderBy('service_products.name');

        if($request->has('input') && $request->get('input') != '')
            $query->leftJoin('appeals','appeals.id','service_products.appeal_id')
                ->where('appeals.name', 'LIKE', "%$request->input%")
                ->orWhere('service_products.name','LIKE', "%$request->input%");

        if($request->has('appeal_id'))
            $query->where('appeal_id', $request->appeal_id);

        if($request->has('group_id'))
            $query->where('group_id', $request->group_id);

        $products = $query->get();
        return new ServiceProductCollection($products);
    }

    /**
     *Создать продукт.
     *
     * @param  ServiceProductCreate  $request
     * @return ServiceProductSaveResource
     */
    public function store(ServiceProduct $serviceproduct, ServiceProductCreate $request)
    {
        $data = $request->input();
        if($data['group_id'] === 0)
            unset($data['group_id']);
        $serviceproduct->fill($data)->save();
        return (new ServiceProductSaveResource($serviceproduct))->additional(['message' => 'Услуга добавлена']);
    }

    /**
     * Открыть продукт.
     *
     * @param  ServiceProduct $serviceproduct
     * @return ServiceProductSaveResource
     */
    public function show(ServiceProduct $serviceproduct)
    {
        return new ServiceProductSaveResource($serviceproduct);
    }

    /**
     * Изменить продукт.
     *
     * @param  ServiceProductCreate  $request
     * @param  ServiceProduct $serviceproduct
     * @return ServiceProductSaveResource
     */
    public function update(ServiceProduct $serviceproduct, ServiceProductCreate $request)
    {
        $data = $request->input();
        if($data['group_id'] === 0)
            unset($data['group_id']);
        $serviceproduct->fill($data)->save();
        return (new ServiceProductSaveResource($serviceproduct))
            ->additional(['message' => 'Услуга изменена']);
    }

    /**
     * Удалить продукт.
     *
     * @param  ServiceProduct $serviceproduct
     * @return ServiceProductSaveResource
     */
    public function destroy(ServiceProduct $serviceproduct)
    {
        $product = $serviceproduct->toArray();
        $serviceproduct->delete();
        return (new ServiceProductSaveResource((object)$product))
            ->additional(['message' => 'Услуга '.$product['name'].' удалена']);;
    }
}
