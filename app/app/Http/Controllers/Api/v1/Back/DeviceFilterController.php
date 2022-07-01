<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceFilter;
use App\Http\Resources\Device\Filter\FilterListCollection;
use App\Http\Resources\Device\Filter\FilterEditResource;
use App\Repositories\Device\FilterRepository;

class DeviceFilterController extends Controller
{
    private $repo;

    public function __construct(FilterRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $devicefilters =  $this->repo->getAllSort();
        return new FilterListCollection($devicefilters);
    }

    public function store(DeviceFilter $devicefilter, Request $request)
    {
        $this->repo->save($devicefilter, $request->all());
        return new FilterEditResource($devicefilter);
    }

    public function edit(DeviceFilter $devicefilter)
    {
        return new FilterEditResource($devicefilter);
    }

    public function update(DeviceFilter $devicefilter, Request $request)
    {
        $this->repo->save($devicefilter, $request->all());
        return new FilterEditResource($devicefilter);
    }

    public function destroy(DeviceFilter $devicefilter)
    {
        return $this->repo->delete($devicefilter);
    }
}
