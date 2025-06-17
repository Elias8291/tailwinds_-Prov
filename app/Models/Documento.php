<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documento';

    protected $fillable = [
        'nombre',
        'tipo_persona',
        'descripcion',
        'es_visible'
    ];

    protected $casts = [
        'es_visible' => 'boolean'
    ];

    /**
     * Relación muchos-a-muchos con SeccionTramite
     */
    public function secciones()
    {
        return $this->belongsToMany(SeccionTramite::class, 'documento_seccion', 'documento_id', 'seccion_id')
                    ->withTimestamps();
    }

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'documento_solicitante')
            ->withPivot(['ruta_archivo', 'estado', 'comentario'])
            ->withTimestamps();
    }
} 