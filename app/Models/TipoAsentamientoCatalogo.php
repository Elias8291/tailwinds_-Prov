<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AsentamientoCatalogo;

class TipoAsentamientoCatalogo extends Model
{
    use HasFactory;

    protected $table = 'tipo_asentamiento_catalogo';
    
    protected $fillable = [
        'nombre'
    ];

    /**
     * Relación: asentamientos de este tipo
     */
    public function asentamientos()
    {
        return $this->hasMany(AsentamientoCatalogo::class, 'tipo_asentamiento_id');
    }
}
