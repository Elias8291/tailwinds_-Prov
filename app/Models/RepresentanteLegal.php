<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentanteLegal extends Model
{
    use HasFactory;

    protected $table = 'representante_legal';

    protected $fillable = [
        'instrumento_notarial_id',
        'nombre',
        'curp',
        'rfc',
        'email',
        'telefono',
    ];

    public function instrumentoNotarial()
    {
        return $this->belongsTo(InstrumentoNotarial::class);
    }

    public function detalleTramite()
    {
        return $this->hasOne(DetalleTramite::class);
    }
} 