<?php

namespace App\Services\Worksheet;

use App\Models\Worksheet;
use App\Models\Client;
use App\Models\Trafic;
use App\Models\ClientPhone;
use App\Repositories\Client\ClientRepository;

class WorksheetService
{
    public function create($trafic_id)
    {
        $trafic = Trafic::with('status')->find($trafic_id);

        $clientService = new ClientRepository();
        $client = $clientService->findOrCreate($trafic);

        $worksheet = Worksheet::create([
            'client_id'         => $client->id,
            'trafic_id'         => $trafic->id,
            'company_id'        => $trafic->salon->id,
            'structure_id'      => $trafic->structure->id,
            'appeal_id'         => $trafic->appeal->id
        ]);

        $worksheet->trafic->status;

        return $worksheet;
    }
}
