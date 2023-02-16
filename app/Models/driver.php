<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class driver extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'age',
        'active',
    ];
    function orders()
    {
        return $this->hasMany(order::class, 'driver_id', 'id');
    }
    function accounts()
    {
        return $this->belongsTo(Account::class);
    }
    public $timestamps = false;
}
