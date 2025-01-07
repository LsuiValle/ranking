<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class champs extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'title', 'tags'];    
}