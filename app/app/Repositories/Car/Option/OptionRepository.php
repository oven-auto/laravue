<?php

namespace App\Repositories\Car\Option;

use App\Models\Option;
use App\Repositories\Car\Option\DTO\OptionDTO;

Class OptionRepository
{
    /**
     * CREATE
     * @param array $data ['name', 'code', 'price', 'brand_id', 'mark_id']
     * @return Option
     */
    public function store(array $data) : Option
    {
        $option = Option::create(array_merge((new OptionDTO($data))->get(), ['author_id' => auth()->user()->id]));

        return $option;
    }



    /**
     * UPDATE
     * @param Option $option
     * @param array $data ['name', 'code', 'price', 'brand_id', 'mark_id']
     * @return void
     */
    public function update(Option $option, array $data) : void
    {
        $option->fill(array_merge((new OptionDTO($data))->get(), ['author_id' => auth()->user()->id]))->save();
    }



    /**
     * DELETE
     * @param Option $option
     * @return void
     */
    public function delete(Option $option) : void
    {
        $option->delete();
    }



    /**
     * RESTORE
     * @param Option $option
     * @return void
     */
    public function restore(Option $option) : void
    {
        $option->restore();
    }



    /**
     * GET
     * @param array $data ['trash', 'mark_id', 'name', 'code']
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(array $data) : \Illuminate\Database\Eloquent\Collection
    {
        $query = Option::select('options.*')->with(['mark','brand','author']);

        if(isset($data['mark_id']))
            $query->where('options.mark_id', $data['mark_id']);

        if(isset($data['trash']) && $data['trash'])
            $query->onlyTrashed();

        if(isset($data['code']))
            $query->where('options.code', $data['code']);

        if(isset($data['name']))
            $query->where('options.name', 'LIKE', '%'.$data['mark_id'].'%');

        $options = $query->get();

        return $options;
    }
}
