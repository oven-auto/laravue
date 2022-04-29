<?php

namespace App\Http\Controllers\Api\v1\Back\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormSection;

class FormController extends Controller
{
    public function store(Request $request, Form $form)
    {
        $section = FormSection::find($request->get('form_section_id'));
        $form->fill($request->except(['recipients','bodies']));
        //$form->body = json_encode($request->body);
        $form->brand_id = $section->brand_id;
        $form->save();
        $form->recipients()->sync($request->get('recipients'));
        $form->bodies()->sync($request->get('bodies'));
        return response()->json([
            'data' => $form,
            'status' => 1
        ]);
    }

    public function edit(Form $form, Request $request)
    {
        $data = $form->toArray();
        $data['bodies'] = $form->bodies->pluck('id');
        $data['recipients'] = $form->recipients->pluck('id');
        return response()->json([
            'data'=>$data,
            'status'=>1
        ]);
    }

    public function update(Form $form, Request $request)
    {
        $section = FormSection::find($request->get('form_section_id'));
        $form->fill($request->except(['recipients','bodies']));
        //$form->body = json_encode($request->body);
        $form->brand_id = $section->brand_id;
        $form->save();
        $form->recipients()->sync($request->get('recipients'));
        $form->bodies()->sync($request->get('bodies'));
        return response()->json([
            'data' => $form,
            'status' => 1
        ]);
    }

    public function controlls()
    {
        $data = \App\Models\FormControll::get();
        return response()->json([
            'data' => $data,
            'status' => $data->count() ? 1 : 0
        ]);
    }
}
