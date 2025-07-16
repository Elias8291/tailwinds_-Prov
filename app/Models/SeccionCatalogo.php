<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SeccionTramite;

class SeccionCatalogo extends Model
{
    use HasFactory;

    protected $table = 'secciones_catalogo';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'orden'
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    /**
     * Relación: secciones de trámite que usan este catálogo
     */
    public function seccionesTramite()
    {
        return $this->hasMany(SeccionTramite::class, 'seccion_id');
    }
}
