<?php

namespace App\Http\Controllers\Api\v1\Back\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserSaveResource;
use App\Http\Requests\User\UserCreate;

class UserController extends Controller
{
    private $service;

    public function __construct(\App\Services\Auth\AuthService $service)
    {
        $this->service = $service;

        $this->middleware('permission.developer:user_list')->only('index');
        $this->middleware('permission.developer:user_edit')->only('update');
        $this->middleware('permission.developer:user_delete')->only('delete');
        $this->middleware('permission.developer:user_show')->only('show');
        $this->middleware('permission.developer:user_add')->only('store');
    }
    /**
     * Display a listing of the resource.
     *
     * @return UserCollection
     */
    public function index(Request $request)
    {
        $query = User::with('role')->orderBy('lastname');
        if($request->has('input') && $request->get('input')!='')
            $query->where('lastname', 'LIKE', "%$request->input%");

        $users = $query->get();
        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return UserSaveResource
     */
    public function store(UserCreate $request)
    {
        $user = $this->service->register($request->input());
        return (new UserSaveResource($user))
            ->additional(['message' => 'Пользователь создан']);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return UserSaveResource
     */
    public function show(User $user)
    {
        return new UserSaveResource($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User $user
     * @return UserSaveResource
     */
    public function update(User $user, UserCreate $request)
    {
        $user = $this->service->update($user, $request->input());
        return (new UserSaveResource($user))
            ->additional(['message' => 'Пользователь создан']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return UserSaveResource
     */
    public function destroy(User $user)
    {
        $old = $user->toArray();
        $user->delete();
        return (new UserSaveResource((object) $old))
            ->additional(['message' => 'Пользователь '.$old['lastname'].' '.$old['name'].' удален']);
    }
}
