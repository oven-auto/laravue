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
            'car_id' => 1
        ];

        $response = $this->service->sendPost('/api/event', $data);

        dd($response->json());
    }
}
