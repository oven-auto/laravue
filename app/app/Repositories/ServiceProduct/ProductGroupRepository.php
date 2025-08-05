<?php

namespace App\Repositories\ServiceProduct;

use App\Models\ProductGroup;

Class ProductGroupRepository
{
    public function getById(int $id)
    {
        return ProductGroup::findOrFail($id);
    }



    public function get()
    {
        $groups = ProductGroup::orderBy('sort')->get();

        return $groups;
    }



    public function create(array $data)
    {
        $group = ProductGroup::create($data);

        return $group;
    }



    public function update(int $id, array $data)
    {
        $group = $this->getById($id);

        $group->fill($data)->save();

        return $group;
    }



    public function delete(int $id)
    {
        $group = $this->getById($id);

        $old = $group->replicate();

        $group->delete();

        return $old;
    }
}