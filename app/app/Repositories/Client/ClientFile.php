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

    public function save(ClientFileModel $ClientFileModel, Array $data, $files = [])
    {
        $arr['title'] = $data['title'];

        if(request()->method() == 'POST') {
            $arr['author_id'] = auth()->user()->id;
            $arr['client_id'] = $data['client_id'];
            $arr['file'] = $this->service->download($data['client_id'], $files['file']);
        }

        $ClientFileModel->fill($arr)->save();
    }
}
