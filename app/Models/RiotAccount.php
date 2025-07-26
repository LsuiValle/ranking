<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RiotAccount extends Authenticatable
{
    use HasFactory;

    // Campos que pueden ser llenados en la base de datos
    protected $fillable = [
        'puuid',
        'game_name',
        'tag_line',
        'division',
        'rango',
        'points',
        'wins',
        'defeat',
        'level',
        'summonerid',
        
    ];
}

