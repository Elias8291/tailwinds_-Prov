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
        'descripcion',
        'tipo_persona',
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

    /**
     * Obtiene los documentos solicitantes asociados.
     */
    public function documentosSolicitante()
    {
        return $this->hasMany(DocumentoSolicitante::class, 'documento_id');
    }
} 