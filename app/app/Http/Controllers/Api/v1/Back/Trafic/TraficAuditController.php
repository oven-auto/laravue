<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trafic;
use App\Http\Requests\Trafic\TraficProcessing as TPRequest;
use App\Models\TraficProcessing;

class TraficAuditController extends Controller
{
    public $loadService;

    public function __construct(\App\Repositories\Trafic\TraficAuditRepository $service)
    {
        $this->repository = $service;
    }

    public function load(Trafic $trafic, TPRequest $request)
    {
        $this->repository->saveTraficAudit($trafic, $request->input(),$request->allFiles());

        return response()->json([
            'data' => $trafic->processing->map(function($item){
                return [
                    'scenario' => $item->standart->name,
                    'user' => $item->user->cut_name,
                    'record' => $item->getFile('record'),
                    'audit' => $item->getFile('audit'),
                    'result' => $item->procent,
                    'created_at' => !empty($item->created_at) ? $item->created_at->format('d.m.Y (H:i)') : '',
                    'status' => $item->status_result,
                ];
            }),
            'success' => 1,
            'message' => 'Аудит сохранен',
        ]);
    }

    public function show( TraficProcessing $trafic_processing)
    {
        return response()->json([
            'data' => [
                'record' => $trafic_processing->getFile('record'),
                'audit' => $trafic_processing->getFile('audit'),
                'result' => $trafic_processing->result
            ]
,            'success' => 1,
        ]);
    }
}
