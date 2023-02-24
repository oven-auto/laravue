<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;
use \App\Repositories\Complectation\ComplectationRepository;
use App\Http\Resources\Complectation\ComplectationListCollection;
use App\Http\Resources\Complectation\ComplectationEditResource;

class ComplectController extends Controller
{
    public function __construct(ComplectationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $complectations = $this->repo->filter($request->all());
        return new ComplectationListCollection($complectations);
    }

    public function edit(Complectation $complectation)
    {
        return new ComplectationEditResource($complectation);
    }

    public function store(Complectation $complectation, Request $request)
    {
        $result = $this->repo->save($complectation, $request->all());
        return new ComplectationEditResource($complectation);
    }

    public function update(Complectation $complectation, Request $request)
    {
        $this->repo->save($complectation, $request->input());
        return new ComplectationEditResource($complectation);
    }
}
