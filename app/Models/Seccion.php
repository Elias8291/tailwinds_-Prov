<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'seccion_revision')
            ->withPivot(['estado', 'comentario', 'revisado_por'])
            ->withTimestamps();
    }

    public function revisores()
    {
        return $this->belongsToMany(User::class, 'seccion_revision', 'seccion_id', 'revisado_por')
            ->withPivot(['estado', 'comentario'])
            ->withTimestamps();
    }

    /**
     * Relación con las revisiones de sección
     */
    public function revisiones()
    {
        return $this->hasMany(SeccionRevision::class, 'seccion_id');
    }
} 