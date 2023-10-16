<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;

class Worksheet extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'close_at',
    ];

    private const WORKING_STATUSES = [
        'work', 'check'
    ];

    public function getCreatedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d.m.Y') : '';
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'client_id')->withDefault();
    }

    public function trafic()
    {
        return $this->hasOne(\App\Models\Trafic::class, 'id', 'trafic_id')->withDefault();
    }

    public function company()
    {
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id')->withDefault();
    }

    public function structure()
    {
        return $this->hasOne(\App\Models\Structure::class, 'id', 'structure_id')->withDefault();
    }

    public function appeal()
    {
        return $this->hasOne(\App\Models\Appeal::class, 'id', 'appeal_id')->withDefault();
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }

    public function executors()
    {
        return $this->belongsToMany(\App\Models\User::class, 'worksheet_executors', 'worksheet_id');
    }

    public function subclients()
    {
        return $this->belongsToMany(\App\Models\Client::class, 'worksheet_sub_clients', 'worksheet_id');
    }

    public function last_action()
    {
        return $this->hasOne(\App\Models\WorksheetAction::class,'worksheet_id', 'id')->orderBy('id', 'DESC')->withDefault();
    }

    public function actions()
    {
        return $this->hasMany(\App\Models\WorksheetAction::class,'worksheet_id', 'id')->with(['comments','author','task']);
    }

    public function status()
    {
        return $this->hasOne(\App\Models\WorksheetStatus::class, 'slug', 'status_id')->withDefault();
    }

    public function inspector()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'inspector_id')->withDefault();
    }

    public function isWork()
    {
        if(in_array($this->status_id, self::WORKING_STATUSES))
            return 1;
        return 0;
    }
}
