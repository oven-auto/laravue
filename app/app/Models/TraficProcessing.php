<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class TraficProcessing extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getProcentAttribute()
    {
        return $this->result.'%';
    }

    public function getFile($param)
    {
        if(isset($this->$param) && Storage::disk('public')->exists($this->$param))
            return asset('storage/'.$this->$param) . '?' . date('dmyhm');
    }
}
