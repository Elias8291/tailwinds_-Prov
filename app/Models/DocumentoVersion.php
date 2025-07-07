<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentoVersion extends Model
{
    use HasFactory;

    protected $table = 'documento_versiones';

    protected $fillable = [
        'documento_solicitante_id',
        'version',
        'ruta_archivo',
        'fecha_subida',
        'observaciones'
    ];

    protected $casts = [
        'fecha_subida' => 'date'
    ];

    /**
     * Obtiene el documento solicitante asociado.
     */
    public function documentoSolicitante()
    {
        return $this->belongsTo(DocumentoSolicitante::class, 'documento_solicitante_id');
    }
} 