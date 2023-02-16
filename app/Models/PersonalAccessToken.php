<?php

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;

    protected $connection = 'D:\laravel-projects\project22\database\migrations\2019_12_14_000001_create_personal_access_tokens_table.php';
}
