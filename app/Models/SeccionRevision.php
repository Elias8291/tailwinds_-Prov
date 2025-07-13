<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el trámite
     */
    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    /**
     * Relación con la sección del trámite
     */
    public function seccion(): BelongsTo
    {
        return $this->belongsTo(SeccionTramite::class, 'seccion_id');
    }

    /**
     * Relación con el usuario que revisó
     */
    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    /**
     * Scopes
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazado');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Accesor para obtener el estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return match($this->estado) {
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado',
            'pendiente' => 'Pendiente',
            default => 'Pendiente'
        };
    }
} 