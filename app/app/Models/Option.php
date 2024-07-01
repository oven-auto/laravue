<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /** RELATIONS */

    /**
     * BRAND
     */
    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id');
    }



    /**
     * MODEL
     */
    public function mark()
    {
        return $this->hasOne(\App\Models\Mark::class, 'id', 'mark_id');
    }



    /**
     * AUTHOR
     */
    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function prices()
    {
        return $this->hasMany(\App\Models\OptionPrice::class, 'option_id', 'id');
    }



    public function current_price()
    {
        return $this->hasOne(\App\Models\OptionCurrentPrice::class, 'option_id', 'id');
    }



    /**
     * ACCESSORS
     */

    public function getPriceAttribute()
    {
        return $this->current_price->price ?? 0;
    }
}
