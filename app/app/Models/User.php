<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'lastname'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeByStructure($query, $structure_id)
    {
        return $query
            ->select('users.id','users.name','users.lastname')
            ->leftJoin('user_company_structures','user_company_structures.user_id','=','users.id')
            ->where('user_company_structures.company_structure_id', '=', $structure_id);
    }

    public function getCutNameAttribute()
    {
        if($this->name && $this->lastname)
            return "$this->lastname ".mb_substr($this->name,0,1).'.';
        return '';

    }

    public function structures()
    {
        return $this->hasmany(\App\Models\UserCompanyStructure::class,'user_id','id');
    }

    public function role()
    {
        return $this->hasOne(\App\Models\Role::class, 'id', 'role_id')->withDefault();
    }

    public function permissions()
    {
       return $this->belongsToMany(\App\Models\Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }
}
