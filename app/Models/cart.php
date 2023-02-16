<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'pharmacy_id',
        'accsepted',
        'accsepted_driv',
        'prescription',


    ];
    function orders()
    {
        return $this->hasMany(order::class, 'cart_id', 'id');
    }
    public function pharmacy()
    {
        return $this->belongsTo(pharmacy::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function medicines()
    {
        return $this->belongsToMany(medicine::class);
    }
}
