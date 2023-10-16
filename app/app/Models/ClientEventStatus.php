<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;

class ClientEventStatus extends Model
{
    use HasFactory, Filterable;

    public function getBeginAttribute()
    {
        return date('H:i', strtotime($this->begin_time));
    }

    public function getEndAttribute()
    {
        return date('H:i', strtotime($this->end_time));
    }

    public function getTimeStatusAttribute()
    {
        if($this->begin_time == '09:00:00' && $this->end_time == '21:00:00')
            return 0;
        return 1;
    }

    public $with = ['completer','description'];

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'processed_at',
        'date_at',
    ];

    public function scopeOnlyTableData($query)
    {
        return $query->select('client_event_statuses.*');
    }

    public function scopeListOrder($query)
    {
        return $query
            ->groupBy('client_event_statuses.id')
            ->orderBy('client_event_statuses.date_at', 'ASC')
            ->orderBy('client_event_statuses.begin_time')
            ->orderBy('client_event_statuses.confirm', 'ASC')
            ->orderBy('client_event_statuses.id', 'ASC');
    }

    public function scopeWithEventAndTrafic($query)
    {
        return $query->with(['event.files','trafic', 'trafics', 'reporters', 'completer']);
    }

    public function getStatusAttribute()
    {
        return $this->description->name;
    }

    public function isWork()
    {
        if($this->confirm == 'waiting')
            return 1;
        return 0;
    }



    public function completer()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }

    public function description()
    {
        return $this->hasOne(\App\Models\ClientEventStatusDescription::class, 'confirm', 'confirm')->withDefault();
    }

    public function event()
    {
        return $this->hasOne(\App\Models\ClientEvent::class, 'id', 'event_id')->withDefault()->with([
            'group', 'type', 'client', 'author', 'executors',
        ]);
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\ClientEventComment::class, 'client_event_status_id', 'id');
    }

    public function lastComment()
    {
        return $this->hasOne(\App\Models\ClientEventComment::class, 'client_event_status_id', 'id')->orderBy('id', 'DESC')->withDefault();
    }

    public function trafic()
    {
        return $this->hasOneThrough(
            \App\Models\Trafic::class,
            \App\Models\ClientEventTrafic::class,
            'event_status_id',
            'id',
            'id',
            'trafic_id'
        )->withDefault();
    }

    public function trafics()
    {
        return $this->hasManyThrough(
            \App\Models\Trafic::class,
            \App\Models\ClientEventTrafic::class,
            'event_status_id',
            'id',
            'id',
            'trafic_id'
        );
    }

    public function reporters()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'client_event_reporters',
            'event_id',
            'user_id',
            'id'
        )->withPivot('created_at');
    }
}
