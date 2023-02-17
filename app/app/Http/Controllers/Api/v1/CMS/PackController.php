<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;
use App\Http\Filters\PackFilter;
use App\Http\Resources\Pack\PackListCollection;
use App\Http\Resources\Pack\PackEditResource;
use App\Repositories\Pack\PackRepository;

class PackController extends Controller
{
    private $repo;

    public function __construct(PackRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $packs = $this->repo->filter($request->all());
        return new PackListCollection($packs);
    }

    public function edit(Pack $pack)
    {
        return new PackEditResource($pack);
    }

    public function store(Pack $pack, Request $request)
    {
        $this->repo->save($pack, $request->all());
        return new PackEditResource($pack);
    }

    public function update(Pack $pack, Request $request)
    {
        $this->repo->save($pack, $request->all());
        return new PackEditResource($pack);
    }

    public function destroy(Pack $pack)
    {
        return $this->repo->destroy($pack);
    }
}
