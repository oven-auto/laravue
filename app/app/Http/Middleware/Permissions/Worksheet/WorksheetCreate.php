<?php

namespace App\Http\Middleware\Permissions\Worksheet;

use Closure;
use Illuminate\Http\Request;

class WorksheetCreate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $trafic = \App\Models\Trafic::find($request->trafic_id);
        $user = auth()->user();

        if($user->role->permissions->contains('slug','worksheet_add_for_all'))
            return $next($request);

        if($user->role->permissions->contains('slug','worksheet_add_me'))
            if($user->id == $trafic->manager_id)
                return $next($request);

        if($user->role->permissions->contains('slug', 'worksheet_add_company'))
            if($user->structures->contains('company_id', $trafic->company_id))
                return $next($request);

        throw new \Exception('У вас нет права создавать рабочий лист из этого трафика');
    }
}
