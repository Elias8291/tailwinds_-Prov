<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documento';

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'fecha_expiracion',
        'es_visible',
        'tipo_persona'
    ];

    protected $casts = [
        'fecha_expiracion' => 'date',
        'es_visible' => 'boolean'
    ];

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'documento_solicitante')
            ->withPivot(['ruta_archivo', 'estado', 'comentario'])
            ->withTimestamps();
    }
} 