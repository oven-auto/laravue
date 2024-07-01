<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarDateLogistic extends Model
{
    use HasFactory;

    /**
     * В ЭТОЙ ТАБЛИЧКЕ 2 ТРИГЕРА, НА СОЗДАНИЕ, И НА УДАЛЕНИЕ, Которые МЕНЯЮТ СТАТУС МАШИНЫ
     */

    protected $guarded = [];

    protected $with = ['state'];

    public $dates = ['created_at', 'updated_at', 'date_at'];

    /**RELATIONS */

    /**AUTHOR */
    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    /**
     * LOGISTIC STATE
     */
    public function state()
    {
        return $this->hasOne(\App\Models\LogisticState::class, 'system_name', 'logistic_system_name');
    }
}
