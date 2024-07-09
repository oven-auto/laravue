<?php

namespace App\Classes\LadaDNM;

use App\Models\MarkAlias;

class DNMEvent
{
    private $service;

    public function __construct()
    {
        $this->service = DNM::init();
    }







    public function visit(DNMWorksheet $dNMWorksheet)
    {
        $data = [
            'event_type' => 'visit',
            'worksheet_id' => $dNMWorksheet->getDnmWorksheetId(),
            'code' => (string)$dNMWorksheet->obj->trafic_id,
            'occurred' => now()->format('d.m.Y H:i:s'),
            'manager_id' => 1764,
            'car_id' => $dNMWorksheet->dnmWorksheet->dnm_appeal_id,
        ];

        $response = $this->service->sendPost('/api/event', $data);

        if ($response->getStatusCode() == 201) {
            return;
        }
    }



    public function reject(DNMWorksheet $dNMWorksheet)
    {
        $data = [
            'event_type' => 'reject',
            'worksheet_id' => $dNMWorksheet->getDnmWorksheetId(),
            'code' => (string)rand(1, 255),
            'occurred' => now()->format('d.m.Y H:i:s'),
            'manager_id' => 1764,
            'car_id' => $dNMWorksheet->dnmWorksheet->dnm_appeal_id,
        ];

        $response = $this->service->sendPost('/api/event', $data);

        if ($response->getStatusCode() == 201) {
            return;
        }
    }
}
