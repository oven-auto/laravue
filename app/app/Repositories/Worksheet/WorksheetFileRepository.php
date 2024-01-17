<?php

namespace App\Repositories\Worksheet;

use App\Models\WorksheetFile as WorksheetFileModel;


Class WorksheetFileRepository
{
    private $service;

    public function __construct(\App\Services\Download\WorksheetFileLoad $service)
    {
        $this->service = $service;
    }



    /**
     * СОХРАНИТЬ ФАИЛ В РЛ
     * @param \App\Models\Worksheet $worksheet
     * @param array $files
     * @return array
     */
    public function saveFiles(\App\Models\Worksheet $worksheet, $files = []) : array
    {
        $res = [];

        $files = collect($files)->collapse();

        foreach($files as $itemFile)
        {
            $arr['file'] = $this->service->download($worksheet->id, $itemFile);
            $arr['worksheet_id'] = $worksheet->id;
            $arr['author_id'] = auth()->user()->id;

            $res[] = WorksheetFileModel::create($arr);
        }

        return $res;
    }
}
