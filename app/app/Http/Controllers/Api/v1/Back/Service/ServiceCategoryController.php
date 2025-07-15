<?php

namespace App\Http\Controllers\Api\v1\Back\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\ServiceCategoryRequest;
use App\Http\Resources\Services\ServiceCategoryResource;
use App\Repositories\Services\ServiceCategoryRepository;

class ServiceCategoryController extends Controller
{
    public function __construct(
        private ServiceCategoryRepository $repo
    )
    {
        
    }


    
    /**
     * @OA\Get(
     *      path="/finservices/categories",
     *      operationId="getfinservicescategories",
     *      tags={"Финансовые сервисы"},
     *      summary="Категории список",
     *      description="Категории список",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function index()
    {
        $categories = $this->repo->getAll();

        return ServiceCategoryResource::collection($categories);
    }



        /**
     * @OA\Post(
     *      path="/finservices/categories",
     *      operationId="storefinservicescategories",
     *      tags={"Финансовые сервисы"},
     *      summary="Создать Категории список",
     *      description="Создать Категории список",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function store(ServiceCategoryRequest $request)
    {
        $category = $this->repo->create($request->validated());

        return new ServiceCategoryResource($category);
    }



            /**
     * @OA\Post(
     *      path="/finservices/categories/{id}",
     *      operationId="updatefinservicescategories",
     *      tags={"Финансовые сервисы"},
     *      summary="Изменить Категории список",
     *      description="Изменить Категории список",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function update(int $id, ServiceCategoryRequest $request)
    {
        $category = $this->repo->update($id, $request->validated());

        return new ServiceCategoryResource($category);
    }



     /**
     * @OA\Get(
     *      path="/finservices/categories/{id}",
     *      operationId="showfinservicescategories",
     *      tags={"Финансовые сервисы"},
     *      summary="Открыть Категории список",
     *      description="Открыть Категории список",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function show(int $id)
    {
        $category = $this->repo->getById($id);

        return new ServiceCategoryResource($category);
    }
}
