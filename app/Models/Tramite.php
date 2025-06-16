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
        return $this->hasMany(SeccionRevision::class);
    }

    public function progresoSecciones()
    {
        return $this->hasMany(ProgresoTramite::class);
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
     * Obtiene el porcentaje de progreso basado en el tipo de persona
     *
     * @return float
     */
    public function getPorcentajeProgreso()
    {
        $tipoPersona = $this->solicitante->tipo_persona ?? 'Física';
        $totalSecciones = $tipoPersona === 'Moral' ? 6 : 3; // 6 para moral, 3 para física
        $progresoMaximo = $tipoPersona === 'Moral' ? 6 : 3; // Máximo progreso permitido
        
        // Limitar el progreso mostrado al máximo permitido
        $progresoMostrado = min($this->progreso_tramite, $progresoMaximo);
        
        return ($progresoMostrado / $totalSecciones) * 100;
    }

    /**
     * Verifica si el trámite está completado según el tipo de persona
     *
     * @return bool
     */
    public function estaCompletadoSegunTipo()
    {
        $tipoPersona = $this->solicitante->tipo_persona ?? 'Física';
        $progresoRequerido = $tipoPersona === 'Moral' ? 6 : 3;
        
        return $this->progreso_tramite >= $progresoRequerido;
    }

    /**
     * Verifica si debe mostrar la vista de estado (enviado para revisión)
     *
     * @return bool
     */
    public function debesMostrarEstado()
    {
        $tipoPersona = $this->solicitante->tipo_persona ?? 'Física';
        return ($tipoPersona === 'Moral' && $this->progreso_tramite >= 7) || 
               ($tipoPersona === 'Física' && $this->progreso_tramite >= 4);
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

    /**
     * Obtiene el estado de revisión de una sección específica
     *
     * @param int $seccionId
     * @return SeccionRevision|null
     */
    public function getEstadoSeccion($seccionId)
    {
        return $this->seccionesRevision()->where('seccion_id', $seccionId)->first();
    }

    /**
     * Verifica si una sección específica está rechazada
     *
     * @param int $seccionId
     * @return bool
     */
    public function seccionEstaRechazada($seccionId)
    {
        $revision = $this->getEstadoSeccion($seccionId);
        return $revision && $revision->estaRechazada();
    }

    /**
     * Verifica si una sección específica está aprobada
     *
     * @param int $seccionId
     * @return bool
     */
    public function seccionEstaAprobada($seccionId)
    {
        $revision = $this->getEstadoSeccion($seccionId);
        return $revision && $revision->estaAprobada();
    }

    /**
     * Obtiene las secciones que necesitan corrección
     *
     * @return array
     */
    public function getSeccionesParaCorregir()
    {
        return $this->seccionesRevision()
            ->where('estado', 'rechazado')
            ->with('seccion')
            ->get()
            ->map(function($revision) {
                return [
                    'seccion_id' => $revision->seccion_id,
                    'nombre' => $revision->seccion->nombre ?? 'Sección ' . $revision->seccion_id,
                    'comentario' => $revision->comentario,
                    'orden' => $revision->seccion->orden ?? $revision->seccion_id
                ];
            })
            ->toArray();
    }

    /**
     * Retrocede el progreso a la primera sección rechazada
     */
    public function retrocederASeccionRechazada()
    {
        $primeraSeccionRechazada = $this->seccionesRevision()
            ->where('estado', 'rechazado')
            ->join('seccion_tramite', 'seccion_revision.seccion_id', '=', 'seccion_tramite.id')
            ->orderBy('seccion_tramite.orden')
            ->select('seccion_tramite.orden')
            ->first();

        if ($primeraSeccionRechazada) {
            $this->update([
                'progreso_tramite' => $primeraSeccionRechazada->orden,
                'estado' => 'Pendiente'
            ]);
        }
    }
} 