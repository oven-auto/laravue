<?php

namespace App\Http\Controllers\Integration\PotokBit;

use App\Http\Controllers\Controller;
use App\Http\Requests\PotokBitCreateRequest;
use App\Models\Company;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;

class PotokBitController extends Controller
{
    private $repo;

    public function __construct(TraficRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Trafic $trafic, PotokBitCreateRequest $request)
    {
        $validated = $request->validate([
            'firstname' => 'nullable|sometimes|string',
            'lastname' => 'nullable|sometimes|string',
            'phone' => 'required|digits:11',
            'link' => 'required',
            'comment' => 'required|string',
        ]);

        $company = Company::find(2);

        $structureId = $company->structures->where('id', 1)->first()->pivot->id;

        $data = [
            'author_id' => auth()->user()->id,
            'firstname' => $validated['firstname'] ?? '',
            'lastname' => $validated['lastname'] ?? '',
            'phone' => $validated['phone'],
            'comment' => $validated['comment'],
            'company_id' => $company->id,
            'company_structure_id' => $structureId,
            'client_type_id' => 1,
            'trafic_chanel_id' => 43,
            'interval' => 15,
            'begin_at' => now(),
            'end_at' => now()->addMinutes(15),
        ];

        $trafic = Trafic::create($data);

        $this->repo->saveLink($trafic, $request->link);

        return response()->json([
            'success' => 1,
        ], 200);
    }
}
