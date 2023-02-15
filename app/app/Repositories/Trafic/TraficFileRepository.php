<?php

namespace App\Repositories\Trafic;

use App\Services\Download\TraficFileLoad;
use App\Models\Trafic;

Class TraficFileRepository
{
    public $loadService;

    public function __construct()
    {
        $this->loadService = new TraficFileLoad();
    }

    public function saveTraficFiles(Trafic $trafic, $files)
    {
        foreach($files as $itemFile) {
            $arr['name'] = $itemFile->getClientOriginalName();
            $arr['filepath'] = $this->loadService->download($trafic->id, $itemFile);
            $arr['trafic_id'] = $trafic->id;
            $arr['user_id'] = auth()->user()->id;
            $trafic->files()->create($arr);
        }
        return 1;
    }
}
