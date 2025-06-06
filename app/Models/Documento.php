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
        'descripcion',
        'requerido',
        'formato_permitido',
        'tamano_maximo',
    ];

    protected $casts = [
        'requerido' => 'boolean',
        'tamano_maximo' => 'integer',
    ];

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'documento_solicitante')
            ->withPivot(['ruta_archivo', 'estado', 'comentario'])
            ->withTimestamps();
    }
} 