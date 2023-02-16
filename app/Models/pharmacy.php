<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pharmacy extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'account_id',
        'active',
        'address',
        'address_latitude',
        'address_longitude'
    ];
    public function midicines()
    {
        return $this->belongsToMany(medicine::class,'id','pharmacy_id','medicine_id');
    }
    function carts()
    {
        return $this->hasMany(cart::class);
    }
    function accounts()
    {
        return $this->belongsTo(Account::class);
    }
    function alternatives()
    {
        return $this->hasMany(Alternative::class);
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public $timestamps = false;
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ratings' => 'integer',
    ];
    
}
