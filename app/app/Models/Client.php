<?php

namespace App\Models;

use App\Models\Interfaces\PersonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Createable;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model implements PersonInterface
{
    use HasFactory, Createable, Filterable, SoftDeletes;

    protected $guarded = [];

    public function loyalty()
    {
        return 'Неизвестно';
    }

    public function abbreviated_name()
    {
        return $this->full_name;
    }

    public function getFullNameAttribute()
    {
        $mas[] = $this->lastname;
        $mas[] = $this->firstname;
        $mas[] = $this->fathername;
        $mas[] = $this->company_name;
        $result = trim(implode(' ',$mas));
        return $result;
    }

    public static function getColumnsName()
    {
        $client = new Client();
        return $client->getConnection()->getSchemaBuilder()->getColumnListing($client->getTable());
    }

    public function critical()
    {
        return '';
    }

    public function initials()
    {
        if(isset($this->company_name) && !empty($this->company_name))
            return  mb_substr($this->company_name,0,1);

        return mb_substr($this->lastname,0,1).mb_substr($this->firstname,0,1);
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

    public function worksheets()
    {
        return $this->hasMany(\App\Models\Worksheet::class, 'client_id', 'id');
    }

    public function inn()
    {
        return $this->hasOne(\App\Models\ClientInn::class, 'client_id', 'id')->withDefault();
    }

    public function events()
    {
        return $this->hasMany(\App\Models\ClientEvent::class, 'client_id', 'id');
    }

    public function unionsChildren()
    {
        return $this->belongsToMany(Client::class, 'client_unions', 'parent', 'client_id')->with(['phones','inn']);
    }

    public function unionsParent()
    {
        return $this->belongsToMany(Client::class, 'client_unions', 'client_id', 'parent')->with(['phones','inn']);
    }

    public function isCompany()
    {
        if($this->client_type_id == 2)
            return true;
        return false;
    }
}
