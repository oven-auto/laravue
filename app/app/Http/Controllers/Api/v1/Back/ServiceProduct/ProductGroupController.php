<?php

namespace App\Http\Controllers\Api\v1\Back\ServiceProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductGroup;
use App\Http\Resources\ServiceProduct\GroupCollection;
use App\Http\Resources\ServiceProduct\GroupSaveResource;
use App\Http\Requests\ServiceProduct\ProductGroupCreate;

class ProductGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission.developer:product_group_list')->only('index');
        $this->middleware('permission.developer:product_group_edit')->only('update');
        $this->middleware('permission.developer:product_group_delete')->only('delete');
        $this->middleware('permission.developer:product_group_show')->only('show');
        $this->middleware('permission.developer:product_group_add')->only('store');
    }

    /**
     * Список групп продуктов.
     *
     * @return GroupCollection
     */
    public function index() : GroupCollection
    {
        $group = ProductGroup::orderBy('sort')->get();
        return new GroupCollection($group);
    }

    /**
     * Создать группу продукта.
     *
     * @param  ProductGroupCreate  $request
     * @return GroupSaveResource
     */
    public function store(ProductGroup $productgroup, ProductGroupCreate $request) : GroupSaveResource
    {
        $productgroup->fill($request->input())->save();
        return (new GroupSaveResource($productgroup))->additional(['message' => 'Группа добавлена']);
    }

    /**
     * Открыть группу продукта.
     *
     * @param  ProductGroup $productgroup
     * @return GroupSaveResource
     */
    public function show(ProductGroup $productgroup) : GroupSaveResource
    {
        return new GroupSaveResource($productgroup);
    }

    /**
     * Изменить группу продукта.
     *
     * @param  ProductGroupCreate  $request
     * @param  ProductGroup $productgroup
     * @return GroupSaveResource
     */
    public function update(ProductGroup $productgroup, ProductGroupCreate $request) : GroupSaveResource
    {
        $productgroup->fill($request->input())->save();
        return (new GroupSaveResource($productgroup))->additional(['message' => 'Группа изменена']);
    }

    /**
     * Удалить группу продукта.
     *
     * @param  ProductGroup $productgroup
     * @return GroupSaveResource
     */
    public function destroy(ProductGroup $productgroup) : GroupSaveResource
    {
        $group = $productgroup->toArray();
        $productgroup->delete();
        return (new GroupSaveResource((object) $group))
            ->additional(['message' => 'Группа '.$group['name'].' удалена']);
    }
}
