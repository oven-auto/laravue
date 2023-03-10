<?php

namespace App\Http\Controllers\Api\v1\Back\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Resources\User\RoleCollection;
use App\Http\Resources\User\RoleSaveResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RoleCollection
     */
    public function index() : RoleCollection
    {
        return new RoleCollection(Role::with('permissions')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RoleSaveResource
     */
    public function store(Role $role, Request $request) : RoleSaveResource
    {
        $role->fill($request->except('permissions'))->save();
        $role->permissions()->sync($request->permissions);
        return (new RoleSaveResource($role))->additional(['message' => 'Роль создана']);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $role
     * @return RoleSaveResource
     */
    public function show(Role $role) : RoleSaveResource
    {
        return (new RoleSaveResource($role));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return RoleSaveResource
     */
    public function update(Role $role, Request $request) : RoleSaveResource
    {
        $role->fill($request->except('permissions'))->save();
        $role->permissions()->sync($request->permissions);
        return (new RoleSaveResource($role))->additional(['message' => 'Роль изменена']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $role
     * @return RoleSaveResource
     */
    public function destroy(Role $role) : RoleSaveResource
    {
        Role::destroy($role->id);
        return (new RoleSaveResource($role))->additional(['message' => 'Роль удалена']);
    }
}
