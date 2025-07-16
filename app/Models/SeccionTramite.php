<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tramite;
use App\Models\SeccionCatalogo;
use App\Models\RevisionSeccion;

class SeccionTramite extends Model
{
    use HasFactory;

    protected $table = 'secciones_tramite';
    
    protected $fillable = [
        'tramite_id',
        'seccion_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /**
     * Relación: trámite de la sección
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    /**
     * Relación: catálogo de la sección
     */
    public function seccionCatalogo()
    {
        return $this->belongsTo(SeccionCatalogo::class, 'seccion_id');
    }

    /**
     * Relación: revisiones de la sección
     */
    public function revisiones()
    {
        return $this->hasMany(RevisionSeccion::class);
    }
}
