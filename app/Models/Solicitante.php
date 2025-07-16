<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tramite;
use App\Models\User;
use App\Models\Domicilio;
use App\Models\DatoConstitutivo;
use App\Models\RepresentanteLegal;
use App\Models\Accionista;
use App\Models\ActividadSolicitante;
use App\Models\ActividadCatalogo;
use App\Models\Proveedor;

class Solicitante extends Model
{
    use HasFactory;

    protected $table = 'solicitantes';
    
    protected $fillable = [
        'user_id', 
        'rfc', 
        'nombre_razon_social', 
        'persona_tipo', 
        'curp', 
        'giro', 
        'pagina_web', 
        'telefono_celular', 
        'telefono_oficina'
    ];

    protected $casts = [
        'persona_tipo' => 'string',
    ];

    /**
     * Relación: usuario del solicitante
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: trámites del solicitante
     */
    public function tramites()
    {
        return $this->hasMany(Tramite::class);
    }

    /**
     * Relación: domicilios del solicitante
     */
    public function domicilios()
    {
        return $this->hasMany(Domicilio::class);
    }

    /**
     * Relación: datos constitutivos del solicitante
     */
    public function datosConstitutivos()
    {
        return $this->hasOne(DatoConstitutivo::class);
    }

    /**
     * Relación: representantes legales del solicitante
     */
    public function representantesLegales()
    {
        return $this->hasMany(RepresentanteLegal::class);
    }

    /**
     * Relación: accionistas del solicitante
     */
    public function accionistas()
    {
        return $this->hasMany(Accionista::class);
    }

    /**
     * Relación: actividades del solicitante (many-to-many)
     */
    public function actividades()
    {
        return $this->belongsToMany(ActividadCatalogo::class, 'actividad_solicitante', 'solicitante_id', 'actividad_id');
    }

    /**
     * Relación: actividad_solicitante pivot
     */
    public function actividadSolicitante()
    {
        return $this->hasMany(ActividadSolicitante::class);
    }

    /**
     * Relación: proveedor del solicitante
     */
    public function proveedor()
    {
        return $this->hasOne(Proveedor::class);
    }
} 