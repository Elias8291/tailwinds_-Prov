<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sector';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }
} 