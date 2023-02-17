<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotorTransmission;
use App\Http\Resources\Motor\TransmissionListCollection;
use App\Http\Resources\Motor\TransmissionEditResource;

class MotorTransmissionController extends Controller
{
    public function index()
    {
        $motortransmissions = MotorTransmission::get();
        return new TransmissionListCollection($motortransmissions);
    }

    public function edit(MotorTransmission $motortransmission)
    {
        return new TransmissionEditResource($motortransmission);
    }

    public function store(MotorTransmission $motortransmission, Request $request)
    {
        $motortransmission->fill($request->input())->save();
        return new TransmissionEditResource($motortransmission);
    }

    public function update(MotorTransmission $motortransmission, Request $request)
    {
        $motortransmission->fill($request->input())->save();
        return new TransmissionEditResource($motortransmission);
    }
}
