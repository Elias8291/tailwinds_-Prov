<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EstadoCatalogo;
use App\Models\LocalidadCatalogo;

class MunicipioCatalogo extends Model
{
    use HasFactory;

    protected $table = 'municipios_catalogo';
    
    protected $fillable = [
        'estado_id',
        'nombre'
    ];

    /**
     * Relación: estado del municipio
     */
    public function estado()
    {
        return $this->belongsTo(EstadoCatalogo::class, 'estado_id');
    }

    /**
     * Relación: localidades del municipio
     */
    public function localidades()
    {
        return $this->hasMany(LocalidadCatalogo::class, 'municipio_id');
    }
}
