<?php

namespace App\Http\Controllers\Api\v1\Back\Bodywork;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bodywork\BodyworkSaveRequest;
use App\Http\Resources\Bodywork\BodyworkSaveResource;
use App\Models\BodyWork;
use App\Repositories\Bodywork\BodyworkRepository;

class BodyworkController extends Controller
{
    private $repo;

    public function __construct(BodyworkRepository $repo)
    {
        $this->repo = $repo;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => BodyWork::get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'vehicle' => $item->vehicle->name,
                    'acronym' => $item->acronym
                ];
            }),
            'success' => 1,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BodyWork $bodywork, BodyworkSaveRequest $request)
    {
        $this->repo->save($bodywork, $request->validated());

        return response()->json([
            'data' => new BodyworkSaveResource($bodywork),
            'success' => 1,
            'message' => 'Кузов добавлен.'
        ]);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BodyWork $bodywork)
    {
        return response()->json([
            'data' => new BodyworkSaveResource($bodywork),
            'success' => 1,
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BodyWork $bodywork, BodyworkSaveRequest $request)
    {
        $this->repo->save($bodywork, $request->validated());

        return response()->json([
            'data' => new BodyworkSaveResource($bodywork),
            'success' => 1,
            'message' => 'Кузов изменен.'
        ]);
    }
}
