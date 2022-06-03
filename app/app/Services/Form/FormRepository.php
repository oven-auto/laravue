<?php

namespace App\Services\Form;
use App\Models\Form;
use Illuminate\Http\UploadedFile;
use App\Models\FormSection;
use Storage;

Class FormRepository
{
    public function save(Form $form, $data = [])
    {
        $result = [];
        try {
            if(isset($data['banner']) && $data['banner'] instanceof UploadedFile)
                $data['banner'] = $this->saveImage($data['banner']);
            else
                unset($data['banner']);

            $bodies = explode(',', $data['bodies']);
            $recipients = explode(',', $data['recipients']);
            unset($data['bodies'], $data['recipients']);

            $section = FormSection::find($data['form_section_id']);
            $form->brand_id = $section->brand_id;

            $form->fill($data);
            $form->save();

            if( count($recipients ))
                $form->recipients()->sync($recipients);

            if( count($bodies))
                $form->bodies()->sync($bodies);
            $result = ['status' => 1, 'data'=>$form];
        } catch(\Exception $e) {
            $result = ['status' => 0, 'message' => $e->getMessage(), 'data'=>[]];
        }
        return $result;
    }

    public function saveImage($file)
    {
        $bannerName = 'form_'.date('dmyhms').'.'.$file->getClientOriginalExtension();
        $path = '/public/form/';
        $finalName = '/form/'.$file->move(Storage::path($path), $bannerName)->getFilename();
        return $finalName;
    }

    public function getFormById($id)
    {
        $form = Form::find($id);
        $form->banner = $form->banner ? asset('storage/'.$form->banner) . '?'.date('dmYh') : '';
        $data = $form->toArray();
        $data['bodies'] = $form->bodies->pluck('id');
        $data['recipients'] = $form->recipients->pluck('id');
        return $data;
    }

    public function findBy($data = [])
    {
        $query = Form::select('forms.*');

    	if(isset($data['id']))
    		$query->where('forms.id',$data['id']);

        if(isset($data['brand_id']))
            $query->where('forms.brand_id', $data['brand_id']);

        if(isset($data['form_event']))
            $query->leftJoin('form_events', 'form_events.id', 'forms.form_event_id')
                ->where('form_events.name', $data['form_event']);

        $form = $query->first();
        $form->banner = $form->banner ? asset('storage/'.$form->banner) . '?'.date('dmYh') : '';
        $result = $form->toArray();
        $result['bodies'] = $form->bodies->groupBy('form_controll_group_id');
        
        return $result;
    }
}
