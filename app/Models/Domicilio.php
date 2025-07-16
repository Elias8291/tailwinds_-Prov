<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;

class Domicilio extends Model
{
    use HasFactory;

    protected $table = 'domicilios';
    
    protected $fillable = [
        'solicitante_id',
        'tipo',
        'calle',
        'numero_exterior',
        'numero_interior',
        'colonia',
        'localidad',
        'municipio',
        'estado',
        'pais',
        'codigo_postal',
        'entre_calle_1',
        'entre_calle_2',
        'is_principal'
    ];

    protected $casts = [
        'tipo' => 'string',
        'is_principal' => 'boolean',
    ];

    /**
     * Relación: solicitante del domicilio
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }
}
