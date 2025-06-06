<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoSolicitante extends Model
{
    use HasFactory;

    protected $table = 'documento_solicitante';

    protected $fillable = [
        'tramite_id',
        'documento_id',
        'ruta_archivo',
        'estado',
        'comentario',
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
} 