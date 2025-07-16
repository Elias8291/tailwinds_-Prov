<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;
use App\Models\ActividadCatalogo;

class ActividadSolicitante extends Model
{
    use HasFactory;

    protected $table = 'actividad_solicitante';
    
    protected $fillable = [
        'solicitante_id',
        'actividad_id'
    ];

    /**
     * Relación: solicitante de la actividad
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    /**
     * Relación: catálogo de la actividad
     */
    public function actividadCatalogo()
    {
        return $this->belongsTo(ActividadCatalogo::class, 'actividad_id');
    }
}
