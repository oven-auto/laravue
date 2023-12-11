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

    public function getByParam(Array $data)
    {
        // $query = WorksheetFileModel::query();

        // if(isset($data['client_id']))
        //     $query->where('client_id', $data['client_id']);

        // $files = $query->orderBy('id','DESC')->get();

        // return $files;
    }

    public function saveFiles(\App\Models\Worksheet $worksheet, $files = [])
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
