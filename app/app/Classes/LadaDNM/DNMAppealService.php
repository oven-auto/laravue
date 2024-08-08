<?php

namespace App\Classes\LadaDNM;

use App\Models\MarkAlias;

class DNMAppealService
{
    public static function appeal(DNMWorksheetService $dNMWorksheetService)
    {
        $dnmService = DNM::init();

        $alias = new MarkAlias();
        $traficNeed = $dNMWorksheetService->obj->trafic->needs;
        foreach ($traficNeed as $item)
            if ($item->model == 'AppModelsMarkAliases') {
                $alias = MarkAlias::find($item->uid);
                break;
            }

        if ($alias && $alias->id) {
            $response = $dnmService->sendPost('/api/need', [
                'code' => (string)$dNMWorksheetService->obj->trafic_id . $alias->id,
                'brand_id' => $alias->dnm_brand_id,
                'model_alias_id' => $alias->dnm_id,
            ]);

            if ($response->getStatusCode() == 201)
                $dNMWorksheetService->dnmWorksheet->appeals()->updateOrCreate(
                    ['dnm_worksheet_id' => $dNMWorksheetService->dnmWorksheet->id],
                    ['dnm_appeal_id' => $response->json()['id']]
                );
        }
    }
}
