<?php

namespace App\Http\Controllers\Api\v1\Back\TaskList;

use App\Http\Controllers\Controller;
use App\Models\ClientEventStatus;
use App\Models\Trafic;
use App\Models\Worksheet;
use Illuminate\Http\Request;
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
                $query->whereTime('trafic_controls.end_at', '<', now());
            })->count();

        $worksheet = Worksheet::query()
            ->leftJoin('worksheet_actions', 'worksheet_actions.worksheet_id', 'worksheets.id')
            ->leftJoin('worksheet_executors', 'worksheet_executors.worksheet_id', 'worksheets.id')
            ->leftJoin('sub_actions', 'sub_actions.worksheet_id', 'worksheets.id')
            ->where(function($query) use ($user){
                $query->where('worksheet_executors.user_id', $user);
                $query->where(function($subQ){
                    $subQ->whereTime('end_at', '<', now());
                    $subQ->orWhereTime('sub_actions.created_at', '<', now()->addHour(1));
                });
            })->count();

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
                'worksheets' => $worksheet,
                'events' => $events
            ],
            'success' => 1
        ]);
    }
}
