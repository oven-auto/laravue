<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worksheet\Reserve\ContractSaveRequest;
use App\Http\Resources\Worksheet\Reserve\ContractResource;
use App\Models\WsmReserveNewCarContract;
use App\Repositories\Worksheet\Modules\Reserve\ReserveContractRepository;

class ContractController extends Controller
{
    private $repo;

    public function __construct(ReserveContractRepository $repo)
    {
        $this->repo = $repo;
    }



    public function store(WsmReserveNewCarContract $contract, ContractSaveRequest $request)
    {
        $this->repo->create($contract, $request->validated());

        return new ContractResource($contract);
    }



    public function update(WsmReserveNewCarContract $contract, ContractSaveRequest $request)
    {
        $this->repo->update($contract, $request->validated());

        return new ContractResource($contract);
    }



    public function show(WsmReserveNewCarContract $contract)
    {
        return new ContractResource($contract);
    }
}
