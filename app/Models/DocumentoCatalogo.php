<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentoSolicitante;

class DocumentoCatalogo extends Model
{
    use HasFactory;

    protected $table = 'documentos_catalogo';
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    /**
     * Relación: documentos solicitante que usan este catálogo
     */
    public function documentosSolicitante()
    {
        return $this->hasMany(DocumentoSolicitante::class, 'documento_catalogo_id');
    }
}
