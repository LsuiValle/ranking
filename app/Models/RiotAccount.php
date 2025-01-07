<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiotAccount extends Model
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

