<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CountryFactory;
use App\Http\Resources\CountryFactory\CountryFactoryEditResource;
use App\Http\Resources\CountryFactory\CountryFactoryListCollection;

class CountryFactoryController extends Controller
{
    public function index()
    {
        $countryfactories = CountryFactory::get();
        return new CountryFactoryListCollection($countryfactories);
    }

    public function edit(CountryFactory $countryfactory)
    {
        return new CountryFactoryEditResource($countryfactory);
    }

    public function store(CountryFactory $countryfactory, Request $request)
    {
        $countryfactory->fill($request->input())->save();
        return new CountryFactoryEditResource($countryfactory);
    }

    public function update(CountryFactory $countryfactory, Request $request)
    {
        $countryfactory->fill($request->input())->save();
        return new CountryFactoryEditResource($countryfactory);
    }
}
