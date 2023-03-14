<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;
use Storage;
use App\Http\Resources\Mark\MarkListCollection;
use App\Http\Resources\Mark\MarkEditResource;
use  \App\Repositories\Mark\MarkRepository;

class MarkController extends Controller
{
    private $repo;

    public function __construct(MarkRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $marks = $this->repo->getAll($request->all());
        dd($marks);
        return new MarkListCollection($marks);
    }

    public function edit(Mark $mark, Request $request)
    {
        return new MarkEditResource($mark);
    }

    public function store(Mark $mark, Request $request)
    {
        $this->repo->saveMark($mark, $request->all());
        return new MarkEditResource($mark);
    }

    public function update(Mark $mark, Request $request)
    {
        $this->repo->saveMark($mark, $request->all());
        return new MarkEditResource($mark);
    }
}
