<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    /**
     * Actualiza el progreso del trámite a una sección específica
     *
     * @param int $seccion Número de sección completada (1-6)
     * @return void
     */
    public function actualizarProgresoSeccion($seccion)
    {
        Log::info('🔄 Intentando actualizar progreso:', [
            'tramite_id' => $this->id,
            'progreso_actual' => $this->progreso_tramite,
            'nueva_seccion' => $seccion
        ]);

        // Solo actualizar si la nueva sección es mayor al progreso actual
        if ($this->progreso_tramite < $seccion) {
            // Usar un enfoque más directo para asegurar que se guarde
            $this->progreso_tramite = $seccion;
            $resultado = $this->save();
            
            Log::info('✅ Progreso actualizado:', [
                'tramite_id' => $this->id,
                'nueva_seccion' => $seccion,
                'resultado_save' => $resultado,
                'progreso_final' => $this->progreso_tramite
            ]);
        } else {
            Log::info('⚠️ No se actualizó progreso (sección no mayor):', [
                'tramite_id' => $this->id,
                'progreso_actual' => $this->progreso_tramite,
                'seccion_solicitada' => $seccion
            ]);
        }
    }

    /**
     * Obtiene el nombre de la sección actual basado en el progreso
     *
     * @return string
     */
    public function getNombreSeccionActual()
    {
        $secciones = [
            0 => 'Sin iniciar',
            1 => 'Datos Generales',
            2 => 'Domicilio', 
            3 => 'Constitución',
            4 => 'Accionistas',
            5 => 'Apoderado Legal',
            6 => 'Documentos'
        ];

        return $secciones[$this->progreso_tramite] ?? 'Desconocido';
    }

    /**
     * Obtiene el porcentaje de progreso basado en el número de sección
     *
     * @return float
     */
    public function getPorcentajeProgreso()
    {
        $totalSecciones = 6; // Total de secciones del formulario
        return ($this->progreso_tramite / $totalSecciones) * 100;
    }

    /**
     * Verifica si el trámite puede ser editado
     *
     * @return bool
     */
    public function puedeSerEditado()
    {
        return in_array($this->estado, ['Pendiente', 'Rechazado']);
    }

    /**
     * Verifica si el trámite está en revisión
     *
     * @return bool
     */
    public function estaEnRevision()
    {
        return $this->estado === 'En Revision';
    }

    /**
     * Verifica si el trámite está completado
     *
     * @return bool
     */
    public function estaCompletado()
    {
        return $this->progreso_tramite >= 6;
    }

    /**
     * Obtiene el color del estado para la UI
     *
     * @return string
     */
    public function getColorEstado()
    {
        return match($this->estado) {
            'Pendiente' => 'yellow',
            'En Revision' => 'blue',
            'Aprobado' => 'green',
            'Rechazado' => 'red',
            'Por Cotejar' => 'purple',
            default => 'gray'
        };
    }

    /**
     * Permite la edición del trámite después de ser rechazado
     *
     * @return void
     */
    public function habilitarEdicion()
    {
        if ($this->estado === 'Rechazado') {
            $this->update([
                'estado' => 'Pendiente',
                'fecha_revision' => null,
                'revisado_por' => null,
                'observaciones' => null
            ]);
        }
    }
} 