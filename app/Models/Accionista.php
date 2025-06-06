<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accionista extends Model
{
    use HasFactory;

    protected $table = 'accionista';

    protected $fillable = [
        'nombre',
        'tipo_persona',
        'rfc',
        'curp',
    ];

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'accionista_solicitante')
            ->withPivot('porcentaje_participacion')
            ->withTimestamps();
    }
} 