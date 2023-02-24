<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;
use App\Http\Resources\Color\ColorListCollection;
use App\Http\Resources\Color\ColorEditResource;

class ColorController extends Controller
{
    private $repo;

    public function __construct(\App\Repositories\Color\ColorRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $colors = $this->repo->filter($request->input());
        return new ColorListCollection($colors);
    }

    public function edit(Color $color)
    {
        return new ColorEditResource($color);
    }

    public function store(Color $color, Request $request)
    {
        $this->repo->save($color, $request->input());
        return new ColorEditResource($color);
    }

    public function update(Color $color, Request $request)
    {
        $this->repo->save($color, $request->input());
        return new ColorEditResource($color);
    }
}
