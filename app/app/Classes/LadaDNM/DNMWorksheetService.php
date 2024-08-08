<?php

namespace App\Classes\LadaDNM;

use App\Models\DnmWorksheet;
use App\Models\Worksheet;

class DNMWorksheetService
{
    private $dnmService;

    public $obj;

    public $dnmWorksheet;

    public $event;

    public function __construct(Worksheet $obj)
    {
        $this->dnmService = DNM::init();

        $this->obj = $obj;

        $this->dnmWorksheet = DnmWorksheet::where('worksheet_id', $obj->id)->first() ?? new DnmWorksheet();

        $this->event = new DNMEvent($this);
    }



    public function getDnmWorksheetId()
    {
        return $this->dnmWorksheet->dnm_id;
    }



    /**
     * ЗАПИСАТЬ ОТВЕТ ОТ ДНМ
     */
    private function write(array $data)
    {
        $this->dnmWorksheet = DnmWorksheet::updateOrCreate(
            ['worksheet_id' => $this->obj->id],
            ['dnm_id' => $data['id']]
        );
    }



    private function fill()
    {
        $clientService = DNMClientService::saveClient($this->obj->client);

        return [
            'code' => (string) $this->obj->id,
            'client_id' => $clientService->getDnmId(),
            'source_id' => "13"
        ];
    }



    public function setAppeal()
    {
        DNMAppealService::appeal($this);
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
        $response = $this->dnmService->sendPut('/api/worksheet/' . $this->dnmWorksheet->dnm_id, $this->fill());

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



    public static function setWorksheet(Worksheet $worksheet)
    {
        $me = new self($worksheet);
        $me->save();
        $me->event = new DNMEvent($me);
        return $me;
    }
}
