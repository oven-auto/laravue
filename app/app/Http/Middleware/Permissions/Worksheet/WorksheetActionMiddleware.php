<?php

namespace App\Http\Middleware\Permissions\Worksheet;

use App\Models\Worksheet;
use App\Models\WorksheetAction;
use Closure;
use Illuminate\Http\Request;

class WorksheetActionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $executorPerm = '', $superPerm = '')
    {
        $user = auth()->user();
        $userPermissions = $user->role->permissions;

        if($request->has('action_id'))
            $worksheet = WorksheetAction::findOrFail($request->action_id)->worksheet;
        elseif($request->has('worksheet_id'))
            $worksheet = Worksheet::findOrFail($request->worksheet_id);
        else
            throw new \Exception('Проблема: недостаточно данных для выполнения запроса');

        if($userPermissions->contains('slug', $superPerm))
            return $next($request);

        if($userPermissions->contains('slug', $executorPerm) && $worksheet->executors->contains('id', $user->id))
            return $next($request);

        throw new \Exception('Нет прав для выполнения запроса в это рабочем листе.
            Вы либо не находитесь в списке ответственных рабочего листа, либо у Вас нет полномочий вносить эти изменения в рабочий лист'
        );
    }
}
