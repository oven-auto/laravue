<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Services\Download\DownloadImage;

class TraficFileController extends Controller
{
    private $loadService;

    public function __construct(DownloadImage $service)
    {
        $this->loadService = $service;
    }

    public function load($trafic, Request $request)
    {
        $trafic = \App\Models\Trafic::withTrashed()->findOrFail($trafic);

        $arr['trafic_id'] = $trafic->id;

        $arr = [];

        if ($request->file('record') instanceof UploadedFile) {
            $arr['record'] = $this->loadService
                ->setFile($request->file('record'))
                ->setCatalog('trafic')
                ->setPathName($trafic->id)
                ->setPrefix($trafic->id.'_trafic_record')
                ->save();
            }

        if ($request->file('audit') instanceof UploadedFile)
            $arr['audit'] = $this->loadService
                ->setFile($request->file('audit'))
                ->setCatalog('trafic')
                ->setPathName($trafic->id)
                ->setPrefix($trafic->id.'_trafic_audit')
                ->save();

        $arr['result'] = $request->result;

        $trafic->processing()->updateOrCreate(
            ['trafic_id' => $trafic->id], $arr);

        return response()->json([
            'data' => $trafic->processing,
            'success' => 1,
            'message' => 'Фаилы добавлены'
        ]);
    }

    public function show($trafic_id)
    {
        $result = \App\Models\TraficProcessing::where('trafic_id', $trafic_id)->first();
        return response()->json([
            'data' => [
                'record' => $result->getFile('record'),
                'audit' => $result->getFile('audit'),
                'result' => $result->result
            ]
,            'success' => 1,
        ]);
    }
}