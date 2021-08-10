<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceBrand extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id', 'device_id'];

    public $timestamps = false;
}
