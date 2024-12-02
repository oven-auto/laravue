<?php

namespace App\Http\Controllers\Api\v1\Back\TaskList;

use App\Http\Controllers\Controller;
use App\Models\ClientEventStatus;
use App\Models\SubAction;
use App\Models\Trafic;
use App\Models\WorksheetAction;
use Illuminate\Support\Facades\Auth;

class OverdueCountController extends Controller
{
    public function index()
    {
        $user = Auth::id();

        $trafic = Trafic::query()
            ->leftJoin('trafic_controls', 'trafic_controls.trafic_id', 'trafics.id')
            ->where(function($query) use ($user){
                $query->where('trafics.manager_id', $user);
                $query->whereIn('trafics.trafic_status_id', [1,2,6]);
                $query->whereDate('trafic_controls.end_at', '<', now());
            })->count();

        $worksheet = WorksheetAction::query()
            ->leftJoin('worksheet_executors', 'worksheet_executors.worksheet_id', 'worksheet_actions.worksheet_id')
            ->where('worksheet_executors.user_id', $user)
            ->whereDate('end_at', '<', now())
            ->where('worksheets.status', 'work')
            ->count() ?? 0;

        $subAction = SubAction::query()
            ->leftJoin('sub_action_executors', 'sub_action_executors.sub_action_id', 'sub_actions.id')
            ->where('sub_action_executors.user_id', $user)
            ->WhereDate('sub_actions.created_at', '<', now()->addHour(1))
            ->where('sub_actions.status', 1)
            ->count() ?? 0;

        $events = ClientEventStatus::query()
            ->leftJoin('client_event_status_executors', 'client_event_status_executors.client_event_status_id', 'client_event_statuses.id')
            ->where(function($query) use ($user){
                $query->where('client_event_status_executors.user_id', $user);
                $query->where('client_event_statuses.confirm', 'waiting');
                $query->whereDate('client_event_statuses.date_at', '<', now());
            })->count();

        return response()->json([
            'data' => [
                'trafics' => $trafic,
                'worksheets' => $subAction + $worksheet,
                'events' => $events
            ],
            'success' => 1
        ]);
    }
}
