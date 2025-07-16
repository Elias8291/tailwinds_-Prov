<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActividadCatalogo;

class SectorCatalogo extends Model
{
    use HasFactory;

    protected $table = 'sectores_catalogo';
    
    protected $fillable = [
        'nombre'
    ];

    /**
     * Relación: actividades del sector
     */
    public function actividades()
    {
        return $this->hasMany(ActividadCatalogo::class, 'sector_id');
    }
}
