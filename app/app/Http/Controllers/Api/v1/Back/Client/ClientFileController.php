<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientFile;
use App\Http\Requests\Client\FileRequest;

class ClientFileController extends Controller
{
    private $repo;

    public function __construct(\App\Repositories\Client\ClientFile $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Список всех фаилов клиента где в request параметре client_id передается id клиента
     * @param Request $request Illuminate\Http\Request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(FileRequest $request)
    {
        $files = $this->repo->getByParam($request->all());
        return new \App\Http\Resources\Client\File\IndexCollection($files);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(ClientFile $file, FileRequest $request)
    {
        $files = $this->repo->save($request->input(), $request->allFiles());
        return (new \App\Http\Resources\Client\File\IndexCollection($files))
            ->additional(['message' => 'Фаил успешно добавлен']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(ClientFile $file, FileRequest $request)
    {
        $files = $this->repo->save($request->input(), $request->allFiles());
        return (new \App\Http\Resources\Client\File\IndexCollection($files))
            ->additional(['message' => 'Информация о фаиле изменена']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ClientFile $file)
    {
        $file->delete();
        return response()->json([
            'message' => "Фаил удален",
            'success' => 1
        ]);
    }
}
