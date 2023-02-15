<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class TraficProcessing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getProcentAttribute()
    {
        return $this->result.'%';
    }

    public function getStatusResultAttribute()
    {
        if($this->status == 0)
            return 'Не пройден';
        return 'Пройден';
    }

    public function getFile($param)
    {
        if(isset($this->$param) && Storage::disk('public')->exists($this->$param))
            return asset('storage/'.$this->$param) . '?' . date('dmyhm');
    }

    public function standart()
    {
        return $this->hasOne(\App\Models\AuditStandart::class, 'id','audit_standart_id')->withDefault();
    }

    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'id' ,'user_id')->withDefault();
    }
}
