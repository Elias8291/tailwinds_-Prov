<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactoSolicitante extends Model
{
    use HasFactory;

    protected $table = 'contacto_solicitante';

    protected $fillable = [
        'nombre',
        'puesto',
        'email',
        'telefono',
    ];

    public function detalleTramite()
    {
        return $this->hasOne(DetalleTramite::class, 'contacto_id');
    }
} 