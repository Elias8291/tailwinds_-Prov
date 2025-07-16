<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;

class DatoConstitutivo extends Model
{
    use HasFactory;

    protected $table = 'datos_constitutivos';
    
    protected $fillable = [
        'solicitante_id',
        'numero_acta_constitutiva',
        'fecha_constitucion',
        'nombre_notario',
        'numero_notaria',
        'entidad_federativa_notaria',
        'numero_registro_publico',
        'fecha_inscripcion_registro'
    ];

    protected $casts = [
        'fecha_constitucion' => 'date',
        'fecha_inscripcion_registro' => 'date',
    ];

    /**
     * Relación: solicitante de los datos constitutivos
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }
}
