<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'title',
        'body',
        'seen',
        'type'
    ];
    public function Account()
    {
        return $this->belongsTo(Account::class);
    }
}
