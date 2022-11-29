<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkDocument extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getUrlBrochureAttribute()
    {
        return $this->brochure ? asset('storage'.$this->brochure) . '?' . date('dmyh') : '';
    }

    public function getUrlAccessoryAttribute()
    {
        return $this->accessory ? asset('storage'.$this->accessory) . '?' . date('dmyh') : '';
    }

    public function getUrlManualAttribute()
    {
        return $this->manual ? asset('storage'.$this->manual) . '?' . date('dmyh') : '';
    }

    public function getUrlPriceAttribute()
    {
        return $this->price ? asset('storage'.$this->price) . '?' . date('dmyh') : '';
    }
}
