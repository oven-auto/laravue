<?php

namespace App\Repositories\User;

use App\Models\User;
use DB;

Class UserRepository
{
    public function getListWithCoutTrafic($structure_id, $appeal_id)
    {
        $users = User::counterSelect()
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

        return $data;
    }
}