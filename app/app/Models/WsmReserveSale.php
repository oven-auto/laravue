<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WsmReserveSale extends Model
{
    use HasFactory;

    protected $guarded;

    public $dates = ['date_at'];



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function decorator()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'decorator_id');
    }
}
