<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Createable;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, Createable, Filterable, SoftDeletes;

    protected $guarded = [];

    public function loyalty()
    {
        return 'Неизвестно';
    }

    public function getFullNameAttribute()
    {
        return $this->lastname.' '.$this->firstname.' '.$this->fathername;
    }

    public static function getColumnsName()
    {
        $client = new Client();
        return $client->getConnection()->getSchemaBuilder()->getColumnListing($client->getTable());
    }

    public static function findByPhone($phone_number)
    {
        $result = self::select('clients.*')
            ->join('client_phones', 'client_phones.client_id', '=', 'clients.id')
            ->where('client_phones.phone', $phone_number)
            ->first();

        return $result ?? new Client();
    }

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
        return $this->hasOne(\App\Models\TraficSex::class,'id','trafic_sex_id')->withDefault();
    }

    public function zone()
    {
        return $this->hasOne(\App\Models\TraficZone::class,'id','trafic_zone_id')->withDefault();
    }

    public function passport()
    {
        return $this->hasOne(\App\Models\ClientPassport::class, 'client_id', 'id')->withDefault();
    }

    public function cars()
    {
        return $this->hasMany(\App\Models\ClientCar::class, 'client_id', 'id')->where('actual', 1);
    }

    public function latest_worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class,'client_id', 'id')->orderBy('id','DESC')->withDefault();
    }


}
