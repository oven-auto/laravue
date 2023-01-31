<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class UserWithTokenMiddleware
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
        //$request->merge(['test' => $request->all()]);

        $headers = getallheaders();
        if(isset($headers['Authorization'])) {
            $data = $headers['Authorization'];
            $data = \explode(' ', $data);

            if(count($data)<2)
                return \response()->json([
                    'success' => 0,
                    'message' => 'Вы не авторизованы или авторизованы не правильно',
                    'error' => 'Ошибка авторизации'
                ], 401);

            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($data[1]);

            if($token) {
                $user = $token->tokenable;
                //$request->merge(['user' => $user]);
                Auth::login($user);
                return $next($request);
            } else {
                return \response()->json([
                    'success' => 0,
                    'message' => 'Вы не авторизованы или Ваша сессия устарела',
                    'error' => 'Ошибка авторизации'
                ], 401);
            }
        } else
            return \response()->json([
                'success' => 0,
                'message' => 'Вы не авторизованы, сходите авторизуйтесь',
                'error' => 'Ошибка авторизации'
            ], 401);
    }
}
