<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medicine extends Model
{
    use HasFactory;
    protected $table = 'medicines';
    protected $fillable = [
        'arabName',
        'engName',
        'company_id',
        'categoury_id',
        'classifications_id',
        'pharmacological_form',
        'formula',

        'drug_details',
        'purchasing_price',
        'image'
    ];
    function company()
    {
        return $this->belongsTo(company::class,);
    }
    function categoury()
    {
        return $this->belongsTo(categoury::class);
    }

    public function pharmacy()
    {
        return $this->belongsToMany(pharmacy::class, 'id', 'pharmacy_id', 'medicine_id');
    }
    public function cart()
    {
        return $this->belongsToMany(cart::class);
    }
    function alternatives()
    {
        return $this->hasMany(Alternative::class);
    }
}
