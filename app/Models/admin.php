<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class admin extends Model
{

    use HasFactory;
    function accounts()
    {
        return $this->belongsTo(Account::class);
    }
    public $timestamps = false;
}
