<?php

namespace App\Repositories\Car\Color;

use App\Models\DealerColor;
use App\Models\DealerColorImage;
use App\Repositories\Car\Color\DTO\ColorDTO;
use App\Services\Download\ColorFileLoad;

Class ColorRepository
{
    /**
     * STORE
     * @param array $data
     * @return DealerColor
     */
    public function store(array $data) : DealerColor
    {
        $color = DealerColor::create(array_merge(
            (new ColorDTO($data))->get(),
            ['author_id' => auth()->user()->id],
        ));

        return $color;
    }



    /**
     * UPDATE
     * @param DealerColor $dealerColor
     * @param array $data
     * @return void
     */
    public function update(DealerColor $dealerColor, array $data) : void
    {
        $dealerColor->fill(array_merge(
            (new ColorDTO($data))->get(),
            ['author_id' => auth()->user()->id],
        ))->save();
    }



    /**
     * DELETE
     * @param DealerColor $dealerColor
     * @return void
     */
    public function delete(DealerColor $dealerColor) : void
    {
        $dealerColor->delete();
    }



    /**
     * RESTORE
     * @param DealerColor $dealerColor
     * @return void
     */
    public function restore(DealerColor $dealerColor) : void
    {
        $dealerColor->restore();
    }



    /**
     * GET
     * @param array $data [trash | mark_id | name]
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(array $data) : \Illuminate\Database\Eloquent\Collection
    {
        count($data) ?: throw new \Exception('Необходимо указать хотя бы один параметр');

        $query = DealerColor::select('dealer_colors.*')->with(['mark', 'brand', 'base', 'author']);

        if(isset($data['mark_id']))
            $query->where('mark_id', $data['mark_id']);

        if(isset($data['trash']) && $data['trash'])
            $query->onlyTrashed();

        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        $colors = $query->get();

        return $colors;
    }



    /**
     * LIST
     * @param string $markId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(int $markId) : \Illuminate\Database\Eloquent\Collection
    {
        $query = DealerColor::select('dealer_colors.id', 'dealer_colors.name');

        $query->where('mark_id', $markId);

        $list = $query->get();

        return $list;
    }



    public function appendImage(array $data)
    {
        $service = new ColorFileLoad();

        $strImage = $service->download($data['color_id'], $data['image']);
        
        $image = DealerColorImage::create([
            'image' => $strImage,
            'body_work_id' => $data['bodywork'],
            'dealer_color_id' => $data['color_id'],
        ]);

        return $image;
    }



    public function updateImage(DealerColorImage $image, array $data)
    {
        $image->body_work_id = $data['bodywork'];
        
        if(isset($data['image']))
        {
            unset($image->image);
            
            $service = new ColorFileLoad();

            $strImage = $service->download($image->dealer_color_id, $data['image']);

            $image->image = $strImage;
        }

        $image->save();

        return $image;
    }



    public function deleteImage(DealerColorImage $image)
    {
        unset($image->image);

        $image->delete();
    }
}
