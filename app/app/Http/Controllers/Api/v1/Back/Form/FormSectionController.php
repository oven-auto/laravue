<?php

namespace App\Http\Controllers\Api\v1\Back\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormSection;

class FormSectionController extends Controller
{
    public function index()
    {
        $data = FormSection::with('form')->orderBy('brand_id')->get();
        $grouped = $data->groupBy('parent_id');

        foreach ($data as $key=>$item) {
            if ($grouped->has($item->id)) {
                $item->childrens = $grouped[$item->id];
            }
        }

        return response()->json([
            'data' => $data->where('parent_id', null),
            'count' => $data->count(),
            'status' => $data->count() ? 1 : 0,
            'message' => $data->count() ? '' : 'Ничего не нашлось'
        ]);
    }

    public function edit(FormSection $formsection)
    {
        return response()->json([
            'data' => $formsection,
            'status' => 1
        ]);
    }

    public function store(Request $request, FormSection $formsection)
    {
        $data = $request->all();
        $formsection->fill($data)->save();
        return response()->json([
            'status' => 1,
            'data' => $formsection
        ]);
    }

    public function update(Request $request, FormSection $formsection)
    {
        $data = $request->all();
        $formsection->fill($data)->save();
        return response()->json([
            'status' => 1,
            'data' => $formsection
        ]);
    }
}
