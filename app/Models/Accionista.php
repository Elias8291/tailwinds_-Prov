<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;

class Accionista extends Model
{
    use HasFactory;

    protected $table = 'accionistas';
    
    protected $fillable = [
        'solicitante_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'rfc',
        'porcentaje_participacion'
    ];

    protected $casts = [
        'porcentaje_participacion' => 'decimal:2',
    ];

    /**
     * Relación: solicitante del accionista
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }
}
