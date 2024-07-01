<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Tuning;

use App\Http\Controllers\Controller;
use App\Http\Requests\Car\Tuning\TuningSaveRequest;
use App\Http\Resources\Car\TuningCollection;
use App\Http\Resources\Car\TuningItemResource;
use Illuminate\Http\Request;
use App\Models\Tuning;
use App\Repositories\Car\Tuning\TuningRepository;

class TuningController extends Controller
{
    private $repo;

    public function __construct(TuningRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Request $request)
    {
        $tunings = $this->repo->get($request->all());

        return new TuningCollection($tunings);
    }



    public function store(Tuning $tuning, TuningSaveRequest $request)
    {
        $this->repo->save($tuning, $request->validated());

        return (new TuningItemResource($tuning))
            ->additional(['message' => 'Тюнинг создан']);
    }



    public function update(Tuning $tuning, TuningSaveRequest $request)
    {
        $this->repo->save($tuning, $request->validated);

        return (new TuningItemResource($tuning))
            ->additional(['message' => 'Тюнинг изменен']);
    }



    public function show(Tuning $tuning)
    {
        return (new TuningItemResource($tuning));
    }



    public function destroy(Tuning $tuning)
    {
        $this->repo->destroy($tuning);

        return response()->json([
            'success' => 1,
            'message' => 'Тюнинг удален'
        ]);
    }



    public function restore(Tuning $tuning)
    {
        $this->repo->restore($tuning);

        return response()->json([
            'success' => 1,
            'message' => 'Тюнинг актуален'
        ]);
    }
}
