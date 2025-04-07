<?php

namespace App\Http\Controllers\Api\v1\Back\Audit\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserSmallResource;
use App\Models\Audit\Audit;
use App\Models\Audit\AuditAssist;
use App\Models\Audit\AuditMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditAssistantController extends Controller
{
    public function __construct(
        public $subject = 'Ассистент аудита',
        public $genus = 'male',
    )
    {
        $this->middleware('notice.message')->only(['store', 'update',]);
    }



    /**
     * @OA\Get(
     *      path="/audits/assistant",
     *      operationId="getAuditassistantList",
     *      tags={"Аудит стандартов"},
     *      summary="Список открытых ассистентов в трафике",
     *      description="Список открытых ассистентов в трафике (trafic_id = 1)",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'trafic_id' => 'required',
        ]);

        $assists = AuditAssist::select('id', 'trafic_id', 'audit_id')
            ->where('trafic_id', $validated['trafic_id'])
            ->get();

        return response()->json([
            'data' => $assists->map(function($item) {
                return [
                    'id'            => $item->id,
                    'trafic_id'     => $item->trafic_id,
                    'audit_id'      => $item->audit_id,
                ];
            }),
            'success' => 1,
        ]);
    }



    /**
     * @OA\Get(
     *      path="/audits/assistant/{assistantId}",
     *      operationId="getAuditassistant",
     *      tags={"Аудит стандартов"},
     *      summary="Получить данные ассистента",
     *      description="Получить данные ассистента",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function show(int $id, Request $request)
    {   
        $assist = AuditAssist::findOrFail($id);

        return response()->json([
            'data' => [
                'id'            => $assist->id,
                'audit_id'      => $assist->audit_id,
                'result'        => json_decode($assist->result,1),
                'author'        => new UserSmallResource($assist->author),
                'created_at'    => $assist->created_at->format('d.m.Y'),
                'updated_at'    => $assist->updated_at->format('d.m.Y'),
            ],
            'success' => 1,
        ]);
    }



    /**
     * @OA\Post(
     *      path="/audits/assistant",
     *      operationId="postAuditassistantList",
     *      tags={"Аудит стандартов"},
     *      summary="Создать ассистента в трафике",
     *      description="Создать ассистента в трафике (trafic_id = 1, audit_id = 1, result = strjson)",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trafic_id' => 'required',
            'audit_id'  => 'required',
            'result'    => 'required',
        ]);

        $validated['author_id'] = Auth::id();

        $existed = AuditAssist::query()
            ->where('trafic_id', $validated['trafic_id'])
            ->where('audit_id', $validated['audit_id'])
            ->first();

        if($existed)
            throw new \Exception('Уже существует ассистент для этого аудита в этом трафике.');
        
        $assist = AuditAssist::create($validated);

        AuditMaster::create($request->only(['trafic_id', 'audit_id']));

        return response()->json([
            'data' => [
                'id' => $assist->id,
            ],
            'success' => 1,
        ]);
    }



    /**
     * @OA\Patch(
     *      path="/audits/assistant/{assistantId}",
     *      operationId="patchAuditassistantList",
     *      tags={"Аудит стандартов"},
     *      summary="Изменить ассистента в трафике",
     *      description="Изменить ассистента в трафике (trafic_id = 1, audit_id = 1, result = strjson)",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function update(int $id, Request $request)
    {
        $validated = $request->validate([
            'trafic_id' => 'required',
            'audit_id'  => 'required',
            'result'    => 'required',
        ]);

        $assist = AuditAssist::findOrFail($id);

        $assist->fill($validated)->save();

        return response()->json([
            'data' => [
                'id' => $assist->id,
            ],
            'success' => 1,
        ]);
    }
}
