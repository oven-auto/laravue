<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorType;
use App\Http\Resources\Motor\TypeListCollection;
use App\Http\Resources\Motor\TypeEditResource;

class MotorTypeController extends Controller
{
    public function index()
    {
        $motortypes = MotorType::get();
        return new TypeListCollection($motortypes);
    }

    public function edit(MotorType $motortype)
    {
        return new TypeEditResource($motortype);
    }

    public function store(MotorType $motortype, Request $request)
    {
        $motortype->fill($request->input())->save();
        return new TypeEditResource($motortype);
    }

    public function update(MotorType $motortype, Request $request)
    {
        $motortype->fill($request->input())->save();
        return new TypeEditResource($motortype);
    }
}
