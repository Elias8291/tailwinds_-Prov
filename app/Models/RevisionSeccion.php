<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SeccionTramite;
use App\Models\User;

class RevisionSeccion extends Model
{
    use HasFactory;

    protected $table = 'revisiones_seccion';
    
    protected $fillable = [
        'seccion_tramite_id',
        'revisor_id',
        'comentarios',
        'estado_revision'
    ];

    protected $casts = [
        'estado_revision' => 'string',
    ];

    /**
     * Relación: sección de trámite revisada
     */
    public function seccionTramite()
    {
        return $this->belongsTo(SeccionTramite::class, 'seccion_tramite_id');
    }

    /**
     * Relación: revisor de la sección
     */
    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }
}
