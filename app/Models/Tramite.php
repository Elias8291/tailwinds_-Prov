<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;

    protected $table = 'tramite';

    protected $fillable = [
        'solicitante_id',
        'tipo_tramite',
        'estado',
        'progreso_tramite',
        'revisado_por',
        'fecha_revision',
        'fecha_inicio',
        'fecha_finalizacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_revision' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
        'progreso_tramite' => 'integer',
    ];

    protected $attributes = [
        'estado' => 'Pendiente',
        'progreso_tramite' => 0,
    ];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function detalle()
    {
        return $this->hasOne(DetalleTramite::class);
    }

    /** Get detalle tramite relation */
    public function detalleTramite()
    {
        return $this->hasOne(DetalleTramite::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoSolicitante::class);
    }

    /** Get documentos solicitante relation */
    public function documentosSolicitante()
    {
        return $this->hasMany(DocumentoSolicitante::class);
    }

    public function actividades()
    {
        return $this->belongsToMany(Actividad::class, 'actividad_solicitante')
            ->withTimestamps();
    }

    public function accionistas()
    {
        return $this->belongsToMany(Accionista::class, 'accionista_solicitante')
            ->withPivot('porcentaje_participacion')
            ->withTimestamps();
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function seccionesRevision()
    {
        return $this->belongsToMany(Seccion::class, 'seccion_revision')
            ->withPivot(['estado', 'comentario', 'revisado_por'])
            ->withTimestamps();
    }
} 