<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use Illuminate\Http\Request;
use Auth;

class TraficPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission, $trafic='')
    {
        $userPermissions = Auth::user()->role->permissions;

        foreach($userPermissions as $item)
            if($item->slug == $permission)
                 return $next($request);

        throw new \Exception('Недостаточно прав');
    }
}
