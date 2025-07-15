<?php 

namespace App\Repositories\Worksheet\Modules\Credit;

use App\Helpers\Array\ArrayHelper;
use App\Http\DTO\Worksheet\Credit\CreateCreditDTO;
use App\Http\Filters\CreditFilter;
use App\Models\WSMCredit;
use App\Models\WSMCreditCar;
use Illuminate\Support\Facades\DB;

Class CreditRepository
{
    public function createAward(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $credit->award()->updateOrCreate(
            ['wsm_credit_id' => $credit->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->award)
        );
    }



    public function createContract(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $credit->contract()->updateOrCreate(
            ['wsm_credit_id' => $credit->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->contract)
        );
    }




    public function createDeduction(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $credit->deduction()->updateOrCreate(
            ['wsm_credit_id' => $credit->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->deduction)
        );
    }



    public function createCalculation(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $credit->calculation()->updateOrCreate(
            ['wsm_credit_id' => $credit->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->calculation)
        );
    }



    public function createServices(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $credit->services()->sync((array) $dto->services->services);
    }



    public function createApproximates(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $credit->approximates()->sync($dto->approximates->approximates);
    }



    public function createCar(WSMCredit $credit, CreateCreditDTO $dto)
    {
        $type = WSMCreditCar::getModelName($dto->car->type);

        $car = $type::findOrFail($dto->car->id);

        if($car) 
        {
            $credit->car()->updateOrCreate(
                ['wsm_credit_id' => $credit->id],
                ['carable_id' => $car->id, 'carable_type' => $type]
            );
        }
    }



    public function getById(int $id)
    {
        return WSMCredit::findOrFail($id);
    }



    public function create(CreateCreditDTO $dto) : WSMCredit
    {
        $result = DB::transaction(function() use ($dto){
            $credit = WSMCredit::create((array) $dto->credit);
            
            if(!ArrayHelper::isAllNull((array) $dto->award))
                $this->createAward($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->contract))
                $this->createContract($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->deduction))
                $this->createDeduction($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->calculation))
                $this->createCalculation($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->services))
                $this->createServices($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->approximates))
                $this->createApproximates($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->car))
                $this->createCar($credit, $dto);

            return $credit;
        }, 1);

        return $result;
    }



    public function update(int $id, CreateCreditDTO $dto) : WSMCredit
    {
        $result = DB::transaction(function() use ($dto, $id){
            $credit = $this->getById($id);

            $credit->fill((array) $dto->credit)->save();

            if(!ArrayHelper::isAllNull((array) $dto->award))
                $this->createAward($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->contract))
                $this->createContract($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->deduction))
                $this->createDeduction($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->calculation))
                $this->createCalculation($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->services))
                $this->createServices($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->approximates))
                $this->createApproximates($credit, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->car))
                $this->createCar($credit, $dto);

            return $credit;
        }, 1);
        
        return $result;
    }



    public function getToWorksheet(array $data)
    {
        $query = WSMCredit::query()->select('wsm_credits.*')->allRelations();

        if(isset($data['worksheet']))
            $query->where('worksheet_id', $data['worksheet']);

        $result = $query->get();

        return $result;
    }



    public function delete(int $id) : void
    {
        $credit = $this->getById($id);

        $credit->delete();
    }



    public function paginate(array $data)
    {
        $query = WSMCredit::query()->allRelations()->select('wsm_credits.*');

        $filter = app()->make(CreditFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);

        $result = $query->simplePaginate(20);

        return $result;
    }



    public function count(array $data)
    {
        $query = WSMCredit::select([
            DB::raw('COUNT(wsm_credits.id) as _count'),
            //DB::raw('SUM(wsm_services.cost) as _cost'),
            //DB::raw('SUM(wsm_service_awards.sum) as _award'),
        ]);

        $filter = app()->make(CreditFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);

        $res = DB::table($query)->first();

        return $res;
    }
}