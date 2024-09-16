<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['type', 'author', 'sum', 'reparation', 'reparation_date', 'base'];



    public function modulable()
    {
        return $this->morphTo();
    }



    public function type()
    {
        return $this->hasOne(\App\Models\DiscountType::class, 'id', 'discount_type_id')->withTrashed();
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function sum()
    {
        return $this->hasOne(\App\Models\DiscountSum::class, 'discount_id', 'id');
    }



    public function reparation()
    {
        return $this->hasOne(\App\Models\DiscountReparation::class, 'discount_id', 'id');
    }



    public function reparation_date()
    {
        return $this->hasOne(\App\Models\DiscountReparationDate::class, 'discount_id', 'id');
    }



    public function base()
    {
        return $this->hasOne(\App\Models\DiscountBase::class, 'discount_id', 'id');
    }



    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'id', 'worksheet_id');
    }



    public function scopeWorksheetRelation(Builder $builder)
    {
        $builder->with(['worksheet' => function ($worksheetBuilder) {
            $worksheetBuilder->with(['client']);
        }]);
    }
}
