<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'citas';

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 