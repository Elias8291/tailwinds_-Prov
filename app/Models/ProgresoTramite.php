<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoTramite extends Model
{
    use HasFactory;

    protected $table = 'progreso_tramite';

    protected $fillable = [
        'tramite_id',
        'seccion_id',
        'estado',
        'observaciones',
        'fecha_inicio',
        'fecha_completado'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_completado' => 'datetime'
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function seccion()
    {
        return $this->belongsTo(SeccionTramite::class, 'seccion_id');
    }

    public function calcularPorcentaje()
    {
        $totalSecciones = $this->tramite->secciones()->where('es_requerido', true)->count();
        $seccionesCompletadas = $this->tramite->secciones()
            ->where('es_requerido', true)
            ->wherePivot('estado', 'completado')
            ->count();

        return $totalSecciones > 0 ? ($seccionesCompletadas / $totalSecciones) * 100 : 0;
    }
} 