<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;

class MarkController extends Controller
{

    const MARK_COL = [
        'name', 'prefix', 'sort', 'status', 'brand_id', 'body_work_id', 'country_factory_id'
    ];

    public function index()
    {
        $marks = Mark::with(['icon', 'bodywork', 'brand'])->get();
        if($marks->count())
            return response()->json([
                'status' => 1,
                'data' => $marks,
                'count' => $marks->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одной модели'
        ]);
    }

    public function edit(Mark $mark, Request $request)
    {
        echo 'edit';
    }

    public function store(Mark $mark, Request $request, \App\Repositories\MarkRepository $service)
    {
        $service->saveMark($mark, $request->only(self::MARK_COL));
        $service->saveInfo($mark, $request->get('info'));
        $service->saveProperties($mark, $request->get('properties'));
        $service->saveIcon($mark, $request->icon);
        $service->saveBanner($mark, $request->banner);
        $service->saveDocuments($mark, $request->document);
    }

    public function update(Mark $mark, Request $request)
    {
        echo 'update';
    }
}
