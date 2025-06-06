<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiasInhabiles extends Model
{
    use HasFactory;

    protected $table = 'dias_inhabiles';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];
} 