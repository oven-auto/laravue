<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Services\Worksheet\WorksheetUser;
use Illuminate\Http\Request;

class WorksheetReportController extends Controller
{
    public $service;

    public function __construct(WorksheetUser $service)
    {
        $this->service = $service;
    }



    /**
     * ОТЧИТАТЬСЯ ЗА РЛ
     * @param Request $request [worksheet_id => int]
     */
    public function report(Request $request)
    {
        $worksheetId = $request->get('worksheet_id');

        $this->service->report($worksheetId);

        return response()->json([
            'message' => 'Вы отчитались',
            'success' => 1,
        ]);
    }



    /**
     * ОТМЕНИТЬ ОТЧЕТ ЗА РАБОЧИЙ ЛИСТ
     * @param Request $request [worksheet_id = int, user_id = int]
     */
    public function deport(Request $request)
    {
        $worksheetId = $request->get('worksheet_id');
        $userId = $request->get('user_id');

        $this->service->deport($worksheetId, $userId);

        return response()->json([
            'message' => 'Отчет пользователя отменен',
            'success' => 1,
        ]);
    }
}
