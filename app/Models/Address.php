<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    function maps()
    {
        return $this->hasMany(map::class, 'map_id', 'id');
    }
    public $timestamps = false;

}
