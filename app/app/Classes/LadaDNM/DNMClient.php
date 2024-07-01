<?php

namespace App\Classes\LadaDNM;

use App\Models\Client;

class DNMClient
{
    private $dnmService;

    private $obj;

    public function __construct(Client $obj)
    {
        $this->dnmService = new DNM();

        $this->obj = $obj;
    }



    private function create($data = [])
    {
        $data = [
            'name' => $this->obj->firstname,
            'last_name' => $this->obj->lastname,
            'phone_hash' => sha1($this->obj->phones[0]),
            'sex' => $this->obj->sex->dnm_id,
            'code' => (string)$this->obj->id,
        ];

        $response = $this->dnmService->sendPost('/api/client', $data);

        return $response;
    }



    private function update($data = [])
    {
        $data = [
            'name' => $this->obj->firstname,
            'last_name' => $this->obj->lastname,
            'phone_hash' => sha1($this->obj->phones[0]),
            'sex' => $this->obj->sex->dnm_id,
            //'code' => (string)$this->obj->id,
        ];

        $dnmClient = $this->dnmService->sendGet('/api/client/', ['code' => $this->obj->id]);

        if ($dnmClient->ok()) {
            $jsonClient = $dnmClient->json();
            $clientId = $jsonClient[0]['id'];
            $response = $this->dnmService->sendPut('/api/client/' . $clientId, $data);

            return $response;
        }

        throw new \Exception($dnmClient->body());
    }



    public function handler()
    {
        if ($this->obj->wasRecentlyCreated)
            $response = $this->create();
        else
            $response = $this->update();

        if (!$response->ok())
            throw new \Exception($response->body());
    }
}
