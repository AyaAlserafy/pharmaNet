<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class map extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'address',
        'address_latitude',
        'address_longitude',
    ];
    public $timestamps = false;
    function users()
    {
        return $this->belongsTo(users::class, 'user_id', 'id');
    }
    function addresses()
    {
        return $this->belongsTo(addresses::class, 'address_id', 'id');
    }

    protected $casts = [
        'ratings' => 'integer',
    ];
}
