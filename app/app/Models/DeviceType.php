<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\SortInterface;

class DeviceType extends Model implements SortInterface
{
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = false;
}
