<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BodyWork;
use App\Http\Resources\BodyWork\BodyWorkListCollection;
use App\Http\Resources\BodyWork\BodyWorkEditResource;

class BodyWorkController extends Controller
{
    public function index()
    {
        $bodyworks = BodyWork::get();
        return new BodyWorkListCollection($bodyworks);
    }

    public function edit(BodyWork $bodywork)
    {
        return new BodyWorkEditResource($bodywork);
    }

    public function store(BodyWork $bodywork, Request $request)
    {
        $bodywork->fill($request->input())->save();
        return new BodyWorkEditResource($bodywork);
    }

    public function update(BodyWork $bodywork, Request $request)
    {
        $bodywork->fill($request->input())->save();
        return new BodyWorkEditResource($bodywork);
    }
}
