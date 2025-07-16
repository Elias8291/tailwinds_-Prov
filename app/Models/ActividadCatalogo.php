<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SectorCatalogo;
use App\Models\ActividadSolicitante;
use App\Models\Solicitante;

class ActividadCatalogo extends Model
{
    use HasFactory;

    protected $table = 'actividades_catalogo';
    
    protected $fillable = [
        'sector_id',
        'nombre',
        'descripcion',
        'es_personalizada'
    ];

    protected $casts = [
        'es_personalizada' => 'boolean',
    ];

    /**
     * Relación: sector de la actividad
     */
    public function sector()
    {
        return $this->belongsTo(SectorCatalogo::class, 'sector_id');
    }

    /**
     * Relación: actividades solicitante que usan este catálogo
     */
    public function actividadesSolicitante()
    {
        return $this->hasMany(ActividadSolicitante::class, 'actividad_id');
    }

    /**
     * Relación: solicitantes que tienen esta actividad (many-to-many)
     */
    public function solicitantes()
    {
        return $this->belongsToMany(Solicitante::class, 'actividad_solicitante', 'actividad_id', 'solicitante_id');
    }
}
