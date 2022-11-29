<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarMarker extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['changed_at'];

    protected $dateFormat = 'Y-m-d H:i:s';

    public $timestamps = false;

    public function name()
    {
        return $this->hasOne(\App\Models\Marker::class, 'id', 'marker_id')->withDefault();
    }

    public function moderator()
    {
        return $this->hasOne(\App\Models\User::class,'id','user_id')->withDefault();
    }

    public function getChangeDateAttribute()
    {
        if($this->changed_at)
            return $this->changed_at->format('d.m.Y');
    }
}
