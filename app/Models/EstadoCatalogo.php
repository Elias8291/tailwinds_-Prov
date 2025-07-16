<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CatalogoPais;
use App\Models\MunicipioCatalogo;

class EstadoCatalogo extends Model
{
    use HasFactory;

    protected $table = 'estados_catalogo';
    
    protected $fillable = [
        'pais_id',
        'nombre'
    ];

    /**
     * Relación: país del estado
     */
    public function pais()
    {
        return $this->belongsTo(CatalogoPais::class, 'pais_id');
    }

    /**
     * Relación: municipios del estado
     */
    public function municipios()
    {
        return $this->hasMany(MunicipioCatalogo::class, 'estado_id');
    }
}
