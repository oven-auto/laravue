<?php

namespace App\Http\Middleware\Permissions\Trafic;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraficClose
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
        if($userPermission->contains('slug', 'trafic_close_alien'))
            return $next($request);
        
        //Если есть право удалять любой свой
        if($userPermission->contains('slug', 'trafic_close') && $trafic->manager_id == Auth::id())
            return $next($request);
        
        //Если есть парво удалять только свой ожидающий
        if($userPermission->contains('slug', 'trafic_close_waiting_author') && $trafic->isWaiting() && $trafic->author_id = Auth::id())
            return $next($request);

        throw new \Exception('Доступ ограничен! Вы не можете упустить/удалить.');
    }
}
