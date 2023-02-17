<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Resources\Brand\BrandEditResource;
use App\Http\Resources\Brand\BrandEditCollectionResource;
use App\Repositories\Brand\BrandRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Brand\BrandCreateRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;

class BrandController extends Controller
{
    private $repo;

    public function __construct(BrandRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $brands = $this->repo->getAll();
        return new BrandEditCollectionResource($brands);
    }

    public function store(Brand $brand, BrandCreateRequest $request)
    {
        $brand = $this->repo->save($brand, $request->all());
        return new BrandEditResource($brand);
    }

    public function edit(Brand $brand)
    {
        return new BrandEditResource($brand);
    }

    public function update(Brand $brand, BrandUpdateRequest $request)
    {
        $brand = $this->repo->save($brand, $request->all());
        return new BrandEditResource($brand);
    }

    public function destroy($id)
    {

    }
}
