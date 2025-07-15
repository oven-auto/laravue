<?php

namespace App\Repositories\Services;

use App\Models\ServiceCategory;

Class ServiceCategoryRepository
{
    public function getAll()
    {
        return ServiceCategory::all();
    }



    public function getById(int $id)
    {
        return ServiceCategory::findOrFail($id);
    }



    public function create(array $data)
    {
        $category = ServiceCategory::create($data);

        return $category;
    }



    public function update(int $id, array $data)
    {
        $category = $this->getById($id);

        $category->fill($data);

        if($category->isDirty())
            $category->save();

        return $category;
    }
}