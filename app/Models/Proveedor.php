<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Solicitante;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    
    protected $fillable = [
        'solicitante_id',
        'numero_proveedor',
        'fecha_aprobacion',
        'fecha_vencimiento',
        'estado'
    ];

    protected $casts = [
        'fecha_aprobacion' => 'date',
        'fecha_vencimiento' => 'date',
        'estado' => 'string',
    ];

    /**
     * Relación: solicitante del proveedor
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }
}
