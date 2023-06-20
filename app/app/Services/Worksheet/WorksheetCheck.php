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
        foreach ($worksheets as $item)
        {
            if(
                //$item->appeal_id == $trafic->appeal_id &&
                $item->structure_id == $trafic->company_structure_id &&
                $item->company_id == $trafic->company_id
            )
            {
                return new WorksheetSaveResource($item);
            }
        }

        return 0;
    }
}
