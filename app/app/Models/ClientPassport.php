<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPassport extends Model
{
    use HasFactory;

    protected $dates = [
        'birthday_at',
        'driver_license_issue_at',
        'passport_issue_at',
    ];

    protected $guarded = [];

    public $timestamps = false;

    public static function getColumnsName()
    {
        $clientPassport = new ClientPassport();
        return $clientPassport->getConnection()->getSchemaBuilder()->getColumnListing($clientPassport->getTable());
    }



    public function getBirthdayAttribute()
    {
        if($this->birthday_at)
            return $this->birthday_at->format('d.m.Y');
        return '';
    }
}
