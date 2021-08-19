<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credit;
use Storage;

class CreditController extends Controller
{
    public function index()
    {
        $credits = Credit::with('marks')->get();
        if($credits->count())
            return response()->json([
                'status' => 1,
                'data' => $credits,
                'count' => $credits->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного кредита'
        ]);
    }

    public function edit(Credit $credit)
    {
        $data = $credit->toArray();
        $data['marks'] = $credit->marks->pluck('id');
        $data['banner'] = asset('storage/'.$credit->banner) . '?'.date('dmYhms');
        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function store(Credit $credit, Request $request)
    {
        $data = $request->except(['banner', 'marks']);

        if($request->has('banner') && $request->hasFile('banner')) {
            $bannerName = 'banner_'.date('dmyhms').'.'.$request->banner->getClientOriginalExtension();
            $path = '/public/credit/';
            $urlPath = '/credit/';
            $finalName = $urlPath.'/'.$request->banner->move(Storage::path($path), $bannerName)->getFilename();
            $data['banner'] = $finalName;
        }
        $credit->fill($data)->save();
        $credit->marks()->sync($request->get('marks'));

        return response()->json([
            'status' => 1,
            'data' => $credit,
            'message' => 'Кредит создан'
        ]);
    }

    public function update(Credit $credit, Request $request)
    {
        $data = $request->except(['banner', 'marks']);

        if($request->has('banner') && $request->hasFile('banner')) {
            $bannerName = 'banner_'.date('dmyhms').'.'.$request->banner->getClientOriginalExtension();
            $path = '/public/credit/';
            $urlPath = '/credit/';
            $finalName = $urlPath.'/'.$request->banner->move(Storage::path($path), $bannerName)->getFilename();
            $data['banner'] = $finalName;
        }
        $credit->fill($data)->save();
        $credit->marks()->sync($request->get('marks'));

        return response()->json([
            'status' => 1,
            'data' => $credit,
            'message' => 'Кредит обновлен'
        ]);
    }
}
