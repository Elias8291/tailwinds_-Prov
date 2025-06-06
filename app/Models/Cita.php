<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'cita';

    protected $fillable = [
        'solicitante_id',
        'tramite_id',
        'fecha_cita',
        'hora_cita',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'hora_cita' => 'datetime',
    ];

    protected $attributes = [
        'estado' => 'Pendiente',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }
} 