<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Repositories\Device\DeviceRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Http\Resources\Device\DeviceListCollection;
use App\Http\Resources\Device\DeviceEditResource;


class DeviceController extends Controller
{
    private $repo;

    public function __construct(DeviceRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $devices = $this->repo->getAll($request->all());
        return new DeviceListCollection($devices);
    }

    public function store(Device $device, Request $request)
    {
        $device = $this->repo->save($device, $request->all());
        return new DeviceEditResource($device);
    }

    public function edit(Device $device)
    {
        return new DeviceEditResource($device);
    }

    public function update(Device $device, Request $request)
    {
        $device = $this->repo->save($device, $request->all());
        return new DeviceEditResource($device);
    }

    public function destroy(Device $device)
    {
        return $this->repo->delete($device);
    }
}
