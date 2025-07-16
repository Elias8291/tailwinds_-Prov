<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MunicipioCatalogo;
use App\Models\AsentamientoCatalogo;

class LocalidadCatalogo extends Model
{
    use HasFactory;

    protected $table = 'localidades_catalogo';
    
    protected $fillable = [
        'municipio_id',
        'nombre'
    ];

    /**
     * Relación: municipio de la localidad
     */
    public function municipio()
    {
        return $this->belongsTo(MunicipioCatalogo::class, 'municipio_id');
    }

    /**
     * Relación: asentamientos de la localidad
     */
    public function asentamientos()
    {
        return $this->hasMany(AsentamientoCatalogo::class, 'localidad_id');
    }
}
