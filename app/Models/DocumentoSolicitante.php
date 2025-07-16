<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tramite;
use App\Models\DocumentoCatalogo;
use App\Models\DocumentoVersion;

class DocumentoSolicitante extends Model
{
    use HasFactory;

    protected $table = 'documentos_solicitante';
    
    protected $fillable = [
        'tramite_id',
        'documento_catalogo_id',
        'estado_revision',
        'comentarios_revision_actual',
        'fecha_cotejo_presencial',
        'documento_cotejado'
    ];

    protected $casts = [
        'estado_revision' => 'string',
        'fecha_cotejo_presencial' => 'datetime',
        'documento_cotejado' => 'boolean',
    ];

    /**
     * Relación: trámite del documento
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    /**
     * Relación: catálogo del documento
     */
    public function documentoCatalogo()
    {
        return $this->belongsTo(DocumentoCatalogo::class, 'documento_catalogo_id');
    }

    /**
     * Relación: versiones del documento
     */
    public function versiones()
    {
        return $this->hasMany(DocumentoVersion::class);
    }
}
