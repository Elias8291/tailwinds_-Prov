<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipio';

    protected $fillable = [
        'estado_id',
        'nombre',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function localidades()
    {
        return $this->hasMany(Localidad::class);
    }
} 