<?php

namespace App\Repositories\Discount;

use App\Models\DiscountType;
use App\Repositories\Discount\DTO\DiscountCarDTO;

class DiscountRepository
{
    public function all(array $data)
    {
        $query = DiscountType::query();

        if (isset($data['trash']) && $data['trash'] == 1)
            $query->onlyTrashed();

        if (isset($data['all']) && $data['all'] == 1)
            $query->withTrashed();

        if (isset($data['modul_id']))
            $query->where('modul_id', $data['modul_id']);

        $discounts = $query->get();

        return $discounts;
    }



    public function save(DiscountType $discount, array $data)
    {
        $dto = (new DiscountCarDTO($data))->get();

        $discount->author_id = auth()->user()->id;

        $discount->fill($dto)->save();
    }



    public function delete(DiscountType $discount)
    {
        $discount->delete();
    }



    public function restore(DiscountType $discount)
    {
        $discount->restore();
    }
}
