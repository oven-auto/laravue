<?php

namespace App\Repositories\Trafic;

use App\Services\Download\TraficFileLoad;
use App\Services\FileConverter\Audio;
use App\Models\Trafic;
use App\Models\AuditStandart;

Class TraficAuditRepository
{
    public $loadService;

    public function __construct()
    {
        $this->loadService = new TraficFileLoad();
    }

    public function saveTraficAudit(Trafic $trafic, $data = [], $files)
    {
        $arr['record'] = Audio::wavToMp3($this->loadService->download($trafic->id, $files['record']));
        $arr['audit'] = $this->loadService->download($trafic->id, $files['audit']);
        $arr['result'] = $data['result'];
        $arr['trafic_id'] = $trafic->id;
        $arr['audit_standart_id'] = $data['scenario'];
        $arr['user_id'] = auth()->user()->id;
        $arr['status'] = AuditStandart::find($arr['audit_standart_id'])->target < $arr['result'] ? true : false;

        $trafic->processing()->create($arr);

        return $arr;
    }
}
