<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;

class RepresentanteLegal extends Model
{
    use HasFactory;

    protected $table = 'representantes_legales';
    
    protected $fillable = [
        'solicitante_id',
        'nombre_completo',
        'rfc',
        'curp',
        'numero_escritura_poder',
        'fecha_escritura_poder',
        'nombre_notario_poder',
        'numero_notario_poder',
        'entidad_federativa_notario_poder',
        'folio_registro_publico_poder',
        'fecha_inscripcion_poder'
    ];

    protected $casts = [
        'fecha_escritura_poder' => 'date',
        'fecha_inscripcion_poder' => 'date',
    ];

    /**
     * Relación: solicitante del representante legal
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }
}
