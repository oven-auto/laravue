<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getUrlImageAttribute()
    {
        if($this->image)
            return asset('storage'.$this->image) . '?' . date('dmyhm');
        return '';
    }
}
