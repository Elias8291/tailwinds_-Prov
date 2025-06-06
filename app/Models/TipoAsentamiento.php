<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAsentamiento extends Model
{
    use HasFactory;

    protected $table = 'tipo_asentamiento';

    protected $fillable = [
        'nombre',
    ];

    public function asentamientos()
    {
        return $this->hasMany(Asentamiento::class);
    }
} 