<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class advertisement extends Model
{
    use HasFactory;

    function medicine()
    {
        return $this->belongsTo(medicine::class);
    }
    function pharmacy()
    {
        return $this->belongsTo(pharmacy::class);
    }
}
