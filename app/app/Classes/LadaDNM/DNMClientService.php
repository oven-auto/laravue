<?php

namespace App\Classes\LadaDNM;

use App\Models\Client;
use App\Models\DnmClient;

class DNMClientService
{
    private $dnmService;

    private $obj;

    private $dnmClient;

    public function __construct(Client $obj)
    {
        $this->dnmService = DNM::init();

        $this->obj = $obj;

        $this->dnmClient = \App\Models\DnmClient::where('client_id', $obj->id)->first() ?? new DnmClient();
    }



    /**
     * ЗАПИСАТЬ ОТВЕТ ОТ ДНМ
     * ХРАНЮ id клиента нашей системы
     * и id клиента в системе ДНМ
     */
    private function write(array $data)
    {
        $this->dnmClient = DnmClient::updateOrCreate(
            ['client_id' => $this->obj->id],
            ['dnm_id' => $data['id']]
        );
    }



    /**
     * ВЕРНУТЬ МАССИВ ДАННЫХ ДЛЯ ОТПРАВКИ
     */
    private function fill()
    {
        if ($this->obj->isPerson())
            return [
                'name' => $this->obj->firstname ?? '',
                'last_name' => $this->obj->lastname,
                'phone_hash' => sha1($this->obj->phones[0]),
                'sex' => $this->obj->sex->dnm_id ?? '',
                'code' => (string)$this->obj->id,
                'type' => $this->obj->getDnmTypeClient() ?? ''
            ];
        elseif ($this->obj->isCompany())
            return [
                'code' => (string)$this->obj->id,
                'company_name' => (string)$this->obj->company_name,
                'type' => $this->obj->getDnmTypeClient() ?? '',
                'company_address' => $this->obj->zone->name ?? ''
            ];
    }



    /**
     * ЗАПРОС НА СОЗДАНИЕ КЛИЕНТА ДНМ
     */
    private function create()
    {
        $data = $this->fill();

        $response = $this->dnmService->sendPost('/api/client', $data);

        if ($response->getStatusCode() == 201) {
            $this->write($response->json());
            return 1;
        }

        throw new \Exception($response->body());
    }



    /**
     * ЗАПРОС НА ИЗМЕНЕНИЕ КЛИЕНТА В ДНМ
     */
    private function update()
    {
        $data = $this->fill();

        $response = $this->dnmService->sendPut('/api/client/' . $this->dnmClient->dnm_id, $data);

        if ($response->getStatusCode() == 200) {
            $this->write($response->json());
            return 1;
        }

        throw new \Exception($response->body());
    }



    public function check()
    {
        $response = $this->dnmService->sendGet('/api/client/', ['code' => $this->obj->id]);

        if ($response->ok() && count($response->json())) {
            $this->write($response->json()[0]);
            return 1;
        }
        return 0;
    }



    /**
     * ОБРАБОТЧИК
     */
    public function save()
    {
        if ($this->check())
            $this->update();
        else
            $this->create();
    }



    public function getDnmId()
    {
        return $this->dnmClient->dnm_id;
    }



    public static function saveClient(Client $client)
    {
        $me = new self($client);
        $me->save();
        return $me;
    }
}
