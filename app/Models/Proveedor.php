<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        try {
            // Verificar si ya existe un proveedor para este solicitante
            $proveedorExistente = self::where('solicitante_id', $tramite->solicitante_id)
                                       ->where('estado', 'Activo')
                                       ->first();
            
            if ($proveedorExistente) {
                Log::warning('Ya existe un proveedor activo para este solicitante:', [
                    'solicitante_id' => $tramite->solicitante_id,
                    'pv_existente' => $proveedorExistente->pv,
                    'tramite_id' => $tramite->id
                ]);
                
                // Actualizar observaciones del proveedor existente
                $proveedorExistente->update([
                    'observaciones' => $proveedorExistente->observaciones . 
                                     ' | Trámite adicional procesado: ' . $tramite->id . ' el ' . now()->format('d/m/Y')
                ]);
                
                return $proveedorExistente;
            }
            
            $nuevoPV = self::generarSiguientePV();
            
            $proveedor = self::create([
                'pv' => $nuevoPV,
                'solicitante_id' => $tramite->solicitante_id,
                'fecha_registro' => now(),
                'fecha_vencimiento' => now()->addYear(), // Válido por 1 año
                'estado' => 'Activo',
                'observaciones' => 'Proveedor creado automáticamente desde trámite ID: ' . $tramite->id . 
                                 ' el ' . now()->format('d/m/Y H:i:s')
            ]);

            // Log del evento
            Log::info('Proveedor creado automáticamente:', [
                'pv' => $proveedor->pv,
                'solicitante_id' => $tramite->solicitante_id,
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'fecha_creacion' => now(),
                'fecha_vencimiento' => $proveedor->fecha_vencimiento
            ]);

            // Crear notificación para el solicitante
            self::crearNotificacionProveedor($proveedor, $tramite);

            return $proveedor;
            
        } catch (\Exception $e) {
            Log::error('Error al crear proveedor desde trámite:', [
                'tramite_id' => $tramite->id,
                'solicitante_id' => $tramite->solicitante_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Error al crear el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Crear notificación para el solicitante sobre su nuevo proveedor
     */
    private static function crearNotificacionProveedor($proveedor, $tramite)
    {
        try {
            // Buscar al usuario asociado al solicitante
            $usuario = User::where('email', $tramite->solicitante->email)->first();
            
            if ($usuario) {
                \App\Models\Notificacion::create([
                    'usuario_id' => $usuario->id,
                    'titulo' => '🎉 ¡Felicidades! Su proveedor ha sido creado',
                    'mensaje' => "Su trámite de {$tramite->tipo_tramite} ha sido aprobado exitosamente. " .
                               "Su código de proveedor es: {$proveedor->pv}. " .
                               "Válido hasta: {$proveedor->fecha_vencimiento->format('d/m/Y')}",
                    'tipo' => 'proveedor_creado',
                    'leida' => false,
                    'datos_adicionales' => json_encode([
                        'pv' => $proveedor->pv,
                        'tramite_id' => $tramite->id,
                        'fecha_vencimiento' => $proveedor->fecha_vencimiento->format('Y-m-d')
                    ])
                ]);
                
                Log::info('Notificación de proveedor creada:', [
                    'usuario_id' => $usuario->id,
                    'pv' => $proveedor->pv
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('No se pudo crear notificación de proveedor:', [
                'pv' => $proveedor->pv,
                'error' => $e->getMessage()
            ]);
        }
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