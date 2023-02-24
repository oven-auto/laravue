<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorDriver;
use App\Http\Resources\Motor\DriverListCollection;
use App\Http\Resources\Motor\DriverEditResource;

class MotorDriverController extends Controller
{
    public function index()
    {
        $motordrivers = MotorDriver::get();
        return new DriverListCollection($motordrivers);
    }

    public function edit(MotorDriver $motordriver)
    {
        return new DriverEditResource($motordriver);
    }

    public function store(MotorDriver $motordriver, Request $request)
    {
        $motordriver->fill($request->input())->save();
        return new DriverEditResource($motordriver);
    }

    public function update(MotorDriver $motordriver, Request $request)
    {
        $motordriver->fill($request->input())->save();
        return new DriverEditResource($motordriver);
    }


}
