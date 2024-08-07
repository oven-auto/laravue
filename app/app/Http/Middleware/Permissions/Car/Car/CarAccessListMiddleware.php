<?php

namespace App\Http\Middleware\Permissions\Car\Car;

use Closure;
use Illuminate\Http\Request;

class CarAccessListMiddleware
{
    private const PERMISSION = 'newstock_list';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $userPermissions = auth()->user()->role->permissions;

        if ($userPermissions->contains('slug', self::PERMISSION))
            return $next($request);

        throw new \Exception('Доступ к журналу запрещен');
    }
}
