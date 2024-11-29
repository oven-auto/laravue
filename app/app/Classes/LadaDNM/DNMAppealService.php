<?php

namespace App\Classes\LadaDNM;

use App\Models\DnmWorksheetAppeal;
use App\Models\MarkAlias;
use App\Models\WsmReserveNewCar;
use Exception;
use Illuminate\Support\Facades\Log;

class DNMAppealService
{
    private const ERROR_MSG = [
        'not_alias' => 'Отсутствует синоним машины. Проследуйте в карточку автомобиля и добавте синоним в комплектацию.',
    ];

    public $dnmService;

    public function __construct()
    {
        $this->dnmService = DNM::init();
    }



    /**
     * Получить алиас машины резерва
     */
    private function getAlias(WsmReserveNewCar $reserve)
    {
        $alias = $reserve->car->complectation->alias->alias;

        if(!$alias)
            throw new Exception(self::ERROR_MSG['not_alias']);

        return $alias;
    }



    /**
     * Записать полученные от ДНМ данные
     */
    private function write(\Illuminate\Http\Client\Response  $response, WsmReserveNewCar $reserve)
    {
        DnmWorksheetAppeal::updateOrCreate(
            [
                'reserve_id' => $reserve->id,
            ],
            [
                'dnm_appeal_id' => $response->json()['id'],
                'dnm_worksheet_id' => $reserve->worksheet->dnm->id,
            ]
        );
    }



    /**
     * Отправит в ДНМ данные для создания
     */
    public function create(WsmReserveNewCar $reserve)
    {
        $alias = $this->getAlias($reserve);

        $response = $this->dnmService->sendPost('/api/need', [
            'code' => (string)$reserve->id,
            'brand_id' => $alias->dnm_brand_id,
            'model_alias_id' => $alias->dnm_id,
            'vin'   => $reserve->car->vin,
        ]);

        if ($response->getStatusCode() == 201)
            $this->write($response, $reserve);
        else
            Log::alert('Не удалось создать потребность в DNM.', $response->json());
    }



    /**
     * Отправит в ДНМ данные для изменения
     */
    public function update(WsmReserveNewCar $reserve)
    {
        $alias = $this->getAlias($reserve);

        $response = $this->dnmService->sendPut('/api/need/'.$reserve->dnm->dnm_appeal_id, [
            'code' => (string)$reserve->id,
            'brand_id' => $alias->dnm_brand_id,
            'model_alias_id' => $alias->dnm_id,
            'vin'   => $reserve->car->vin,
        ]);
        
        if ($response->getStatusCode() == 200)
            $this->write($response, $reserve);
        else
            Log::alert('Не удалось изменить потребность в DNM.', $response->json());
    }



    /**
     * Проверить есть ли уже этот резерв в ДНМ
     */
    public function isExist(WsmReserveNewCar $reserve)
    {
        $response = $this->dnmService->sendGet('/api/need',  ['code' => $reserve->id]);

        if ($response->ok() && count($response->json())) 
            return 1;
        return 0;
    }



    /**
     * Сохранить резерв в ДНМ
     */
    public function save(WsmReserveNewCar $reserve)
    {
        (new DNMClientService())->save($reserve);

        (new DNMWorksheetService())->save($reserve->worksheet);

        if($this->isExist($reserve))
            $this->update($reserve);
        else
            $this->create($reserve);
    }
}
