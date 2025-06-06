<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificacionEstatuto extends Model
{
    use HasFactory;

    protected $table = 'modificacion_estatuto';

    protected $fillable = [
        'dato_constitutivo_id',
        'instrumento_notarial_id',
        'descripcion',
    ];

    public function datosConstitutivos()
    {
        return $this->belongsTo(DatosConstitutivo::class, 'dato_constitutivo_id');
    }

    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class);
    }
} 