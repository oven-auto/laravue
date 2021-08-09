<?php

namespace App\Repositories;

use App\Models\Mark;
use Illuminate\Http\UploadedFile;
use Storage;

Class MarkRepository
{
    public function saveMark(Mark $mark, $data = []) :void
    {
        $mark->fill($data)->save();
    }

    public function saveInfo(Mark $mark, $data = []) :void
    {
        if($mark->info->id)
            $mark->info()->update($data);
        else
            $mark->info()->create($data);
    }

    public function saveProperties(Mark $model, $data = []) :void
    {
        $propertiesArray = [];
        foreach($data as $id => $value)
            if(!empty($id) && !empty($value))
                $propertiesArray[] = ['property_id' => $id, 'value' => $value];
        $model->properties()->sync($propertiesArray);
    }

    public function saveIcon(Mark $mark, $file) :void
    {
        if ($file instanceof UploadedFile) {
            $iconName = 'icon_'.$mark->slug.'.'.$file->getClientOriginalExtension();
            $path = '/public/mark/'.$mark->slug;
            $urlPath = '/mark/'.$mark->slug;

            $finalName = $urlPath.'/'.$file->move(Storage::path($path), $iconName)
                ->getFilename();
            if ($mark->icon->id) {
                $mark->icon()->update(['image' => $finalName]);
            } else {
                $mark->icon()->create(['image' => $finalName]);
            }
        }
    }

    public function saveBanner(Mark $mark, $file) :void
    {
        if ($file instanceof UploadedFile) {
            $iconName = 'banner_'.$mark->slug.'.'.$file->getClientOriginalExtension();
            $path = '/public/mark/'.$mark->slug;
            $urlPath = '/mark/'.$mark->slug;

            $finalName = $urlPath.'/'.$file->move(Storage::path($path), $iconName)->getFilename();

            if ($mark->icon->id) {
                $mark->banner()->update(['image' => $finalName]);
            } else {
                $mark->banner()->create(['image' => $finalName]);
            }
        }
    }

    public function saveDocuments(Mark $mark, $data = []) :void
    {
        $finalName = [];
        $path = '/public/mark/'.$mark->slug.'/documents';
        $urlPath = '/mark/'.$mark->slug.'/documents';

        foreach($data as $docName => $file)
            if($file instanceof UploadedFile) {
                $fileName = $docName.'_'.$mark->slug.'.'.$file->getClientOriginalExtension();
                $finalName[$docName] = $urlPath.'/'.$file->move(Storage::path($path), $fileName)
                    ->getFilename();
            }

        if (count($finalName)) {
            if ($mark->document->id)
                $mark->document()->update($finalName);
            else
                $mark->document()->create($finalName);
        }
    }

    public function saveColors(Mark $mark, $data = [])
    {
        $finalName = [];
        $path = '/public/mark/'.$mark->slug.'/colors';
        $urlPath = '/mark/'.$mark->slug.'/colors';
        $idArray = [];

        foreach($data as $colorId => $file) {
            if ($file instanceof UploadedFile) {
                $fileName = $colorId.'_'.$mark->slug.'.'.$file->getClientOriginalExtension();
                $mark->markcolors()->where('color_id', $colorId)->delete();
                $mark->markcolors()->create([
                    'color_id' => $colorId,
                    'image' => $urlPath.'/'.$file->move(Storage::path($path), $fileName)->getFilename()
                ]);
            }
            $idArray[] = $colorId;
        }
        $mark->markcolors()->whereNotIn('color_id', $idArray)->delete();
    }

    public function loadFullData(Mark $mark)
    {
        $mark->info;
        $mark->icon->image = asset('storage'.$mark->icon->image);
        $mark->banner->image = asset('storage'.$mark->banner->image);

        $mark->properties;
        if($mark->properties->count())
            foreach ($mark->properties as $item) {
                $item->value = $item->pivot->value;
                unset($item->pivot);
            }

        $mark->document->brochure = asset('storage'.$mark->document->brochure);
        $mark->document->price = asset('storage'.$mark->document->price);
        $mark->document->manual = asset('storage'.$mark->document->manual);
        $mark->document->accessory = asset('storage'.$mark->document->accessory);

        if($mark->markcolors->count())
            foreach($mark->markcolors as $itemColor)
                $itemColor->image = asset('storage'.$itemColor->image);
    }
}
