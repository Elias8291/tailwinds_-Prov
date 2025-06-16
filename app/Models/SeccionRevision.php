<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeccionRevision extends Model
{
    use HasFactory;

    protected $table = 'seccion_revision';

    protected $fillable = [
        'tramite_id',
        'seccion_id',
        'estado',
        'comentario',
        'revisado_por'
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function seccion()
    {
        return $this->belongsTo(SeccionTramite::class, 'seccion_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    /**
     * Verifica si la sección está aprobada
     */
    public function estaAprobada()
    {
        return $this->estado === 'aprobado';
    }

    /**
     * Verifica si la sección está rechazada
     */
    public function estaRechazada()
    {
        return $this->estado === 'rechazado';
    }

    /**
     * Obtiene el color para mostrar en la UI según el estado
     */
    public function getColorEstado()
    {
        return match($this->estado) {
            'aprobado' => 'green',
            'rechazado' => 'red',
            'pendiente' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Obtiene el icono para mostrar en la UI según el estado
     */
    public function getIconoEstado()
    {
        return match($this->estado) {
            'aprobado' => 'fas fa-check-circle',
            'rechazado' => 'fas fa-times-circle',
            'pendiente' => 'fas fa-clock',
            default => 'fas fa-question-circle'
        };
    }
} 