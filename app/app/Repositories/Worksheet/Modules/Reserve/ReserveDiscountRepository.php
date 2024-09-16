<?php

namespace App\Repositories\Worksheet\Modules\Reserve;

use App\Helpers\Date\DateHelper;
use App\Models\Discount;
use App\Models\WsmReserveCarSale;
use App\Models\WsmReserveNewCar;
use Carbon\Carbon;

class ReserveDiscountRepository
{
    /**
     * ПОЛУЧИТЬ ВСЕ СКИДКИ УКАЗАНОГО РЕЗЕРВА
     */
    public function getDiscountsByReserveId(int $reserveId): \Illuminate\Support\Collection
    {
        $reserve = WsmReserveNewCar::find($reserveId);

        $discounts = $reserve->discounts;

        return $discounts;
    }



    /**
     * СОХРАНИТЬ СКИДКУ
     */
    public function saveDiscount(Discount $discount, $data)
    {
        $reserve = WsmReserveNewCar::find($data['reserve_id']);

        $discount->fill([
            'modulable_id' => $reserve->id,
            'modulable_type' => $reserve::class,
            'worksheet_id' => $reserve->worksheet_id,
            'discount_type_id' => $data['discount_type_id'],
            'author_id' => auth()->user()->id,
        ])->save();
    }



    /**
     * СОХРАНИТЬ СУММУ СКИДКИ
     */
    public function saveSum(Discount $discount, int $amount = null)
    {
        if (!$amount)
            return;

        if (($discount->sum && $discount->sum->amount != $amount) || !$discount->sum)
            $discount->sum()->updateOrCreate(
                ['discount_id' => $discount->id],
                [
                    'amount' => $amount,
                    'author_id' => auth()->user()->id
                ],
            );
    }



    /**
     * СОХРАНИТЬ СУММУ ВОЗМЕЩЕНИЯ
     */
    public function saveReparation(Discount $discount, int $amount = null)
    {
        if (!$amount)
            return;

        if (($discount->reparation && $discount->reparation->amount != $amount) || !$discount->reparation)
            $discount->reparation()->updateOrCreate(
                ['discount_id' => $discount->id],
                [
                    'amount' => $amount,
                    'author_id' => auth()->user()->id
                ],
            );
    }



    /**
     * СОХРАНИТЬ ДАТУ ВОЗМЕЩЕНИЯ
     */
    public function saveReparationDate(Discount $discount, string $date = null)
    {


        if (!$date)
            $discount->reparation_date()->delete();


        else {
            $date = DateHelper::createFromString($date, 'd.m.Y');
            if (($discount->reparation_date && $date->diffInDays($discount->reparation_date->date_at)) || !$discount->reparation_date) {
                $discount->reparation_date()->updateOrCreate(
                    ['discount_id' => $discount->id],
                    [
                        'date_at' => $date,
                        'author_id' => auth()->user()->id
                    ],
                );
            }
        }
    }



    /**
     * СОХРАНИТЬ ОСНОВАНИЕ ВОЗМЕЩЕНИЯ
     */
    public function saveBase(Discount $discount, string $base = null)
    {
        if (!$base)
            $discount->base()->delete();
        else {
            if (($discount->base && $discount->base->base != $base) || !$discount->base)
                $discount->base()->updateOrCreate(
                    ['discount_id' => $discount->id],
                    [
                        'base' => $base,
                        'author_id' => auth()->user()->id
                    ],
                );
        }
    }



    /**
     * ФАСАДНЫЙ МЕТОД СОХРАНЕНИЯ СКИДКИ
     */
    public function save(Discount $discount, array $data): void
    {
        $this->saveDiscount($discount, $data);

        $this->saveSum($discount, $data['sum'] ?? null);

        $this->saveReparation($discount, $data['reparation'] ?? null);

        $this->saveReparationDate($discount, $data['reparation_date'] ?? null);

        $this->saveBase($discount, $data['base'] ?? null);

        $discount->refresh();
    }



    public function delete(Discount $sale): void
    {
        $sale->delete();
    }
}
