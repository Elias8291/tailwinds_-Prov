<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentoNotarial extends Model
{
    use HasFactory;

    protected $table = 'instrumento_notarial';

    protected $fillable = [
        'numero_escritura',
        'fecha',
        'nombre_notario',
        'numero_notario',
        'estado_id',
        'registro_mercantil',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_registro' => 'date',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function datosConstitutivos()
    {
        return $this->hasOne(DatosConstitutivo::class);
    }

    public function modificacionesEstatutos()
    {
        return $this->hasMany(ModificacionEstatuto::class);
    }

    public function representantesLegales()
    {
        return $this->hasMany(RepresentanteLegal::class);
    }
} 