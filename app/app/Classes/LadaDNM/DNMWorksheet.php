<?php

namespace App\Classes\LadaDNM;

use App\Models\Worksheet;

class DNMWorksheet
{
    private $dnmService;

    private $obj;

    public function __construct(Worksheet $obj)
    {
        $this->dnmService = new DNM();

        $this->obj = $obj;
    }



    public function create()
    {
        $dataClient = [
            'name' => $this->obj->client->firstname,
            'last_name' => $this->obj->client->lastname,
            'phone_hash' => sha1($this->obj->client->phones[0]),
            'sex' => $this->obj->client->sex->dnm_id,
            'code' => (string)$this->obj->client->id,
        ];
        $dnmClient = $this->dnmService->sendGet('/api/client/', ['code' => $this->obj->client->id]);

        if ($dnmClient->ok()) {
            $jsonClient = $dnmClient->json();
            $clientId = $jsonClient[0]['id'];
        } else {
            $response = $this->dnmService->sendPost('/api/client', $dataClient);
            if ($response->ok()) {
                $response = $response->json();
                $clientId = $response[0]['id'];
            }
        }

        $dataWorksheet = [
            'code' => (string) $this->obj->id,
            'client_id' => $clientId,
            'source_id' => "13"
        ];

        $response = $this->dnmService->sendPost('/api/worksheet', $dataWorksheet);
    }
}
