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
        'apellido_paterno',
        'apellido_materno',
    ];

    /**
     * Relación con AccionistaSolicitante
     */
    public function accionistaSolicitantes()
    {
        return $this->hasMany(AccionistaSolicitante::class, 'accionista_id');
    }

    /**
     * Relación con Tramites a través de AccionistaSolicitante
     */
    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'accionista_solicitante', 'accionista_id', 'tramite_id')
            ->withPivot('porcentaje_participacion')
            ->withTimestamps();
    }

    /**
     * Obtener el nombre completo del accionista
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno);
    }
} 