<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Http\Resources\Property\PropertyListCollection;
use App\Http\Resources\Property\PropertyEditResource;

class PropertyController extends Controller
{
    private $repo;

    public function __construct(\App\Repositories\Property\PropertyRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $properties = $this->repo->getAll(['sort' => 'sort']);
        return new PropertyListCollection($properties);
    }

    public function edit(Property $property)
    {
        return new PropertyEditResource($property);
    }

    public function store(Property $property, Request $request)
    {
        $this->repo->save($property, $request->input());
        return new PropertyEditResource($property);
    }

    public function update(Property $property, Request $request)
    {
        $this->repo->save($property, $request->input());
        return new PropertyEditResource($property);
    }
}
