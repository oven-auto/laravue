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

        $user = auth()->user();

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'role_id' => auth()->user()->role->permissions->contains('slug','user_add') ? 1 : 0,
            'lastname' => $user->lastname,
            'role' => $user->role->slug,
            'role_name' => $user->role->name,
        ];

        return \response()->json([
            'laravel_session' => session()->getId(),
            'xsrf-token' => csrf_token(),
            'success' => true,
            'data' => $data,
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
            'name' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'phone' => isset($data['phone']) ? preg_replace("/[^,.0-9]/", '', $data['phone']) : ''
        ]);
        return $user;
    }

    public function update(User $user, $data = [])
    {
        $arr = [
            'name' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'phone' => $data['phone'],//isset($data['phone']) ? preg_replace("/[^,.0-9]/", '', $data['phone']) : ''
        ];

        if(isset($data['password']) && $data['password'] != '')
            $arr['password'] = Hash::make($data['password']);

        $user->fill($arr)->save();
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
