<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;
    protected $fillable = [
        'medicine_id',
        'pharamcy_id',
        'arabName',
        'engName',
        
        
        
    ];
    public $timestamps = false;

    
    function medicine()
    {
        return $this->belongsTo(medicine::class);
    }
    function pharmacy()
    {
        return $this->belongsTo(pharmacy::class);
    }
}
