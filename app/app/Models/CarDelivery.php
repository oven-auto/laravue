<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarDelivery extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function stage()
    {
        return $this->hasOne(\App\Models\DeliveryStage::class,'id','delivery_stage_id')->withDefault();
    }
}
