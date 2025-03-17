<?php

namespace App\Http\Middleware\Permissions\Trafic;

use App\Models\Trafic;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraficDelete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {  
        $trafic = $request->trafic;

        //Если статус 3 (принят) то ошибка
        if($trafic->isWorking())
            throw new \Exception('Трафик принят, его нельзя упустить/удалить.');

        $userPermission = auth()->user()->role->permissions;

        //если супер права
        if($userPermission->contains('slug', 'trafic_softdelete_alien'))
            return $next($request);
        
        //Если есть право удалять любой свой
        if($userPermission->contains('slug', 'trafic_softdelete') && $trafic->manager_id == Auth::id())
            return $next($request);
        
        //Если есть парво удалять только свой ожидающий
        if($userPermission->contains('slug', 'trafic_delete_waiting_author') && $trafic->isWaiting() && $trafic->author_id = Auth::id())
            return $next($request);
        
        //Если есть право на отдел
        if (Trafic::checkTrafic('all', $trafic, 'trafic_softdelete_appeals'))
            return $next($request);

        throw new \Exception('Доступ ограничен! Вы не можете упустить/удалить.');
    }
}
