<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividad';

    protected $fillable = [
        'sector_id',
        'nombre',
        'descripcion',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'actividad_solicitante')
            ->withTimestamps();
    }
} 