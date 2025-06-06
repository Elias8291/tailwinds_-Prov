<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTramite extends Model
{
    use HasFactory;

    protected $table = 'detalle_tramite';

    protected $fillable = [
        'tramite_id',
        'razon_social',
        'email',
        'telefono',
        'direccion_id',
        'contacto_id',
        'representante_legal_id',
        'dato_constitutivo_id',
        'sitio_web',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    public function contacto()
    {
        return $this->belongsTo(ContactoSolicitante::class, 'contacto_id');
    }

    public function representanteLegal()
    {
        return $this->belongsTo(RepresentanteLegal::class, 'representante_legal_id');
    }

    public function datosConstitutivos()
    {
        return $this->belongsTo(DatosConstitutivo::class, 'dato_constitutivo_id');
    }
} 