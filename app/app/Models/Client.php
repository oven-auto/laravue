<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Createable;
use App\Models\Traits\Filterable;

class Client extends Model
{
    use HasFactory, Createable, Filterable;

    protected $guarded = [];

    public function phones()
    {
        return $this->hasMany(\App\Models\ClientPhone::class, 'client_id', 'id');
    }

    public function emails()
    {
        return $this->hasMany(\App\Models\ClientEmail::class, 'client_id', 'id');
    }

    public function type()
    {
        return $this->hasOne(\App\Models\ClientType::class,'id','client_type_id')->withDefault();
    }

    public function sex()
    {
        return $this->hasOne(\App\Models\TraficSex::class,'id','client_sex_id')->withDefault();
    }

    public function zone()
    {
        return $this->hasOne(\App\Models\TraficZone::class,'id','client_zone_id')->withDefault();
    }





    public static function findByPhone($phone_number)
    {
        $result = self::select('clients.*')
            ->join('client_phones', 'client_phones.client_id', '=', 'clients.id')
            ->where('client_phones.phone', $phone_number)
            ->first();

        return $result ?? new Client();
    }
}
