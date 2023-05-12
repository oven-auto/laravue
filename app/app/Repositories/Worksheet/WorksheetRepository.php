<?php

namespace App\Repositories\Worksheet;

use App\Models\Worksheet;
use App\Models\Trafic;
use App\Repositories\Client\ClientRepository;

class WorksheetRepository
{
    public function createFromTrafic($trafic_id)
    {
        $trafic = Trafic::with('status')->find($trafic_id);

        $client = ClientRepository::getClientFromTrafic($trafic);

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
