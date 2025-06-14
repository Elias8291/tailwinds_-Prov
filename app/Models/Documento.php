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
        'tipo_persona',
        'seccion',
        'descripcion',
        'es_visible'
    ];

    protected $casts = [
        'es_visible' => 'boolean'
    ];

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'documento_solicitante')
            ->withPivot(['ruta_archivo', 'estado', 'comentario'])
            ->withTimestamps();
    }
} 