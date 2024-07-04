<?php

namespace App\Classes\LadaDNM;

use App\Models\DnmLada;
use App\Models\Worksheet;

class DNMWorksheet
{
    private $dnmService;

    private $obj;

    private $dnmWorksheet;

    public function __construct(Worksheet $obj)
    {
        $this->dnmService = DNM::init();

        $this->obj = $obj;

        $this->dnmWorksheet = DnmLada::where('worksheet_id', $obj->id)->first();
    }



    /**
     * ЗАПИСАТЬ ОТВЕТ ОТ ДНМ
     */
    private function write(array $data)
    {
        $this->dnmWorksheet = DnmLada::updateOrCreate(
            ['worksheet_id' => $this->obj->id],
            ['dnm_worksheet_id' => $data['id']]
        );
    }



    private function fill()
    {
        return [
            'code' => (string) $this->obj->id,
            'client_id' => DnmLada::where('client_id', $this->obj->client_id)->first()->dnm_client_id,
            'source_id' => "13"
        ];
    }



    private function create()
    {
        $response = $this->dnmService->sendPost('/api/worksheet', $this->fill());

        if ($response->getStatusCode() == 201) {
            $this->write($response->json());
            return 1;
        }

        throw new \Exception($response->body());
    }



    private function update()
    {
        $response = $this->dnmService->sendPut('/api/worksheet/' . $this->dnmWorksheet->dnm_worksheet_id, $this->fill());

        if ($response->getStatusCode() == 200) {
            $this->write($response->json());
            return 1;
        }

        throw new \Exception($response->body());
    }



    public function check()
    {
        $response = $this->dnmService->sendGet('/api/worksheet/', ['code' => $this->obj->id]);

        if ($response->ok() && count($response->json())) {
            $this->write($response->json()[0]);
            return 1;
        }
        return 0;
    }



    public function save()
    {
        if ($this->check())
            $this->update();
        else
            $this->create();
    }
}
