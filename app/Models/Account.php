<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Events\Registered;

class Account extends Authenticatable implements MustVerifyEmail
{

    use HasApiTokens, HasFactory, Notifiable;
    function users()
    {
        return $this->hasMany(User::class);
    }
    function drivers()
    {
        return $this->hasMany(driver::class);
    }
    function pharmacies()
    {
        return $this->hasMany(pharmacy::class);
    }
    function admins()
    {
        return $this->hasMany(admin::class);
    }
    function notifications()
    {
        return $this->hasMany(notification::class);
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'type'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
