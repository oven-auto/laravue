<?php

namespace App\Classes\LadaDNM;

class DNMEvent
{
    private $dnm;

    private $dNMWorksheet;

    public function __construct(DNMWorksheetService $dNMWorksheet)
    {
        $this->dnm = DNM::init();

        $this->dNMWorksheet = $dNMWorksheet;
    }



    public function save(array $data)
    {
        $this->dNMWorksheet->dnmWorksheet->events()->create([
            'dnm_event_id' => $data['id'],
            'dnm_worksheet_id' => $data['dnm_worksheet_id'],
            'dnm_appeal_id' => $data['dnm_appeal_id']
        ]);
    }



    public function visit()
    {
        $data = [
            'event_type' => 'visit',
            'worksheet_id' => $this->dNMWorksheet->getDnmWorksheetId(),
            'code' => (string)$this->dNMWorksheet->obj->trafic_id,
            'occurred' => now()->format('d.m.Y H:i:s'),
            'manager_id' => 1764,
            'car_id' => $this->dNMWorksheet->dnmWorksheet->appeals->first()->dnm_appeal_id,
        ];

        $response = $this->dnm->sendPost('/api/event', $data);

        if ($response->getStatusCode() == 201) {
            $this->save($response->json());
            return;
        }
    }



    public function reject()
    {
        $data = [
            'event_type' => 'reject',
            'worksheet_id' => $this->dNMWorksheet->getDnmWorksheetId(),
            'code' => (string)rand(1, 255),
            'occurred' => now()->format('d.m.Y H:i:s'),
            'manager_id' => 1764,
            'car_id' => $this->dNMWorksheet->dnmWorksheet->appeals->first()->dnm_appeal_id,
            'result' => 12,
        ];

        $response = $this->dnm->sendPost('/api/event', $data);

        if ($response->getStatusCode() == 201) {
            $this->save($response->json());
            return;
        }
    }



    public function call()
    {
        $data = [
            'event_type' => 'call',
            'worksheet_id' => $this->dNMWorksheet->getDnmWorksheetId(),
            'code' => (string)rand(1, 255),
            'occurred' => now()->format('d.m.Y H:i:s'),
            'manager_id' => 1764,
            'car_id' => $this->dNMWorksheet->dnmWorksheet->appeals->first()->dnm_appeal_id,
        ];

        $response = $this->dnm->sendPost('/api/event', $data);

        if ($response->getStatusCode() == 201) {
            $this->save($response->json());
            return;
        }
    }
}
