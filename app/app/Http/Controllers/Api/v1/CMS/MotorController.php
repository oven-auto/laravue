<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motor;
use App\Http\Filters\MotorFilter;
use App\Repositories\Motor\MotorRepository;
use App\Http\Resources\Motor\MotorListCollection;
use App\Http\Resources\Motor\MotorEditResource;

class MotorController extends Controller
{
    private $repo;

    public function __construct(MotorRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $motors = $this->repo->getAll($request->all());
        return new MotorListCollection($motors);
    }

    public function edit(Motor $motor)
    {
        return new MotorEditResource($motor);
    }

    public function store(Motor $motor, Request $request)
    {
        $this->repo->save($motor, $request->input());
        return new MotorEditResource($motor);
    }

    public function update(Motor $motor, Request $request)
    {
        $this->repo->save($motor, $request->input());
        return new MotorEditResource($motor);
    }
}
