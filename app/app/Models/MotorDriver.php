<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorDriver extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'acronym', 'driver_type_id'];

    public $timestamps = false;

    public function type()
    {
    	return $this->hasOne(\App\Models\DriverType::class, 'id', 'driver_type_id');
    }
}
