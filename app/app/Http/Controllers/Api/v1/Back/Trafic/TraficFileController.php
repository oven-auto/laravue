<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use App\Models\TraficFile;
use Illuminate\Http\Request;
use App\Models\Trafic;

class TraficFileController extends Controller
{
    private $service;

    public function __construct(\App\Repositories\Trafic\TraficFileRepository $repo)
    {
        $this->service = $repo;
    }

    public function store(Trafic $trafic, Request $request)
    {
        $this->service->saveTraficFiles($trafic, $request->allFiles());

        return response()->json([
            'data' => $trafic->files->map(function($item) {
                return [
                    'name' => $item->name,
                    'file' => $item->getFile('filepath'),
                    'user' => $item->user->cut_name,
                    'created_at' => !empty($item->created_at) ? $item->created_at->format('d.m.Y (H:i)') : '',
                ];
            }),
            'success' => 1,
            'message' => 'Фаилы загружены',
        ]);
    }

    public function destroy(TraficFile $file)
    {
        //$trafic = Trafic::find($file->trafic_id);
        $file->delete();

        return response()->json([
            'data' => [],
            'message' => 'Фаил удален',
            'success' => 1,
        ]);
    }
}
