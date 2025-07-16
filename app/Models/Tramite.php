<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;
use App\Models\DocumentoSolicitante;
use App\Models\SeccionTramite;
use App\Models\RevisionSeccion;

class Tramite extends Model
{
    use HasFactory;

    protected $table = 'tramites';
    
    protected $fillable = [
        'solicitante_id', 
        'tipo', 
        'estado', 
        'fecha_inicio', 
        'fecha_finalizacion', 
        'fecha_limite_correcciones'
    ];

    protected $casts = [
        'tipo' => 'string',
        'estado' => 'string',
        'fecha_inicio' => 'date',
        'fecha_finalizacion' => 'date',
        'fecha_limite_correcciones' => 'date',
    ];

    /**
     * Relación: solicitante del trámite
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    /**
     * Relación: documentos del trámite
     */
    public function documentos()
    {
        return $this->hasMany(DocumentoSolicitante::class);
    }

    /**
     * Relación: secciones del trámite
     */
    public function secciones()
    {
        return $this->hasMany(SeccionTramite::class);
    }

    /**
     * Relación: revisiones de secciones del trámite
     */
    public function revisionesSecciones()
    {
        return $this->hasManyThrough(RevisionSeccion::class, SeccionTramite::class);
    }
} 