<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Repositories\BannerRepository;

class BannerController extends Controller
{

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {   
        $banners = Banner::with('brand')->get();
        foreach($banners as $itemBanner)
            $itemBanner->image = asset('storage'.$itemBanner->image).'?'.date('dmYhms');

        if($banners->count())
            return response()->json([
                'status' => 1,
                'data' => $banners,
                'count' => $banners->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного банера'
        ]);
    }

    public function edit(Banner $banner)
    {
        $banner->image = asset('storage/'.$banner->image) . '?'.date('dmYhms');
        return response()->json([
            'status' => 1,
            'data' => $banner->toArray()
        ]);
    }

    public function store(Banner $banner, Request $request)
    {
        $result = $this->repository->save($banner,$request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $banner,
                'message' => 'Банер создан'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $banner,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }

    public function update(Banner $banner, Request $request)
    {
        $result = $this->repository->save($banner,$request->all());

        if($result['status'])
            return response()->json([
                'status' => 1,
                'data' => $banner,
                'message' => 'Банер изменен'
            ]);
        else
            return response()->json([
                'status' => 0,
                'data' => $banner,
                'errors' => $result['error'],
                'message' => 'Произошла ошибка'
            ]);
    }
}
