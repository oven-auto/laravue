<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use Illuminate\Http\Request;
use \App\Models\Trafic;
use Auth;
use App\Models\Permission;
use DB;

class TraficChangeMiddleware
{
    public function boot(Router $router)
    {
        parent::boot($router);

        $router->model('trafic', '\App\Models\Trafic');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        //Все права пользователя из роли
        $userPermissions = Auth::user()->role->permissions;
        //Получение трафик тк ОРМ тут ещё не запущена
        $trafic = DB::table('trafics')->where('id', $request->trafic)->first();

        //Проверка что трафик с заданным id есть
        if(!$trafic)
            throw new \Exception('Нет такого трафика');

        //Проверка что есть права либо что ответсвенный за трафик тот же кто залогенен
        if($userPermissions->contains('slug', $permission) || $trafic->manager_id == Auth::user()->id)
            return $next($request);

        //Получение текущего права, что бы вернуть исключение
        $permission = Permission::where('slug', $permission)->first();
        throw new \Exception('Отсутствуют права "'.$permission->name.'"');
    }
}
