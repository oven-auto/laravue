<?php

namespace App\Repositories;

use App\Models\Mark;
use Illuminate\Http\UploadedFile;
use Storage;
use DB;

Class MarkRepository
{
    const MARK_COL = [
        'name', 'prefix', 'sort', 'status', 'brand_id', 'body_work_id', 'country_factory_id', 'show_toxic', 'show_driver'
    ];

    public function saveMark(Mark $mark, $data = []) :void
    {
        try {
            DB::transaction(function () use ($mark, $data) {
                $this->saveMain($mark, array_filter($data, function ($key) {
                    if (\array_key_exists($key, array_flip(self::MARK_COL) )) {
                        return true;
                    }
                }, ARRAY_FILTER_USE_KEY));

                if (isset($data['info'])) {
                    $this->saveInfo($mark, $data['info']);
                }
                if (isset($data['properties'])) {
                    $this->saveProperties($mark, $data['properties']);
                }
                if (isset($data['icon'])) {
                    $this->saveIcon($mark, $data['icon']);
                }
                if (isset($data['banner'])) {
                    $this->saveBanner($mark, $data['banner']);
                }
                if (isset($data['document'])) {
                    $this->saveDocuments($mark, $data['document']);
                }
                if (isset($data['colors'])) {
                    $this->saveColors($mark, $data['colors']);
                }
            });
        } catch(\Exception $e) {
			die($e);
		}
    }

    public function saveMain(Mark $mark, $data = []) :void
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
        $model->properties()->detach();

        foreach($data as $id => $value)
            $model->properties()->attach($id, ['value' => $value]);
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
        if($data == null && !is_array($data))
            return;

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
        //dump($path);
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
        $time = '?'.date('dmyhms');
        $mark->info;
        $mark->icon->image = asset('storage'.$mark->icon->image) . $time;
        $mark->banner->image = asset('storage'.$mark->banner->image) . $time;

        $mark->properties;
        if($mark->properties->count())
            foreach ($mark->properties as $item) {
                $item->value = $item->pivot->value;
                unset($item->pivot);
            }

        if($mark->document->brochure)
            $mark->document->brochure = asset('storage'.$mark->document->brochure);
        if($mark->document->price)
            $mark->document->price = asset('storage'.$mark->document->price);
        if($mark->document->manual)
            $mark->document->manual = asset('storage'.$mark->document->manual);
        if($mark->document->accessory)
            $mark->document->accessory = asset('storage'.$mark->document->accessory);

        if($mark->markcolors->count())
            foreach($mark->markcolors as $itemColor)
                $itemColor->image = asset('storage'.$itemColor->image) . $time;
    }

    public function getMarkTabList()
    {
        $marks = Mark::select('name', 'prefix', 'id', 'body_work_id','slug')
            ->with(['icon','bodywork','basecomplectation'])
            ->get();

        return $marks;
    }

    public function getMarkBySlug($slug)
    {
        $mark = Mark::with(['bodywork','banner', 'brand', 'info', 'properties','markcolors'])
            ->where('slug',$slug)
            ->first();

        return $mark;
    }

    public function getMarksName()
    {
        $marks = Mark::select('name','prefix','id', 'slug')->get();
        return $marks;
    }
}
