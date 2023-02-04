<?php

namespace App\Http\Middleware\Permissions\Trafic;

use Closure;
use Illuminate\Http\Request;

class TraficShowAlien
{
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
        $userPermissions = auth()->user()->role->permissions;
        //Получение трафик тк ОРМ тут ещё не запущена
        $trafic = \DB::table('trafics')->where('id', $request->trafic)->first();

        //Проверка что трафик с заданным id есть
        if(!$trafic)
            throw new \Exception('Нет такого трафика');

        //Проверка что есть права либо что ответсвенный за трафик тот же кто залогенен
        $boolRes = ($userPermissions->contains('slug', $permission)
            || $trafic->manager_id == auth()->user()->id
            || $trafic->author_id == auth()->user()->id
        );

        if($boolRes)
            return $next($request);

        //Получение текущего права, что бы вернуть исключение
        $permission = \App\Models\Permission::where('slug', $permission)->first();
        throw new \Exception('Отсутствуют права "'.$permission->name.'"');
    }
}
