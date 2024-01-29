<?php

namespace App\Http\Middleware\Permissions\Trafic;

use App\Models\Trafic;
use Closure;
use Illuminate\Http\Request;
use \Illuminate\Pipeline\Pipeline;

class TraficShow
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission )
    {
        $userPermissions = auth()->user()->role->permissions;

        foreach($userPermissions as $item)
            if($item->slug == $permission)
                 return $next($request);

        $trafic = Trafic::findOrFail($request->trafic);
        if($trafic->isTraficFromEnabledWorksheet())
            return $next($request);

        //Получение текущего права, что бы вернуть исключение
        $permission = \App\Models\Permission::where('slug', $permission)->first();
        throw new \Exception('Отсутствуют права "'.$permission->name.'"');
    }
}
