<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class TraficFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getFile($param)
    {
        if(isset($this->$param) && Storage::disk('public')->exists($this->$param))
            return asset('storage/'.$this->$param) . '?' . date('dmyhm');
    }

    public function user()
    {
        return $this->hasOne(\App\Models\User::class,'id', 'user_id');
    }

    public function trafic()
    {
        return $this->hasOne(\App\Models\Trafic::class, 'id', 'trafic_id')->withDefault();
    }
}
