<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class partidas extends Model
{
    use HasFactory;
    protected $table = 'partidas';
    protected $fillable = ['uuid', 'detalle_historial'];   
}
