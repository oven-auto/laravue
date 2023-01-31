<?php

namespace App\Services\Auth;

use App\Models\User;
use Hash;

Class AuthService
{
    public function login($data)
    {
        if(!auth()->attempt($data))
            return \response()->json([
                'success' => 0,
                'message' => 'Пароль или Email не верный',
                'error' => '-'
            ]);

        return \response()->json([
            'laravel_session' => session()->getId(),
            'xsrf-token' => csrf_token(),
            'success' => true,
            'data' => auth()->user(),
            'token' => auth()->user()->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout($data)
    {
        $headers = getallheaders();
        if(isset($headers['Authorization'])) {
            $data = $headers['Authorization'];
            $data = \explode(' ', $data)[1];
            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($data);
            if($token) {
                $user = $token->tokenable;
                $user->tokens()->delete();
            }
            return \response()->json([
                'status' => true,
                'success' => 1,
                'message' => 'Вы вышли из системы',
            ]);
        }
    }

    public function register($data = [])
    {
        $user = User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => 0,
        ]);
        return $user;
    }

    public function check($data = [])
    {
        return response()->json([
            'data1' => auth()->user()->role->permissions,
            'data' => 1
        ]);
    }


}
