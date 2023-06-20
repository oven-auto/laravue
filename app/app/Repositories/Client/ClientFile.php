<?php

namespace App\Repositories\Client;

use App\Models\ClientFile as ClientFileModel;


Class ClientFile
{
    private $service;

    public function __construct(\App\Services\Download\ClientFileLoad $service)
    {
        $this->service = $service;
    }

    public function getByParam(Array $data)
    {
        $query = ClientFileModel::query();

        if(isset($data['client_id']))
            $query->where('client_id', $data['client_id']);

        $files = $query->get();

        return $files;
    }

    public function save(Array $data, $files = [])
    {
        if(request()->method() == 'POST') {
            foreach($files as $itemFile)
            {
                ClientFileModel::create([
                    'author_id' => auth()->user()->id,
                    'client_id' => $data['client_id'],
                    'file' => $this->service->download($data['client_id'], $itemFile)
                ]);
            }
        }
        $files = ClientFileModel::where('client_id', $data['client_id'])->get();
        return $files;
    }
}
