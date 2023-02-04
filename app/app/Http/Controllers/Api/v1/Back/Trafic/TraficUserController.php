<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class TraficUserController extends Controller
{
    public function index($structure_id, $appeal_id)
    {
        $users = User::select(
                'users.id','users.name','users.lastname',
                \DB::raw('sum(case when date(trafics.created_at) = curdate() then 1 else 0 end) as d_count'),
                \DB::raw('sum(
                        case when
                            year(trafics.created_at) = year(now()) and
                            month(trafics.created_at) = month(now())
                        then 1
                        else 0
                        end
                    ) as m_count
                ')
            )
            ->leftJoin('appeal_users', 'appeal_users.user_id', '=', 'users.id')
            ->leftJoin('trafic_appeals', 'trafic_appeals.appeal_id', '=',  'appeal_users.appeal_id')
            ->leftJoin('trafics', 'trafics.manager_id','users.id')
            ->where('trafic_appeals.id', $appeal_id)
            ->where('trafic_appeals.company_structure_id', $structure_id)
            ->groupBy('users.id')
            ->get();

        $data = [];

        foreach($users as $itemUser)
            $data[] = [
                'id'=>$itemUser->id,
                'name' => $itemUser->cut_name,
                'd_count' => $itemUser->d_count,
                'm_count' => $itemUser->m_count
            ];

        return \response()->json([
            'data' =>  $data,
            'success' => 1,
        ]);
    }
}
