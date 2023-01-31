<?php

namespace App\Http\Middleware\Worksheet;

use Closure;
use Illuminate\Http\Request;
use App\Http\Requests\Worksheet\WorksheetStoreRequest;

class WorksheetCreatingMiddleware
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

        if(!isset($trafic->id))
            throw(new \Exception('Не найден трафик'));

        if(!$trafic->phone)
            throw(new \Exception('Для создания рабочего листа необходим телефон'));

        if($trafic->manager_id != auth()->user()->id)
            throw new \Exception('Создать рабочий лист может только ответственный за трафик');

        if($trafic->trafic_status_id == 3)
            throw new \Exception('Этот трафик уже имеет рабочий лист');

        return $next($request);
    }
}
