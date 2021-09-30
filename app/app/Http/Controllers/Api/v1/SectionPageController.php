<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectionPage;

class SectionPageController extends Controller
{
    public function index(Request $request)
    {
        $query = SectionPage::select('*');
        if($request->has('brand_id'))
            $query->where('brand_id',$request->get('brand_id'));
        $sectionpages = $query->get();
        if($sectionpages->count())
            return response()->json([
                'status' => 1,
                'data' => $sectionpages,
                'count' => $sectionpages->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного раздела'
        ]);
    }

    public function edit(SectionPage $sectionpage)
    {
        return response()->json([
            'status' => 1,
            'data' => $sectionpage
        ]);
    }

    public function store(SectionPage $sectionpage, Request $request)
    {
        $sectionpage->fill($request->all())->save();

        return response()->json([
            'status' => 1,
            'data' => $sectionpage,
            'message' => 'Раздел создан'
        ]);
    }

    public function update(SectionPage $sectionpage, Request $request)
    {
        $sectionpage->fill($request->all())->save();

        return response()->json([
            'status' => 1,
            'data' => $sectionpage,
            'message' => 'Раздел изменен'
        ]);
    }
}
