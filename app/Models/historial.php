<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historial extends Model
{
    use HasFactory;
    protected $table = 'historial';
    protected $fillable = ['id_user', 'id_champion', 'detalle_historial'];    
}