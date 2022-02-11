<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::get();
        return response()->json([
            'status' => 1,
            'brands' => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Brand $brand, \App\Http\Requests\Brand\BrandCreateRequest $request)
    {
        $data = $request->only(['name','brand_color', 'font_color']);
        if($request->has('icon')) {
            $iconName = date('Ymdhis').'.'.$request->icon->getClientOriginalExtension();
            $data['icon'] = $request->icon
                ->move(Storage::path('/public/brand/'), $iconName)
                ->getFilename();
        }
    	$brand = $brand->create($data);
        return response()->json([
            'status' => 1,
            'brand' => $brand,
            'message' => 'Новый бренд создан'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        return response()->json([
            'brand' => $brand,
            'status' => 1,
            'icon_src' => asset('storage/brand/'.$brand->icon)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Brand $brand, \App\Http\Requests\Brand\BrandUpdateRequest $request)
    {
        $data = $request->only(['name','brand_color', 'font_color']);
        if($request->hasFile('icon')) {
            $iconName = date('Ymdhis').'.'.$request->icon->getClientOriginalExtension();
            $data['icon'] = $request->icon
                ->move(Storage::path('/public/brand/'), $iconName)
                ->getFilename();
        }
    	$brand->update($data);
        return response()->json([
            'status' => 1,
            'brand' => $brand,
            'message' => 'Бренд изменен'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
