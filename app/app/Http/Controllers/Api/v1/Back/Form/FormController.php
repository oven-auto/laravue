<?php

namespace App\Http\Controllers\Api\v1\Back\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormSection;
use App\Services\Form\FormRepository;

class FormController extends Controller
{
    private $repository;

    public function __construct(FormRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request, Form $form)
    {
        $data = $this->repository->save($form, $request->all());

        return response()->json([
            'message' => $data['status'] == 1 ? 'Форма создана' : $data['message'],
            'status' => $data['status'],
            'data' => $data['data']
        ]);
    }

    public function edit($form, Request $request)
    {
        $data = $this->repository->getFormById($form);

        return response()->json([
            'data'=>$data,
            'status'=>1
        ]);
    }

    public function update(Form $form, Request $request)
    {
        $data = $this->repository->save($form, $request->all());

        return response()->json([
            'message' => $data['status'] == 1 ? 'Форма обновлена' : $data['message'],
            'status' => $data['status'],
            'data' => $data['data']
        ]);
    }

    public function destroy(Form $form) {
        $form->delete();
        return response()->json([
            'status' => 1
        ]);
    }

    public function controlls()
    {
        $data = \App\Models\FormControllGroup::with('elements')->get();
        return response()->json([
            'data' => $data,
            'status' => $data->count() ? 1 : 0
        ]);
    }
}
