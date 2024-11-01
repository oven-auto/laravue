<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Factory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Factory\FactorySaveRequest;
use App\Http\Resources\Car\Factory\FactorySaveResource;
use App\Models\Factory;
use App\Repositories\Car\Factory\FactoryRepository;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    private $repo;

    public function __construct(FactoryRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Request $request)
    {
        $factories = $this->repo->get($request->all());

        return response()->json([
            'data' => $factories,
            'success' => 1,
        ]);
    }



    public function store(Factory $factory, FactorySaveRequest $request)
    {
        $this->repo->save($factory, $request->validated());

        return (new FactorySaveResource($factory))->additional(['message' => 'Фабрика создана']);
    }



    public function update(Factory $factory, FactorySaveRequest $request)
    {
        $this->repo->save($factory, $request->validated());

        return (new FactorySaveResource($factory))->additional(['message' => 'Фабрика изменена']);
    }



    public function show(Factory $factory)
    {
        return (new FactorySaveResource($factory));
    }



    public function destroy(Factory $factory)
    {
        $this->repo->delete($factory);

        return response()->json([
            'success' => 1,
            'message' => 'Фабрика удалена'
        ]);
    }



    public function revert(Factory $factory)
    {
        $this->repo->restore($factory);

        return response()->json([
            'success' => 1,
            'message' => 'Фабрика актуальна'
        ]);
    }
}
