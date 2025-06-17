<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeccionTramite extends Model
{
    use HasFactory;

    protected $table = 'seccion_tramite';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'es_requerido'
    ];

    protected $casts = [
        'es_requerido' => 'boolean',
        'orden' => 'integer'
    ];

    /**
     * Relación muchos-a-muchos con Documento
     */
    public function documentos()
    {
        return $this->belongsToMany(Documento::class, 'documento_seccion', 'seccion_id', 'documento_id')
                    ->withTimestamps();
    }

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'progreso_tramite')
            ->withPivot(['estado', 'observaciones', 'fecha_inicio', 'fecha_completado'])
            ->withTimestamps();
    }

    public function progresoTramites()
    {
        return $this->hasMany(ProgresoTramite::class, 'seccion_id');
    }

    public function revisionesSecciones()
    {
        return $this->hasMany(SeccionRevision::class, 'seccion_id');
    }
} 