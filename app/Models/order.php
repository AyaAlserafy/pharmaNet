<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'driver_id',
        'cart_id',
        'status',
        
        
    ];
    function driver()
    {
        return $this->belongsTo(driver::class, 'driver_id', 'id');
    }
    function cart()
    {
        return $this->belongsTo(cart::class, 'cart_id', 'id');
    }
}
