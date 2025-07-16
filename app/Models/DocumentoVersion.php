<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentoSolicitante;

class DocumentoVersion extends Model
{
    use HasFactory;

    protected $table = 'documento_versiones';
    
    protected $fillable = [
        'documento_solicitante_id',
        'path_archivo',
        'hash_archivo',
        'fecha_subida',
        'comentarios_subida'
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
    ];

    /**
     * Relación: documento solicitante de la versión
     */
    public function documentoSolicitante()
    {
        return $this->belongsTo(DocumentoSolicitante::class, 'documento_solicitante_id');
    }
}
