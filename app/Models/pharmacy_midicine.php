<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pharmacy_midicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'caliber',
        'quantity',
        'selling_price',
        'production_date',
        'expiration_date',
        'prescription',
        'discount',
        

    ];
    public $timestamps = false;
}
