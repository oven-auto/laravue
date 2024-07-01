<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WsmReservePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $dates = ['date_at'];

    /**
     * RELATIONS
     */

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function reserve()
    {
        return $this->hasOne(\App\Models\WsmReserveNewCar::class, 'id', 'reserve_id');
    }



    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class, 'id', 'payment_id');
    }
}
