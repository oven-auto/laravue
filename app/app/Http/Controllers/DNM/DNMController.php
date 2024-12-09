<?php

namespace App\Http\Controllers\DNM;

use App\Classes\LadaDNM\DNMClientService;
use App\Http\Controllers\Controller;
use App\Models\WsmReserveNewCar;
use Illuminate\Http\Request;

class DNMController extends Controller
{
    public function index(WsmReserveNewCar $reserve, Request $request)
    {
        if(!$request->has('action'))
            return 0;

        match($request->action) {
            'client' => (new DNMClientService())->save($reserve),
            default => '',
        };
    }
}
