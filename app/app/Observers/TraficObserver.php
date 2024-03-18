<?php

namespace App\Observers;

use Illuminate\Http\Request;

class TraficObserver
{
    public function updating(\App\Models\Trafic $trafic)
    {
        $current = $trafic->trafic_status_id;
        $old = $trafic->getOriginal('trafic_status_id');

        if($current != $old && ($current == 4 || $current == 3))
            $trafic->processing_at = now();
    }

    public function saving(\App\Models\Trafic $trafic)
    {
        $currentMn = $trafic->manager_id;
        $oldMn = $trafic->getOriginal('manager_id');
        if(!$currentMn) {
            if($trafic->company_id != null)
                $trafic->trafic_status_id = 1;
            else
                $trafic->trafic_status_id = 6;
        }
        if($currentMn != $oldMn && $trafic->trafic_status_id != 4)
            $trafic->trafic_status_id = 2;
    }

    public function saved(\App\Models\Trafic $trafic)
    {
        $notice = new \App\Classes\Telegram\Notice\TelegramNotice();

        $current = $trafic->trafic_status_id;
        $old = $trafic->getOriginal('trafic_status_id');

        $userId = auth()->user()->id;

        if($current != $old)
            switch ($current) {
                case 1:
                    $notice->set($trafic)->waiting()->send($trafic->usersIdByTraficAppeal(1));
                    break;
                case 2:
                    if($trafic->manager_id != $userId)
                        $notice->set($trafic)->assign()->send([$trafic->manager_id]);
                    break;
                case 3:
                    if($trafic->author_id != $userId)
                        $notice->set($trafic)->confirm()->send([$trafic->author_id]);
                    break;
                case 4:
                    if($trafic->author_id != $userId)
                        $notice->set($trafic)->confirm()->send([$trafic->author_id]);
                    break;
                case 5:
                    if($trafic->author_id != $userId)
                        $notice->set($trafic)->confirm()->send([$trafic->author_id]);
                    break;
            }
    }

    public function deleted(\App\Models\Trafic $trafic)
    {
        // if($trafic->trafic_status_id != 5)
        //     if(request()->has('clone'))
        //     {
        //         \App\Services\Comment\CommentService::customMessage($trafic, \App\Models\Trafic::NOTICES['clone']);
        //     }
        //     else
        //     {
        //         \App\Services\Comment\CommentService::customMessage($trafic, \App\Models\Trafic::NOTICES['delete']);
        //     }

        $trafic->trafic_status_id = 5;

        $trafic->save();
    }

    public function created(\App\Models\Trafic $trafic)
    {
        //\App\Services\Comment\CommentService::systemMessage($trafic);
    }

    public function updated(\App\Models\Trafic $trafic)
    {
        // if($trafic->trafic_status_id == 4 && $trafic->getOriginal('trafic_status_id') != 4)
        //     \App\Services\Comment\CommentService::customMessage($trafic, \App\Models\Trafic::NOTICES['close']);
        // else
        //     \App\Services\Comment\CommentService::systemMessage($trafic);
    }
}
