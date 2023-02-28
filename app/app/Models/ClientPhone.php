<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPhone extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getPhoneMaskAttribute()
    {
        $phone = '';
        $from = $this->phone;
        if($this->phone)
            $phone = sprintf("+%s (%s) %s-%s-%s",
                substr($from, 0, 1),
                substr($from, 1, 3),
                substr($from, 3, 3),
                substr($from, 7, 2),
                substr($from, 9)
            );
        return $phone;
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'client_id')->withDefault();
    }
}
