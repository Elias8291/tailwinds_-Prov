<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cita';

    protected $fillable = [
        'solicitante_id',
        'tramite_id',
        'fecha_hora',
        'motivo',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 