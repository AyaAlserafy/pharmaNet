<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoury extends Model
{
    use HasFactory;
    protected $table = 'categouries';
    public $timestamps = false;
    function medicines()
    {
        return $this->hasMany(medicine::class);
    }
    function alternatives()
    {
        return $this->hasMany(Alternative::class);
    }
    
}
