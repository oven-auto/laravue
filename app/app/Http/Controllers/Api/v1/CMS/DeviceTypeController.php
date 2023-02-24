<?php

namespace App\Http\Controllers\Api\v1\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceType;
use App\Http\Resources\Device\Type\TypeListCollection;
use App\Http\Resources\Device\Type\TypeEditResource;
use App\Repositories\Device\TypeRepository;

class DeviceTypeController extends Controller
{
    private $repo;

    public function __construct(TypeRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $types = $this->repo->getAllSort();
        return new TypeListCollection($types);
    }

    public function store(DeviceType $devicetype, Request $request)
    {
        $this->repo->save($devicetype, $request->all());
        return new TypeEditResource($devicetype);
    }

    public function edit(DeviceType $devicetype)
    {
        return new TypeEditResource($devicetype);
    }

    public function update(DeviceType $devicetype, Request $request)
    {
        $this->repo->save($devicetype, $request->all());
        return new TypeEditResource($devicetype);
    }

    public function destroy(DeviceType $devicetype)
    {
        return $this->repo->delete($devicetype);
    }
}
