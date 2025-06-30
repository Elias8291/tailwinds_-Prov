<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Documento;
use App\Models\Tramite;
use App\Models\DocumentoVersion;

class DocumentoSolicitante extends Model
{
    use HasFactory;

    protected $table = 'documento_solicitante';

    protected $fillable = [
        'tramite_id',
        'documento_id',
        'archivo',
        'estado',
        'comentarios'
    ];

    protected $attributes = [
        'estado' => 'pendiente',
    ];

    /**
     * Obtiene el documento base asociado.
     */
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    /**
     * Obtiene el trámite asociado.
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    /**
     * Obtiene las versiones anteriores del documento.
     */
    public function versiones()
    {
        return $this->hasMany(DocumentoVersion::class, 'documento_solicitante_id');
    }
} 