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

    public function saveIcon(Mark $mark, UploadedFile $file) :void
    {
        $iconName = 'icon_'.$mark->slug.'.'.$file->getClientOriginalExtension();
        $path = '/public/mark/'.$mark->slug;
        $finalName = $path.'/'.$file->move(Storage::path($path), $iconName)
            ->getFilename();
        if($mark->icon->id)
            $mark->icon()->update(['image' => $finalName]);
        else
            $mark->icon()->create(['image' => $finalName]);
    }

    public function saveBanner(Mark $mark, UploadedFile $file) :void
    {
        $iconName = 'banner_'.$mark->slug.'.'.$file->getClientOriginalExtension();
        $path = '/public/mark/'.$mark->slug;
        $finalName = $path.'/'.$file->move(Storage::path($path), $iconName)
            ->getFilename();
        if($mark->icon->id)
            $mark->banner()->update(['image' => $finalName]);
        else
            $mark->banner()->create(['image' => $finalName]);
    }

    public function saveDocuments(Mark $mark, $data = []) :void
    {
        $finalName = [];
        $path = '/public/mark/'.$mark->slug.'/documents';

        foreach($data as $docName => $file)
            if($file instanceof UploadedFile) {
                $fileName = $docName.'_'.$mark->slug.'.'.$file->getClientOriginalExtension();
                $finalName[$docName] = $path.'/'.$file->move(Storage::path($path), $fileName)
                    ->getFilename();
            }

        if (count($finalName)) {
            if ($mark->document->id) {
                $mark->document()->update($finalName);
            } else {
                $mark->document()->create($finalName);
            }
        }
    }
}
