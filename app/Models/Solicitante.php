<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    use HasFactory;

    protected $table = 'solicitante';

    protected $fillable = [
        'usuario_id',
        'tipo_persona',
        'curp',
        'rfc',
        'objeto_social',
        'nombre_completo',
        'razon_social',
    ];

    protected $casts = [
        'tipo_persona' => 'string',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class);
    }

    public function documentosSolicitante()
    {
        return $this->hasManyThrough(
            DocumentoSolicitante::class,
            Tramite::class,
            'solicitante_id', // Foreign key on tramites table
            'tramite_id', // Foreign key on documento_solicitante table
            'id', // Local key on solicitantes table
            'id' // Local key on tramites table
        );
    }



    // Accesor para obtener el nombre o razón social según el tipo de persona
    public function getNombreCompletoAttribute($value)
    {
        if ($this->tipo_persona === 'Moral') {
            return $this->razon_social;
        }
        return $value;
    }
} 