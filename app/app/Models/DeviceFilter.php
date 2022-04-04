<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\SortInterface;

class DeviceFilter extends Model implements SortInterface
{
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = false;
}
