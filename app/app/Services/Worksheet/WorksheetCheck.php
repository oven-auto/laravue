<?php

namespace App\Services\Worksheet;

use App\Http\Resources\Worksheet\WorksheetSaveResource;
use App\Models\Client;
use App\Models\Trafic;

Class WorksheetCheck
{
    public static function check(Client $client, Trafic $trafic)
    {
        $worksheets = $client->open_worksheets;
        $worksheetExistArray = [];
        // dump([
        //     'trafic_company' => $trafic->company_id,
        //     'trafic_structure' => $trafic->structure->id,
        // ]);

        foreach ($worksheets as $item)
        {
            // dump([
            //     'ws_company_id' => $item->company_id,
            //     'ws_stucture' => $item->structure_id,
            // ]);
            if(
                //$item->appeal_id == $trafic->appeal_id &&
                $item->structure_id == $trafic->structure->id &&
                $item->company_id == $trafic->company_id
            )
            {
                return new WorksheetSaveResource($item);
            }
        }

        return 0;
    }
}
