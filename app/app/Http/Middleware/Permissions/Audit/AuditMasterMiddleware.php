<?php

namespace App\Http\Middleware\Permissions\Audit;

use App\Models\Audit\AuditMaster;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AuditMasterMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->user()->id;

        $userPermissions = auth()->user()->role->permissions;

        $route = Route::getCurrentRoute();

        $ref = new \ReflectionMethod($route, 'getControllerMethod');
      
        $ref->setAccessible(true);
        
        $controllerMethod = $ref->invoke($route);

        switch($controllerMethod){
            case 'index':
                if($userPermissions->contains('slug', 'master_index'))
                    return $next($request);
                break; 

            case 'show':
                $audit = AuditMaster::with('trafic')->firstOrFail();
                if($audit->trafic->manager_id == $userId || $userPermissions->contains('slug', 'master_show'))
                    return $next($request);
                break;

            case 'arbitr':
                $audit = AuditMaster::with('trafic')->firstOrFail();
                if($audit->trafic->manager_id == $userId || $userPermissions->contains('slug', 'master_arbitr'))
                    return $next($request);
                break;

            case 'destroy':
                if($userPermissions->contains('slug', 'master_delete'))
                    return $next($request);
                break;

            case 'restore':
                if($userPermissions->contains('slug', 'master_restore'))
                    return $next($request);
                break;  

            case 'store':
                if($userPermissions->contains('slug', 'master_store'))
                    return $next($request);
                break;  

            case 'update':
                if($userPermissions->contains('slug', 'master_update'))
                    return $next($request);
                break;  

            case 'check':
                if($userPermissions->contains('slug', 'master_update'))
                    return $next($request);
                break; 
        }

        throw new \Exception('Отсутствуют права для выполнения данного действия.');
    }
}
