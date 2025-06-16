<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedor';
    protected $primaryKey = 'pv';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pv',
        'solicitante_id',
        'fecha_registro',
        'fecha_vencimiento',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    /**
     * Relación con el solicitante
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class, 'solicitante_id');
    }

    /**
     * Generar el siguiente número PV disponible
     */
    public static function generarSiguientePV()
    {
        // Obtener el último PV registrado
        $ultimoPV = self::orderByRaw('CAST(SUBSTRING(pv, 3) AS UNSIGNED) DESC')->first();
        
        if (!$ultimoPV) {
            // Si no hay registros, empezar con PV0001
            return 'PV0001';
        }

        // Extraer el número del último PV (quitar "PV" del inicio)
        $ultimoNumero = (int) substr($ultimoPV->pv, 2);
        
        // Incrementar en 1
        $nuevoNumero = $ultimoNumero + 1;
        
        // Formatear con ceros a la izquierda (4 dígitos)
        return 'PV' . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crear un nuevo proveedor desde un trámite aprobado
     */
    public static function crearDesdeTramiite($tramite)
    {
        $nuevoPV = self::generarSiguientePV();
        
        return self::create([
            'pv' => $nuevoPV,
            'solicitante_id' => $tramite->solicitante_id,
            'fecha_registro' => now(),
            'fecha_vencimiento' => now()->addYear(), // Válido por 1 año
            'estado' => 'Activo',
            'observaciones' => 'Proveedor creado automáticamente desde trámite ID: ' . $tramite->id
        ]);
    }

    /**
     * Verificar si el proveedor está activo
     */
    public function estaActivo()
    {
        return $this->estado === 'Activo' && $this->fecha_vencimiento >= now();
    }

    /**
     * Verificar si el proveedor está próximo a vencer (30 días)
     */
    public function proximoAVencer()
    {
        return $this->fecha_vencimiento <= now()->addDays(30);
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'solicitante_id', 'solicitante_id');
    }

    public function detallesTramite()
    {
        return $this->hasManyThrough(
            DetalleTramite::class,
            Tramite::class,
            'solicitante_id', // Foreign key on tramite table
            'tramite_id', // Foreign key on detalle_tramite table
            'solicitante_id', // Local key on proveedor table
            'id' // Local key on tramite table
        );
    }

    public function getDiasParaVencerAttribute()
    {
        if ($this->fecha_vencimiento) {
            return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
        }
        return null;
    }

    public function getEstaProximoAVencerAttribute()
    {
        return $this->dias_para_vencer <= 7 && $this->dias_para_vencer >= 0;
    }

    /**
     * Verificar si el proveedor está vencido
     */
    public function estaVencido()
    {
        return $this->fecha_vencimiento < now()->startOfDay();
    }

    /**
     * Actualizar estado automáticamente basado en fecha de vencimiento
     */
    public function actualizarEstadoAutomatico()
    {
        $estadoAnterior = $this->estado;
        
        if ($this->estaVencido() && $this->estado === 'Activo') {
            $this->estado = 'Inactivo';
            $this->observaciones = ($this->observaciones ? $this->observaciones . ' | ' : '') . 
                                  'Estado cambiado automáticamente a Inactivo por vencimiento el ' . now()->format('d/m/Y H:i');
            $this->save();
            
            return [
                'cambio' => true,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'Inactivo',
                'pv' => $this->pv
            ];
        }
        
        return ['cambio' => false, 'pv' => $this->pv];
    }

    /**
     * Actualizar estados de todos los proveedores vencidos
     */
    public static function actualizarEstadosVencidos()
    {
        $proveedoresVencidos = self::where('estado', 'Activo')
                                  ->where('fecha_vencimiento', '<', now()->startOfDay())
                                  ->get();
        
        $actualizados = [];
        
        foreach ($proveedoresVencidos as $proveedor) {
            $resultado = $proveedor->actualizarEstadoAutomatico();
            if ($resultado['cambio']) {
                $actualizados[] = $resultado;
            }
        }
        
        return $actualizados;
    }
} 