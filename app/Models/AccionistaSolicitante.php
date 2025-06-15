<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionistaSolicitante extends Model
{
    use HasFactory;

    protected $table = 'accionista_solicitante';

    protected $fillable = [
        'tramite_id',
        'accionista_id',
        'porcentaje_participacion',
    ];

    protected $casts = [
        'porcentaje_participacion' => 'decimal:2',
    ];

    /**
     * Relación con Tramite
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    /**
     * Relación con Accionista
     */
    public function accionista()
    {
        return $this->belongsTo(Accionista::class, 'accionista_id');
    }
} 