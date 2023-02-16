<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company extends Model
{
    use HasFactory;
    protected $table='companies';
    protected $fillable = [
        'name',
        
    ];
    public $timestamps = false;
    function medicines()
    {
        return $this->hasMany(medicine::class, 'company_id', 'id');
    }
    function alternatives()
    {
        return $this->hasMany(Alternative::class);
    }
}
