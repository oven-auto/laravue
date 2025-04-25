<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WSMReservePlannedPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $table = 'wsm_reserve_planned_payments';

    protected $casts = [
        'date_at' => 'datetime',
    ];



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function deal_type()
    {
        return $this->hasOne(\App\Models\Payment::class, 'id', 'type_id');
    }
}
