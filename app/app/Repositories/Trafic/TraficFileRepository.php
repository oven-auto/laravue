<?php

namespace App\Repositories\Trafic;

use App\Services\Download\TraficFileLoad;
use App\Models\Trafic;
use App\Models\TraficFile;

Class TraficFileRepository
{
    public $loadService;

    public function __construct()
    {
        $this->loadService = new TraficFileLoad();
    }

    public function saveTraficFiles(Trafic $trafic, $files)
    {
        $res = [];

        foreach($files as $itemFile) {

            $arr['name'] = $itemFile->getClientOriginalName();
            $arr['filepath'] = $this->loadService->download($trafic->id, $itemFile);
            $arr['trafic_id'] = $trafic->id;
            $arr['user_id'] = auth()->user()->id;

            $res[] = TraficFile::create($arr);
        }

        return $res;
    }
}
