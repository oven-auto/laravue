<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorTransmission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'acronym', 'transmission_type_id'];

    public $timestamps = false;

    public function type()
    {
    	return $this->hasOne(\App\Models\TransmissionType::class, 'id', 'transmission_type_id');
    }
}
