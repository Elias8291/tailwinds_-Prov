<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosConstitutivo extends Model
{
    use HasFactory;

    protected $table = 'datos_constitutivo';

    protected $fillable = [
        'instrumento_notarial_id',
        'objeto_social',
    ];

    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class);
    }

    public function modificacionesEstatutos()
    {
        return $this->hasMany(ModificacionEstatuto::class, 'dato_constitutivo_id');
    }

    public function detalleTramite()
    {
        return $this->hasOne(DetalleTramite::class, 'dato_constitutivo_id');
    }
} 