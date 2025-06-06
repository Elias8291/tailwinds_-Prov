<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'seccion';

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
} 