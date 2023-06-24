<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientEvent extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $guarded = [];

    public function getExecutorAttribute()
    {
        if($this->executors->contains('id', auth()->user()->id))
            return auth()->user()->cut_name;
        $other = ($this->executors->count() > 1) ? (' (+'.($this->executors->count()-1).')') : '';
        return $this->author->cut_name.$other;
    }

    public function group()
    {
        return $this->hasOne(\App\Models\EventGroup::class, 'id', 'group_id')->withDefault();
    }

    public function type()
    {
        return $this->hasOne(\App\Models\EventType::class, 'id', 'type_id')->withDefault();
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'client_id')->withDefault();
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class,'id','author_id')->withDefault();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\ClientEventComment::class, 'event_id', 'id')->orderBy('id','DESC');
    }

    public function lastComment()
    {
        return $this->hasOne(\App\Models\ClientEventComment::class, 'event_id', 'id')->orderBy('id','DESC')->withDefault();
    }

    public function executors()
    {
        return $this->belongsToMany(\App\Models\User::class, 'client_event_executors', 'event_id', 'executor_id', 'id');
    }

    public function statuses()
    {
        return $this->hasMany(\App\Models\ClientEventStatus::class, 'event_id', 'id');
    }

    public function lastStatus()
    {
        return $this->hasOne(\App\Models\ClientEventStatus::class, 'event_id', 'id')->orderBy('id','DESC')->withDefault();
    }

    public function files()
    {
        return $this->hasMany(\App\Models\ClientEventFile::class, 'event_id', 'id');
    }
}
