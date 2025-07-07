<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\Documento;
use App\Models\Proveedor;
use App\Models\DocumentoSolicitante;
use App\Models\DetalleTramite;

use Carbon\Carbon;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\DetalleTramiteController;
use App\Services\SystemLogService;


class TramiteSolicitanteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verificar automáticamente el estado de proveedores vencidos antes de determinar tipo de trámite
        $this->verificarEstadoProveedorAutomatico($user);
        
        $tipoTramite = $this->determinarTipoTramite($user);
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        // Obtener datos de domicilio si hay un trámite en progreso
        $datosDomicilio = [];
        if ($tramiteEnProgreso) {
            Log::info('Trámite en progreso encontrado:', [
                'tramite_id' => $tramiteEnProgreso->id,
                'tipo_tramite' => $tramiteEnProgreso->tipo_tramite,
                'progreso' => $tramiteEnProgreso->progreso_tramite
            ]);
            
            $datosDomicilio = $this->obtenerDatosDomicilio($tramiteEnProgreso);
            

        }
        
        // Obtener código postal específicamente usando DetalleTramiteController si hay trámite en progreso
        $codigoPostalDomicilio = null;
        if ($tramiteEnProgreso) {
            $detalleTramiteController = new \App\Http\Controllers\DetalleTramiteController();
            $codigoPostalDomicilio = $detalleTramiteController->getCodigoPostalByTramiteId($tramiteEnProgreso->id);
            Log::info('📮 Código postal obtenido para vista index:', [
                'tramite_id' => $tramiteEnProgreso->id,
                'codigo_postal' => $codigoPostalDomicilio
            ]);
        }
        
        // Obtener datos del apoderado legal si hay trámite en progreso y es persona moral
        $datosApoderado = [];
        if ($tramiteEnProgreso && $tramiteEnProgreso->solicitante->tipo_persona === 'Moral') {
            $apoderadoController = new \App\Http\Controllers\Formularios\ApoderadoLegalController();
            $datosApoderado = $apoderadoController->getDatosApoderadoLegal($tramiteEnProgreso);
            $datosApoderado['tramite_id'] = $tramiteEnProgreso->id;
        }
        
        // Obtener información del proveedor para mostrar en la vista
        $infoProveedor = $this->obtenerInfoProveedor($user);
        
        return view('tramites.solicitante.index', compact('tipoTramite', 'user', 'tramiteEnProgreso', 'datosDomicilio', 'codigoPostalDomicilio', 'datosApoderado', 'infoProveedor'));
    }

    private function determinarTipoTramite($user)
    {
        // Obtener el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            // Si no hay solicitante, solo puede hacer inscripción
            return [
                'inscripcion' => true,
                'renovacion' => false,
                'actualizacion' => false
            ];
        }
        
        // Buscar si ya tiene un proveedor activo
        $proveedor = Proveedor::where('solicitante_id', $solicitante->id)
                             ->where('estado', 'Activo')
                             ->first();
        
        Log::info('🔍 Determinando tipo de trámite:', [
            'user_id' => $user->id,
            'solicitante_id' => $solicitante->id,
            'tiene_proveedor' => $proveedor ? 'SI' : 'NO',
            'proveedor_pv' => $proveedor ? $proveedor->pv : null,
            'fecha_vencimiento' => $proveedor ? $proveedor->fecha_vencimiento : null
        ]);
        
        // Si no tiene proveedor activo: SOLO INSCRIPCIÓN
        if (!$proveedor) {
            Log::info('✅ Resultado: SOLO INSCRIPCIÓN (sin proveedor)');
            return [
                'inscripcion' => true,
                'renovacion' => false,
                'actualizacion' => false
            ];
        }
        
        // Si tiene proveedor, verificar si está próximo a vencer (7 días)
        $proximoAVencer = $proveedor->fecha_vencimiento <= Carbon::now()->addDays(7);
        $yaVencido = $proveedor->fecha_vencimiento < Carbon::now();
        
        // ✅ NUEVA VALIDACIÓN: Verificar que tenga al menos 7 meses activo para renovación
        $fechaRegistro = Carbon::parse($proveedor->fecha_registro);
        $mesesActivo = $fechaRegistro->diffInMonths(Carbon::now());
        $tieneSeisOMasMeses = $mesesActivo >= 7;
        
        Log::info('📅 Análisis de fechas:', [
            'fecha_actual' => Carbon::now()->format('Y-m-d'),
            'fecha_registro' => $fechaRegistro->format('Y-m-d'),
            'fecha_vencimiento' => $proveedor->fecha_vencimiento->format('Y-m-d'),
            'fecha_limite_renovacion' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'meses_activo' => $mesesActivo,
            'tiene_7_meses_activo' => $tieneSeisOMasMeses ? 'SI' : 'NO',
            'ya_vencido' => $yaVencido ? 'SI' : 'NO',
            'proximo_a_vencer' => $proximoAVencer ? 'SI' : 'NO'
        ]);
        
        // Si ya venció: SOLO INSCRIPCIÓN (nuevo registro)
        if ($yaVencido) {
            Log::info('✅ Resultado: SOLO INSCRIPCIÓN (proveedor vencido)');
            return [
                'inscripcion' => true,
                'renovacion' => false,
                'actualizacion' => false
            ];
        }
        
        // Si está próximo a vencer (7 días): Verificar tiempo mínimo activo
        if ($proximoAVencer) {
            if ($tieneSeisOMasMeses) {
                Log::info('✅ Resultado: SOLO RENOVACIÓN (próximo a vencer y tiene 7+ meses activo)');
            return [
                'inscripcion' => false,
                'renovacion' => true,
                'actualizacion' => false
            ];
            } else {
                Log::info('⚠️ Resultado: NINGÚN TRÁMITE DISPONIBLE (próximo a vencer pero NO tiene 7 meses activo)', [
                    'meses_faltantes' => 7 - $mesesActivo
                ]);
                return [
                    'inscripcion' => false,
                    'renovacion' => false,
                    'actualizacion' => false,
                    'mensaje_bloqueo' => "Para poder renovar su registro como proveedor, debe haber estado activo por al menos 7 meses. Actualmente lleva {$mesesActivo} " . ($mesesActivo == 1 ? 'mes' : 'meses') . " activo. Podrá renovar cuando complete los 7 meses requeridos."
                ];
            }
        }
        
        // Si es proveedor activo y vigente: SOLO ACTUALIZACIÓN
        Log::info('✅ Resultado: SOLO ACTUALIZACIÓN (proveedor vigente)');
        return [
            'inscripcion' => false,
            'renovacion' => false,
            'actualizacion' => true
        ];
    }

    private function obtenerInfoProveedor($user)
    {
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return null;
        }
        
        $proveedor = Proveedor::where('solicitante_id', $solicitante->id)
                             ->where('estado', 'Activo')
                             ->first();
        
        if (!$proveedor) {
            return null;
        }
        
        $ahora = Carbon::now();
        $fechaVencimiento = Carbon::parse($proveedor->fecha_vencimiento);
        
        $yaVencido = $fechaVencimiento < $ahora;
        $proximoAVencer = $fechaVencimiento <= $ahora->copy()->addDays(7);
        
        // Calcular tiempo restante de manera más precisa
        $tiempoRestante = $this->calcularTiempoRestante($ahora, $fechaVencimiento);
        
        return [
            'pv' => $proveedor->pv,
            'fecha_vencimiento' => $proveedor->fecha_vencimiento,
            'tiempo_restante' => $tiempoRestante,
            'ya_vencido' => $yaVencido,
            'proximo_a_vencer' => $proximoAVencer,
            'estado' => $proveedor->estado
        ];
    }

    private function calcularTiempoRestante($ahora, $fechaVencimiento)
    {
        if ($fechaVencimiento < $ahora) {
            // Ya vencido
            $diff = $ahora->diff($fechaVencimiento);
            $texto = "Vencido hace ";
            
            if ($diff->m > 0) {
                $texto .= $diff->m . " mes" . ($diff->m > 1 ? "es" : "");
                if ($diff->d > 0) {
                    $texto .= " y " . $diff->d . " día" . ($diff->d > 1 ? "s" : "");
                }
            } elseif ($diff->d > 0) {
                $texto .= $diff->d . " día" . ($diff->d > 1 ? "s" : "");
                if ($diff->h > 0) {
                    $texto .= " y " . $diff->h . " hora" . ($diff->h > 1 ? "s" : "");
                }
            } elseif ($diff->h > 0) {
                $texto .= $diff->h . " hora" . ($diff->h > 1 ? "s" : "");
            } else {
                $texto .= "menos de 1 hora";
            }
            
            return [
                'texto' => $texto,
                'clase_css' => 'text-red-600',
                'urgente' => true
            ];
        } else {
            // Tiempo restante
            $diff = $ahora->diff($fechaVencimiento);
            $texto = "";
            $urgente = false;
            $clase_css = 'text-green-600';
            
            if ($diff->m > 0) {
                $texto .= $diff->m . " mes" . ($diff->m > 1 ? "es" : "");
                if ($diff->d > 0) {
                    $texto .= " y " . $diff->d . " día" . ($diff->d > 1 ? "s" : "");
                }
            } elseif ($diff->d > 0) {
                $texto .= $diff->d . " día" . ($diff->d > 1 ? "s" : "");
                if ($diff->d <= 7) {
                    $urgente = true;
                    $clase_css = 'text-orange-600';
                    if ($diff->h > 0) {
                        $texto .= " y " . $diff->h . " hora" . ($diff->h > 1 ? "s" : "");
                    }
                }
            } elseif ($diff->h > 0) {
                $texto .= $diff->h . " hora" . ($diff->h > 1 ? "s" : "");
                $urgente = true;
                $clase_css = 'text-red-600';
            } else {
                $texto = "menos de 1 hora";
                $urgente = true;
                $clase_css = 'text-red-600';
            }
            
            return [
                'texto' => $texto,
                'clase_css' => $clase_css,
                'urgente' => $urgente
            ];
        }
    }

    private function verificarTramiteEnProgreso($user)
    {
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            Log::info('No se encontró solicitante para el usuario:', ['user_id' => $user->id]);
            return null;
        }

        // Buscar un trámite en progreso del solicitante con las relaciones necesarias
        // Incluir también trámites "Para Corrección" y "Rechazado" para que se muestren en el dashboard
        $tramite = Tramite::with([
            'detalleTramite.direccion.asentamiento.localidad.municipio.estado',
            'cita' => function($query) {
                $query->whereIn('estado', ['pendiente', 'confirmada']);
            }
        ])
        ->where('solicitante_id', $solicitante->id)
        ->whereIn('estado', ['Pendiente', 'En Revision', 'Para Corrección', 'Rechazado', 'Aprobado', 'Por Cotejar'])
        ->latest()
        ->first();
        
        if ($tramite) {
            Log::info('Trámite en progreso encontrado:', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'estado' => $tramite->estado,
                'progreso' => $tramite->progreso_tramite,
                'solicitante_id' => $solicitante->id
            ]);
        } else {
            Log::info('No se encontró trámite en progreso para el solicitante:', ['solicitante_id' => $solicitante->id]);
        }
        
        return $tramite;
    }

    public function iniciarInscripcion(Request $request)
    {
        $user = Auth::user();
        
        // Asegurar que el usuario tenga un solicitante
        $this->asegurarSolicitante($user);
        
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        Log::info('Iniciando inscripción:', [
            'user_id' => $user->id,
            'tramite_en_progreso' => $tramiteEnProgreso ? $tramiteEnProgreso->id : 'null',
            'tipo_tramite_progreso' => $tramiteEnProgreso ? $tramiteEnProgreso->tipo_tramite : 'null'
        ]);
        
        if ($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'inscripcion') {
            Log::info('Continuando trámite existente de inscripción:', ['tramite_id' => $tramiteEnProgreso->id]);
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        Log::info('Creando nuevo trámite de inscripción');
        // Crear nuevo trámite de inscripción
        return $this->crearNuevoTramite('inscripcion', $user);
    }

    public function iniciarRenovacion(Request $request)
    {
        $user = Auth::user();
        
        // Asegurar que el usuario tenga un solicitante
        $this->asegurarSolicitante($user);
        
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        Log::info('Iniciando renovación:', [
            'user_id' => $user->id,
            'tramite_en_progreso' => $tramiteEnProgreso ? $tramiteEnProgreso->id : 'null',
            'tipo_tramite_progreso' => $tramiteEnProgreso ? $tramiteEnProgreso->tipo_tramite : 'null'
        ]);
        
        if ($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'renovacion') {
            Log::info('Continuando trámite existente de renovación:', ['tramite_id' => $tramiteEnProgreso->id]);
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        Log::info('Creando nuevo trámite de renovación');
        // Crear nuevo trámite de renovación
        return $this->crearNuevoTramite('renovacion', $user);
    }

    public function iniciarActualizacion(Request $request)
    {
        $user = Auth::user();
        
        // Asegurar que el usuario tenga un solicitante
        $solicitante = $this->asegurarSolicitante($user);
        
        // ✅ VERIFICAR SI ES PROVEEDOR ACTIVO PRIMERO
        $proveedor = Proveedor::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Activo')
            ->first();
        
        if ($proveedor) {
            Log::info('Usuario es proveedor activo, mostrando selector de secciones:', [
                'user_id' => $user->id,
                'proveedor_pv' => $proveedor->pv,
                'solicitante_id' => $solicitante->id
            ]);
            
            // 🎯 REDIRIGIR AL SELECTOR DE SECCIONES
            return redirect()->route('tramites.actualizacion.selector');
        }
        
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        Log::info('Iniciando actualización (no es proveedor activo):', [
            'user_id' => $user->id,
            'tramite_en_progreso' => $tramiteEnProgreso ? $tramiteEnProgreso->id : 'null',
            'tipo_tramite_progreso' => $tramiteEnProgreso ? $tramiteEnProgreso->tipo_tramite : 'null'
        ]);
        
        if ($tramiteEnProgreso && strtolower($tramiteEnProgreso->tipo_tramite) === 'actualizacion') {
            Log::info('Continuando trámite existente de actualización:', ['tramite_id' => $tramiteEnProgreso->id]);
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        Log::info('Creando nuevo trámite de actualización completa');
        // Crear nuevo trámite de actualización completa (para no proveedores)
        return $this->crearNuevoTramite('actualizacion', $user);
    }

    private function continuarTramite($tramite)
    {
        // Verificar si debe mostrar la vista de estado (enviado para revisión)
        if ($tramite->debesMostrarEstado()) {
            return redirect()->route('tramites.solicitante.estado', ['tramite' => $tramite->id]);
        }

        // Si hay secciones rechazadas, retroceder a la primera sección rechazada
        $seccionesRechazadas = $tramite->getSeccionesParaCorregir();
                    if (!empty($seccionesRechazadas) && ($tramite->estado === 'Rechazado' || $tramite->estado === 'Para Corrección')) {
            $tramite->retrocederASeccionRechazada();
            
            return redirect()->route('tramites.create.tipo', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramite->id
            ])->with('warning', 'Hay secciones que necesitan corrección. Por favor, revise las observaciones.');
        }

        // Si el trámite no tiene constancia fiscal, redirigir primero a cargarla
        if (!$this->tieneConstanciaFiscal($tramite)) {
            Log::info('Trámite existente sin constancia fiscal, redirigiendo:', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'progreso' => $tramite->progreso_tramite
            ]);
            
            return redirect()->route('tramites.solicitante.constancia-fiscal', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramite->id
            ])->with('info', 'Para continuar con su trámite, necesitamos validar su constancia de situación fiscal.');
        }

        // Si ya tiene constancia fiscal, ir directamente al formulario
        return redirect()->route('tramites.create.tipo', [
            'tipo_tramite' => strtolower($tramite->tipo_tramite),
            'tramite' => $tramite->id
        ]);
    }

    private function crearNuevoTramite($tipoTramite, $user)
    {
        // Buscar el solicitante asociado al usuario (ya garantizado que existe)
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();

        // Verificar OTRA VEZ si ya existe un trámite del mismo tipo en progreso
        $tramiteExistente = Tramite::where('solicitante_id', $solicitante->id)
            ->where('tipo_tramite', ucfirst($tipoTramite))
            ->whereIn('estado', ['Pendiente', 'En Revision', 'Para Corrección', 'Rechazado'])
            ->first();
            
        if ($tramiteExistente) {
            Log::warning('Intento de crear trámite duplicado, redirigiendo al existente:', [
                'tramite_existente_id' => $tramiteExistente->id,
                'tipo_tramite' => $tipoTramite,
                'solicitante_id' => $solicitante->id
            ]);
            
            return $this->continuarTramite($tramiteExistente);
        }

        // Crear nuevo trámite SOLO si no existe uno del mismo tipo
        $tramite = Tramite::create([
            'solicitante_id' => $solicitante->id,
            'tipo_tramite' => ucfirst($tipoTramite),
            'estado' => 'Pendiente',
            'progreso_tramite' => 0,
            'fecha_inicio' => now(),
        ]);
        
        Log::info('Nuevo trámite creado desde solicitante:', [
            'tramite_id' => $tramite->id,
            'tipo_tramite' => $tipoTramite,
            'solicitante_id' => $solicitante->id
        ]);

        // Log del sistema para auditoría
        SystemLogService::tramiteCreated($tramite->id, ucfirst($tipoTramite), $solicitante->razon_social ?? $solicitante->nombre_completo ?? 'Solicitante');
        
        // Para nuevos trámites, verificar si necesita constancia fiscal
        if ($this->necesitaConstanciaFiscal($tramite)) {
            Log::info('Nuevo trámite requiere constancia fiscal, redirigiendo:', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tipoTramite
            ]);
            
            return redirect()->route('tramites.solicitante.constancia-fiscal', [
                'tipo_tramite' => strtolower($tipoTramite),
                'tramite' => $tramite->id
            ])->with('info', 'Para continuar, necesitamos validar su constancia de situación fiscal.');
        }
        
        // Si no necesita constancia fiscal, ir directamente al formulario
        return redirect()->route('tramites.create.tipo', [
            'tipo_tramite' => strtolower($tipoTramite),
            'tramite' => $tramite->id
        ]);
    }

    /**
     * Verifica si el usuario ya tiene constancia de situación fiscal procesada
     */
    private function tieneConstanciaFiscal($tramite)
    {
        // Verificar si este trámite específico ya tiene progreso
        if ($tramite->progreso_tramite > 0) {
            return true;
        }

        // Verificar si el usuario ya tiene algún trámite con datos de domicilio
        $solicitante = $tramite->solicitante;
        if (!$solicitante) {
            return false;
        }

        // Verificar si ya tiene algún detalle de trámite con dirección
        $tieneDetalleTramiteConDireccion = \App\Models\DetalleTramite::whereHas('tramite', function($query) use ($solicitante) {
                $query->where('solicitante_id', $solicitante->id);
            })
            ->whereNotNull('direccion_id')
            ->exists();

        if ($tieneDetalleTramiteConDireccion) {
            Log::info('Usuario ya tiene trámite con domicilio, omitiendo constancia fiscal:', [
                'solicitante_id' => $solicitante->id,
                'tramite_actual_id' => $tramite->id
            ]);
            return true;
        }

        // Verificar si ya tiene documento de constancia fiscal en cualquier trámite
        $tieneConstanciaFiscal = \App\Models\DocumentoSolicitante::whereHas('documento', function($query) {
                $query->where('nombre', 'like', '%Constancia%Fiscal%')
                      ->orWhere('nombre', 'like', '%Situación Fiscal%');
            })
            ->whereHas('tramite', function($query) use ($solicitante) {
                $query->where('solicitante_id', $solicitante->id);
            })
            ->where('estado', 'Aprobado')
            ->exists();

        if ($tieneConstanciaFiscal) {
            Log::info('Usuario ya tiene constancia fiscal aprobada en otro trámite:', [
                'solicitante_id' => $solicitante->id,
                'tramite_actual_id' => $tramite->id
            ]);
            return true;
        }

        return false;
    }

    /**
     * Determina si un nuevo trámite necesita constancia fiscal
     */
    private function necesitaConstanciaFiscal($tramite)
    {
        // Un nuevo trámite necesita constancia fiscal solo si:
        // 1. No tiene progreso (recién creado)
        // 2. El usuario no tiene constancia fiscal ya procesada en ningún trámite
        // 3. El usuario no tiene datos de domicilio en ningún trámite
        
        $tieneConstancia = $this->tieneConstanciaFiscal($tramite);
        
        Log::info('Verificando necesidad de constancia fiscal:', [
            'tramite_id' => $tramite->id,
            'progreso_tramite' => $tramite->progreso_tramite,
            'tiene_constancia' => $tieneConstancia
        ]);

        return $tramite->progreso_tramite == 0 && !$tieneConstancia;
    }

    /**
     * Asegura que el usuario tenga un registro de solicitante
     */
    private function asegurarSolicitante($user)
    {
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            Log::info('Creando solicitante automáticamente para usuario:', [
                'user_id' => $user->id,
                'user_rfc' => $user->rfc ?? 'N/A',
                'user_nombre' => $user->nombre ?? 'N/A'
            ]);
            
            $solicitante = Solicitante::create([
                'usuario_id' => $user->id,
                'rfc' => $user->rfc ?? '',
                'nombre_completo' => $user->nombre ?? '',
                'razon_social' => '',
                'tipo_persona' => 'Física', // Por defecto
                'curp' => '',
                'giro' => '',
                'estado' => 'Activo'
            ]);
            
            Log::info('Solicitante creado automáticamente:', [
                'solicitante_id' => $solicitante->id,
                'usuario_id' => $user->id
            ]);
        }
        
        return $solicitante;
    }

    /**
     * Muestra el formulario para cargar la constancia de situación fiscal
     */
    public function mostrarConstanciaFiscal($tipoTramite, $tramiteId)
    {
        try {
            $tramite = Tramite::with(['solicitante'])->find($tramiteId);
            
            if (!$tramite) {
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'Trámite no encontrado');
            }

            // Verificar que el trámite pertenece al usuario actual
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'No tienes permisos para acceder a este trámite');
            }

            return view('tramites.solicitante.constancia-fiscal', [
                'tramite' => $tramite,
                'tipoTramite' => $tipoTramite,
                'solicitante' => $tramite->solicitante
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de constancia fiscal:', [
                'message' => $e->getMessage(),
                'tramite_id' => $tramiteId
            ]);

            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'Error al cargar el formulario de constancia fiscal');
        }
    }

    /**
     * Procesa la carga de la constancia de situación fiscal
     */
    public function subirConstanciaFiscal(Request $request)
    {
        try {
            $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id',
                'tipo_tramite' => 'required|string',
                'sat_data' => 'required|string'
            ]);

            $tramite = Tramite::find($request->tramite_id);
            
            if (!$tramite) {
                return back()->with('error', 'Trámite no encontrado');
            }

            // Verificar que el trámite pertenece al usuario actual
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
                return back()->with('error', 'No tienes permisos para este trámite');
            }

            // Decodificar datos del SAT
            $satData = json_decode($request->sat_data, true);
            
            if (!$satData || !isset($satData['details'])) {
                return back()->with('error', 'Los datos de la constancia fiscal no son válidos');
            }

            $details = $satData['details'];

            // Validar que el RFC coincida
            if (isset($details['rfc']) && $details['rfc'] !== $solicitante->rfc) {
                return back()->with('error', 'El RFC de la constancia no coincide con el RFC registrado');
            }

            // Actualizar datos del solicitante si están disponibles
            $updateData = [];
            if (isset($details['razonSocial']) && !empty($details['razonSocial'])) {
                $updateData['razon_social'] = $details['razonSocial'];
            }
            if (isset($details['nombreCompleto']) && !empty($details['nombreCompleto']) && !isset($updateData['razon_social'])) {
                $updateData['razon_social'] = $details['nombreCompleto'];
            }
            if (isset($details['curp']) && !empty($details['curp'])) {
                $updateData['curp'] = $details['curp'];
            }
            if (isset($details['tipoPersona']) && !empty($details['tipoPersona'])) {
                $updateData['tipo_persona'] = ucfirst(strtolower($details['tipoPersona']));
                if ($updateData['tipo_persona'] === 'Fisica') {
                    $updateData['tipo_persona'] = 'Física';
                }
            }

            if (!empty($updateData)) {
                $solicitante->update($updateData);
                Log::info('Datos del solicitante actualizados desde constancia fiscal:', [
                    'solicitante_id' => $solicitante->id,
                    'datos' => $updateData
                ]);
            }

            // Actualizar el progreso del trámite para indicar que ya tiene constancia
            $observaciones = 'Constancia de situación fiscal procesada. RFC: ' . ($details['rfc'] ?? 'N/A');
            if (isset($details['razonSocial'])) {
                $observaciones .= ', Razón Social: ' . $details['razonSocial'];
            }

            $tramite->update([
                'progreso_tramite' => 1,
                'observaciones' => $observaciones
            ]);

            Log::info('Constancia fiscal procesada exitosamente:', [
                'tramite_id' => $tramite->id,
                'rfc' => $details['rfc'] ?? 'N/A',
                'tipo_persona' => $details['tipoPersona'] ?? 'N/A'
            ]);

            // Extraer código postal del SAT si está disponible
            $codigoPostalSat = null;
            if ($request->has('codigo_postal_sat') && !empty($request->codigo_postal_sat)) {
                $codigoPostalSat = $request->codigo_postal_sat;
                Log::info('Código postal del SAT recibido:', ['cp' => $codigoPostalSat]);
            }

            // Redirigir al formulario principal del trámite
            $redirectRoute = redirect()->route('tramites.create.tipo', [
                'tipo_tramite' => $request->tipo_tramite,
                'tramite' => $tramite->id
            ])->with('success', 'Constancia de situación fiscal procesada exitosamente. Los datos han sido extraídos automáticamente.');

            // Si hay código postal del SAT, agregarlo a la sesión
            if ($codigoPostalSat) {
                $redirectRoute->with('codigo_postal_sat', $codigoPostalSat);
            }

            return $redirectRoute;

        } catch (\Exception $e) {
            Log::error('Error al procesar constancia fiscal:', [
                'message' => $e->getMessage(),
                'tramite_id' => $request->tramite_id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al procesar la constancia fiscal: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los datos de domicilio de un trámite específico
     */
    private function obtenerDatosDomicilio($tramite)
    {
        try {
            // Usar el nuevo método del DetalleTramiteController
            $detalleTramiteController = new DetalleTramiteController();
            $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramite->id);
            
            if ($datosDomicilio) {
                Log::info('🏠 DEBUG DOMICILIO: Datos obtenidos exitosamente usando DetalleTramiteController', [
                    'tramite_id' => $tramite->id,
                    'codigo_postal' => $datosDomicilio['codigo_postal'],
                    'estado' => $datosDomicilio['estado'],
                    'municipio' => $datosDomicilio['municipio']
                ]);
                return $datosDomicilio;
            }
            
            // Si no hay datos, retornar estructura básica
            Log::info('🏠 DEBUG DOMICILIO: No se encontraron datos de domicilio', [
                'tramite_id' => $tramite->id
            ]);
            
            return [
                'tramite_id' => $tramite->id,
            ];
            
        } catch (\Exception $e) {
            Log::error('🏠 DEBUG DOMICILIO: Error al obtener datos de domicilio', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'tramite_id' => $tramite->id,
            ];
        }
    }

    /**
     * Obtiene los datos del trámite del usuario autenticado
     */
    public function obtenerDatosTramite()
    {
        try {
            $user = Auth::user();
            
            // Buscar el solicitante asociado al usuario
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Buscar el trámite en progreso más reciente
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            $datosTramite = [
                'paso_inicial' => 1,
                'tipo_persona' => $solicitante->tipo_persona,
                'rfc' => $solicitante->rfc,
                'curp' => $solicitante->curp,
                'nombre_completo' => $solicitante->nombre_completo,
                'razon_social' => $solicitante->razon_social,
                'objeto_social' => $solicitante->objeto_social,
                'tramite_id' => null,
                'tipo_tramite' => 'inscripcion'
            ];

            if ($tramite) {
                $datosTramite = [
                    'paso_inicial' => $tramite->progreso_tramite ?? 1,
                    'tipo_persona' => $solicitante->tipo_persona,
                    'rfc' => $solicitante->rfc,
                    'curp' => $solicitante->curp,
                    'nombre_completo' => $solicitante->nombre_completo,
                    'razon_social' => $solicitante->razon_social,
                    'objeto_social' => $solicitante->objeto_social,
                    'tramite_id' => $tramite->id,
                    'tipo_tramite' => strtolower($tramite->tipo_tramite)
                ];
            }

            return response()->json($datosTramite);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los documentos requeridos según el tipo de persona
     */
    public function obtenerDocumentos()
    {
        try {
            $user = Auth::user();
            
            // Obtener el solicitante para determinar el tipo de persona
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tipoPersona = $solicitante->tipo_persona;

            // Obtener el trámite en progreso
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            // Obtener documentos según el tipo de persona
            $documentos = Documento::where(function($query) use ($tipoPersona) {
                $query->where('tipo_persona', $tipoPersona)
                      ->orWhere('tipo_persona', 'Ambas');
            })
            ->where('es_visible', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'descripcion', 'tipo_persona']);

            // Si hay un trámite, verificar qué documentos ya están subidos
            if ($tramite) {
                $documentosSubidos = \App\Models\DocumentoSolicitante::where('tramite_id', $tramite->id)
                    ->get()
                    ->keyBy('documento_id');

                $documentos = $documentos->map(function($documento) use ($documentosSubidos) {
                    $docSubido = $documentosSubidos->get($documento->id);
                    
                    // Los modelos de IA fueron removidos del sistema
                    $tieneModeloIA = false;
                    
                    // No hay validación IA disponible
                    $validacionIA = null;
                    
                    return [
                        'id' => $documento->id,
                        'nombre' => $documento->nombre,
                        'descripcion' => $documento->descripcion,
                        'tipo_persona' => $documento->tipo_persona,
                        'estado' => $docSubido ? ucfirst($docSubido->estado) : 'Pendiente',
                        'fecha_entrega' => $docSubido ? $docSubido->fecha_entrega : null,
                        'ruta_archivo' => $docSubido ? true : null, // Solo indicar si existe
                        'observaciones' => $docSubido ? $docSubido->observaciones : null,
                        'tiene_modelo_ia' => $tieneModeloIA,
                        'validacion_ia' => null
                    ];
                });
            } else {
                // Si no hay trámite, todos los documentos están pendientes
                $documentos = $documentos->map(function($documento) {
                    // Los modelos de IA fueron removidos del sistema
                    $tieneModeloIA = false;
                    
                    return [
                        'id' => $documento->id,
                        'nombre' => $documento->nombre,
                        'descripcion' => $documento->descripcion,
                        'tipo_persona' => $documento->tipo_persona,
                        'estado' => 'Pendiente',
                        'fecha_entrega' => null,
                        'ruta_archivo' => null,
                        'tiene_modelo_ia' => $tieneModeloIA
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'documentos' => $documentos,
                'tipo_persona' => $tipoPersona,
                'tramite_id' => $tramite ? $tramite->id : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sube un documento del trámite
     */
    public function subirDocumento(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:pdf|max:102400', // 100MB máximo
                'documento_id' => 'required|integer|exists:documento,id'
            ]);

            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Obtener el trámite en progreso
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró un trámite en progreso'
                ], 404);
            }

            $file = $request->file('archivo');
            $documentoId = $request->documento_id;

            // Obtener información del documento esperado
            $documentoInfo = \App\Models\Documento::find($documentoId);
            if (!$documentoInfo) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Tipo de documento no válido'
                ], 400);
            }

            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid('doc_' . $documentoId . '_') . '.' . $extension;
            
            // Almacenar archivo en la ruta correcta que espera el controlador de visualización
            $ruta = $file->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');

            // Crear o actualizar el registro del documento
            $documentoSolicitante = \App\Models\DocumentoSolicitante::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'documento_id' => $documentoId
                ],
                [
                    'fecha_entrega' => now(),
                    'estado' => 'Pendiente',
                    'version_documento' => 1,
                    'ruta_archivo' => encrypt($ruta), // Encriptar la ruta
                    'nombre_original' => $file->getClientOriginalName()
                ]
            );

            return response()->json([
                'success' => true,
                'mensaje' => 'Documento subido correctamente',
                'ruta' => $ruta,
                'docSolicitanteId' => $documentoSolicitante->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al subir documento', [
                'error' => $e->getMessage(),
                'documento_id' => $request->documento_id ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al subir el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reemplaza un documento previamente subido
     */
    public function reemplazarDocumento(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:pdf|max:102400', // 100MB máximo
                'documento_solicitante_id' => 'required|integer|exists:documento_solicitante,id'
            ]);

            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 200);
            }

            // Obtener el documento a reemplazar
            $documentoSolicitante = DocumentoSolicitante::find($request->documento_solicitante_id);
            
            if (!$documentoSolicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 200);
            }

            // Verificar que el documento pertenece al usuario
            $tramite = Tramite::where('id', $documentoSolicitante->tramite_id)
                ->where('solicitante_id', $solicitante->id)
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para modificar este documento'
                ], 200);
            }

            // Verificar que el documento esté rechazado
            if ($documentoSolicitante->estado !== 'Rechazado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden reemplazar documentos rechazados'
                ], 200);
            }

            $file = $request->file('archivo');

            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid('doc_' . $documentoSolicitante->documento_id . '_') . '.' . $extension;
            
            // Almacenar archivo en la ruta correcta
            $ruta = $file->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');

            // Actualizar el registro del documento
            $documentoSolicitante->update([
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => $documentoSolicitante->version_documento + 1,
                'ruta_archivo' => encrypt($ruta), // Encriptar la ruta
                'nombre_original' => $file->getClientOriginalName(),
                'observaciones' => null, // Limpiar observaciones de rechazo
                'fecha_revision' => null,
                'documento_cotejado' => false // Resetear estado de cotejo
            ]);

            // Registrar el cambio en logs
            Log::info('Documento reemplazado', [
                'user_id' => $user->id,
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitante->id,
                'nombre_archivo' => $file->getClientOriginalName(),
                'version' => $documentoSolicitante->version_documento
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documento reemplazado correctamente. Será revisado nuevamente por el personal administrativo.',
                'ruta' => $ruta,
                'docSolicitanteId' => $documentoSolicitante->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al reemplazar documento', [
                'error' => $e->getMessage(),
                'documento_solicitante_id' => $request->documento_solicitante_id ?? 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al reemplazar el documento: ' . $e->getMessage()
            ], 200);
        }
    }

    /**
     * Obtiene el estado actualizado del trámite para actualizaciones automáticas
     */
    public function obtenerEstadoActualizado($tramiteId)
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 200);
            }

            // Verificar que el trámite pertenece al usuario
            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->with(['documentosSolicitante.documento', 'seccionesRevision', 'cita' => function ($query) {
                    $query->whereIn('estado', ['pendiente', 'confirmada']);
                }])
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 200);
            }

            // Obtener información actualizada
            $documentosIndividuales = $tramite->documentosSolicitante()->with('documento')->get();
            $totalDocs = $documentosIndividuales->count();
            $aprobados = $documentosIndividuales->where('estado', 'Aprobado')->count();
            $rechazados = $documentosIndividuales->where('estado', 'Rechazado')->count();
            $enRevision = $documentosIndividuales->whereIn('estado', ['En Revision', 'Pendiente'])->count();

            // Preparar datos de documentos
            $documentos = $documentosIndividuales->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'nombre' => $doc->documento->nombre ?? 'Documento sin nombre',
                    'descripcion' => $doc->documento->descripcion ?? '',
                    'estado' => $doc->estado ?? 'Pendiente',
                    'observaciones' => $doc->observaciones,
                    'documento_cotejado' => $doc->documento_cotejado ?? false,
                    'fecha_entrega' => $doc->fecha_entrega ? $doc->fecha_entrega->format('d/m/Y') : null,
                    'fecha_revision' => $doc->fecha_revision ? $doc->fecha_revision->format('d/m/Y') : null,
                ];
            });

            // Determinar estado de la sección de documentos
            $estadoSeccionDocumentos = 'Sin documentos';
            if ($totalDocs > 0) {
                if ($rechazados > 0) {
                    $estadoSeccionDocumentos = "$rechazados documento(s) rechazado(s)";
                } elseif ($aprobados === $totalDocs) {
                    $estadoSeccionDocumentos = "Todos los documentos aprobados ($aprobados/$totalDocs)";
                } else {
                    $estadoSeccionDocumentos = "En revisión ($aprobados aprobados, $enRevision pendientes)";
                }
            }

            // Obtener información de secciones
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $secciones = $tipoPersona === 'Moral' ? [
                1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle'],
                2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt'],
                3 => ['nombre' => 'Constitución', 'icono' => 'fa-building'],
                4 => ['nombre' => 'Accionistas', 'icono' => 'fa-users'],
                5 => ['nombre' => 'Apoderado Legal', 'icono' => 'fa-user-tie'],
                6 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload']
            ] : [
                1 => ['nombre' => 'Datos Generales', 'icono' => 'fa-user-circle'],
                2 => ['nombre' => 'Domicilio', 'icono' => 'fa-map-marker-alt'],
                3 => ['nombre' => 'Documentos', 'icono' => 'fa-file-upload']
            ];

            $progresoMaximo = $tipoPersona === 'Moral' ? 6 : 3;
            $estadosSecciones = [];
            
            foreach ($secciones as $numero => $seccion) {
                $seccionRechazada = $tramite->seccionEstaRechazada($numero);
                $seccionAprobada = $tramite->seccionEstaAprobada($numero);
                
                if ($seccion['nombre'] === 'Documentos') {
                    $estadosSecciones[$numero] = [
                        'nombre' => $seccion['nombre'],
                        'estado' => $estadoSeccionDocumentos,
                        'rechazada' => $rechazados > 0,
                        'aprobada' => $aprobados === $totalDocs && $totalDocs > 0,
                        'en_revision' => $enRevision > 0 && $rechazados === 0
                    ];
                } else {
                    $estadosSecciones[$numero] = [
                        'nombre' => $seccion['nombre'],
                        'rechazada' => $seccionRechazada,
                        'aprobada' => $seccionAprobada,
                        'en_revision' => !$seccionRechazada && !$seccionAprobada && $tramite->progreso_tramite >= $numero
                    ];
                }
            }

            // Preparar información de la cita
            $citaInfo = null;
            if ($tramite->cita) {
                $dias = [
                    'Monday' => 'Lunes',
                    'Tuesday' => 'Martes', 
                    'Wednesday' => 'Miércoles',
                    'Thursday' => 'Jueves',
                    'Friday' => 'Viernes',
                    'Saturday' => 'Sábado',
                    'Sunday' => 'Domingo'
                ];
                
                $citaInfo = [
                    'id' => $tramite->cita->id,
                    'fecha' => $tramite->cita->fecha_hora->format('d/m/Y'),
                    'hora' => $tramite->cita->fecha_hora->format('H:i'),
                    'dia' => $dias[$tramite->cita->fecha_hora->format('l')] ?? $tramite->cita->fecha_hora->format('l'),
                    'fecha_hora' => $tramite->cita->fecha_hora->format('Y-m-d H:i:s'),
                    'estado' => $tramite->cita->estado,
                    'motivo' => $tramite->cita->motivo,
                    'notas' => $tramite->cita->notas,
                    'ubicacion' => [
                        'nombre' => 'Ciudad Administrativa de Oaxaca',
                        'edificio' => 'Edificio 1',
                        'modulo' => 'Módulo de Proveedores'
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tramite' => [
                        'id' => $tramite->id,
                        'estado' => $tramite->estado,
                        'progreso_tramite' => $tramite->progreso_tramite,
                        'progreso_maximo' => $progresoMaximo,
                        'porcentaje_progreso' => $tramite->getPorcentajeProgreso(),
                        'observaciones' => $tramite->observaciones,
                        'fecha_inicio' => $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y') : null,
                        'fecha_finalizacion' => $tramite->fecha_finalizacion ? $tramite->fecha_finalizacion->format('d/m/Y') : null,
                        'puede_ser_editado' => $tramite->puedeSerEditado()
                    ],
                    'cita' => $citaInfo,
                    'documentos' => $documentos,
                    'estadisticas_documentos' => [
                        'total' => $totalDocs,
                        'aprobados' => $aprobados,
                        'rechazados' => $rechazados,
                        'en_revision' => $enRevision
                    ],
                    'secciones' => $estadosSecciones,
                    'timestamp' => now()->timestamp
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estado actualizado del trámite', [
                'error' => $e->getMessage(),
                'tramite_id' => $tramiteId,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado del trámite'
            ], 200);
        }
    }

    /**
     * Verifica si existe un modelo entrenado para el tipo de documento
     */
    private function tieneModeloEntrenado($tipoDocumento)
    {
        try {
            // Obtener modelo IA activo
            $aiModel = \App\Models\AI\AiDocumentModel::getDefault();
            
            if (!$aiModel || !$aiModel->supported_document_types) {
                // Si no hay modelo o no tiene tipos soportados, usar lógica básica
                return $this->tieneModeloEntrenadoBasico($tipoDocumento);
            }
            
            // Verificar si el modelo soporta exactamente este tipo de documento
            if ($aiModel->supportsDocumentType($tipoDocumento)) {
                Log::info('✅ Modelo IA encontrado para documento', [
                    'tipo_documento' => $tipoDocumento,
                    'modelo_id' => $aiModel->id,
                    'modelo_nombre' => $aiModel->name
                ]);
                return true;
            }
            
            // Verificar equivalencias y patrones
            foreach ($aiModel->supported_document_types as $tipoSoportado) {
                if ($this->sonTiposEquivalentes($tipoDocumento, $tipoSoportado)) {
                    Log::info('✅ Modelo IA encontrado por equivalencia', [
                        'tipo_documento' => $tipoDocumento,
                        'tipo_equivalente' => $tipoSoportado,
                        'modelo_id' => $aiModel->id
                    ]);
                    return true;
                }
            }
            
            Log::info('⚠️ No hay modelo IA para este tipo de documento', [
                'tipo_documento' => $tipoDocumento,
                'tipos_soportados' => $aiModel->supported_document_types
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::warning('Error verificando modelo entrenado, usando lógica básica', [
                'tipo_documento' => $tipoDocumento,
                'error' => $e->getMessage()
            ]);
            
            return $this->tieneModeloEntrenadoBasico($tipoDocumento);
        }
    }
    
    /**
     * Lógica básica para verificar modelos entrenados (fallback)
     */
    private function tieneModeloEntrenadoBasico($tipoDocumento)
    {
        // Lista de documentos para los cuales tenemos modelos entrenados (hardcoded como fallback)
        $documentosConModelo = [
            'Constancia de Situación Fiscal',
            'Acta de Nacimiento', 
            'Credencial de Elector',
            'Comprobante de Domicilio',
            'CURP',
            'RFC',
            'Acta Constitutiva',
            'Identificación Oficial',
            'Poder Notarial'
        ];
        
        // Verificación exacta
        if (in_array($tipoDocumento, $documentosConModelo)) {
            return true;
        }
        
        // Verificación por patrones
        $tipoLower = strtolower($tipoDocumento);
        $patrones = [
            'constancia' => ['constancia', 'fiscal', 'sat'],
            'acta' => ['acta', 'constitutiva', 'nacimiento'],
            'credencial' => ['credencial', 'ine', 'elector'],
            'comprobante' => ['comprobante', 'domicilio', 'recibo'],
            'identificacion' => ['identificacion', 'oficial', 'id'],
            'poder' => ['poder', 'notarial', 'apoderado'],
            'curp' => ['curp', 'clave', 'unica'],
            'rfc' => ['rfc', 'registro', 'federal']
        ];
        
        foreach ($patrones as $categoria => $palabras) {
            foreach ($palabras as $palabra) {
                if (str_contains($tipoLower, $palabra)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Verifica si dos tipos de documento son equivalentes
     */
    private function sonTiposEquivalentes($tipo1, $tipo2)
    {
        $equivalencias = [
            'constancia de situación fiscal' => ['constancia fiscal', 'situación fiscal', 'constancia sat'],
            'acta de nacimiento' => ['acta nacimiento', 'certificado nacimiento'],
            'credencial de elector' => ['ine', 'credencial ine', 'credencial elector'],
            'comprobante de domicilio' => ['comprobante domicilio', 'recibo servicios', 'factura servicios'],
            'curp' => ['clave única', 'curp'],
            'rfc' => ['registro federal contribuyente', 'clave rfc']
        ];
        
        $tipo1Lower = strtolower($tipo1);
        $tipo2Lower = strtolower($tipo2);
        
        foreach ($equivalencias as $principal => $variantes) {
            $todos = array_merge([$principal], $variantes);
            if (in_array($tipo1Lower, $todos) && in_array($tipo2Lower, $todos)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verifica si el documento es correcto según IA considerando alternativas
     */
    private function esDocumentoCorrectoIA($tipoPredicho, $tipoEsperado, $alternativas = [])
    {
        // Verificación directa
        if (strtolower($tipoPredicho) === strtolower($tipoEsperado)) {
            return true;
        }
        
        // Verificación por equivalencias
        if ($this->sonTiposEquivalentes($tipoPredicho, $tipoEsperado)) {
            return true;
        }
        
        // Verificar alternativas si están disponibles
        foreach ($alternativas as $alternativa) {
            if (isset($alternativa['document_type'])) {
                $tipoAlternativo = $alternativa['document_type'];
                if (strtolower($tipoAlternativo) === strtolower($tipoEsperado) ||
                    $this->sonTiposEquivalentes($tipoAlternativo, $tipoEsperado)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Obtiene el tipo predominante considerando predicción principal y alternativas
     */
    private function obtenerTipoPredominante($tipoPrincipal, $alternativas = [])
    {
        // Si no hay alternativas, devolver el tipo principal
        if (empty($alternativas)) {
            return $tipoPrincipal;
        }
        
        // Buscar la alternativa con mayor confianza
        $mejorAlternativa = null;
        $mayorConfianza = 0;
        
        foreach ($alternativas as $alternativa) {
            if (isset($alternativa['confidence']) && $alternativa['confidence'] > $mayorConfianza) {
                $mayorConfianza = $alternativa['confidence'];
                $mejorAlternativa = $alternativa;
            }
        }
        
        // Si la mejor alternativa tiene buena confianza, usarla
        if ($mejorAlternativa && $mayorConfianza > 0.7) {
            return $mejorAlternativa['document_type'] ?? $tipoPrincipal;
        }
        
        return $tipoPrincipal;
    }

    /**
     * Finaliza el trámite
     */
    public function finalizarTramite()
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            // Obtener el trámite en progreso
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un trámite en progreso'
                ], 404);
            }

            // Actualizar el estado del trámite
            $tramite->update([
                'estado' => 'En Revision', // Cambiar a 'En Revision' para que sea consistente con el estado.blade.php
                'fecha_finalizacion' => now(),
                'progreso_tramite' => $solicitante->tipo_persona === 'Física' ? 3 : 6
            ]);

            // Log del sistema para auditoría
            SystemLogService::tramiteFinalized($tramite->id, $tramite->tipo_tramite, $solicitante->razon_social ?? $solicitante->nombre_completo ?? 'Solicitante');

            return response()->json([
                'success' => true,
                'message' => 'Trámite finalizado correctamente',
                'redirect' => route('tramites.solicitante.estado', ['tramite' => $tramite->id])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el trámite: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint para obtener datos de domicilio de un trámite
     */
    public function obtenerDatosDomicilioAPI(Request $request, $tramiteId)
    {
        try {
            $tramite = Tramite::with([
                'detalleTramite.direccion.asentamiento.localidad.municipio.estado'
            ])->find($tramiteId);

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            $datosDomicilio = $this->obtenerDatosDomicilio($tramite);
            
            return response()->json([
                'success' => true,
                'datos' => $datosDomicilio,
                'debug' => [
                    'tramite_id' => $tramite->id,
                    'tiene_detalle' => $tramite->detalleTramite ? 'SI' : 'NO',
                    'direccion_id' => $tramite->detalleTramite->direccion_id ?? 'NULL',
                    'tiene_direccion' => ($tramite->detalleTramite && $tramite->detalleTramite->direccion) ? 'SI' : 'NO'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en obtenerDatosDomicilioAPI:', [
                'message' => $e->getMessage(),
                'tramite_id' => $tramiteId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el estado actual del trámite
     */
    public function mostrarEstadoTramite($tramiteId)
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'No se encontró información del solicitante');
            }

            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->with(['cita' => function ($query) {
                    $query->whereIn('estado', ['pendiente', 'confirmada']);
                }])
                ->first();

            if (!$tramite) {
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'Trámite no encontrado');
            }

            return view('tramites.solicitante.estado', compact('tramite'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar estado del trámite:', [
                'tramite_id' => $tramiteId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'Error al cargar el estado del trámite');
        }
    }

    /**
     * Habilita la edición de un trámite rechazado
     */
    public function habilitarEdicion($tramiteId)
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            if (!$tramite->puedeSerEditado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este trámite no puede ser editado en su estado actual'
                ], 400);
            }

            $tramite->habilitarEdicion();

            Log::info('✅ Edición habilitada para trámite:', [
                'tramite_id' => $tramite->id,
                'estado_anterior' => 'Rechazado',
                'estado_nuevo' => $tramite->estado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trámite habilitado para edición',
                'redirect_url' => route('tramites.create.tipo', [
                    'tipo_tramite' => strtolower($tramite->tipo_tramite),
                    'tramite' => $tramite->id
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Error al habilitar edición del trámite:', [
                'tramite_id' => $tramiteId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al habilitar la edición del trámite'
            ], 500);
        }
    }

    /**
     * Habilita la corrección de una sección específica rechazada
     */
    public function corregirSeccion($tramiteId, $seccionId)
    {
        try {
            $user = Auth::user();
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del solicitante'
                ], 404);
            }

            $tramite = Tramite::where('id', $tramiteId)
                ->where('solicitante_id', $solicitante->id)
                ->first();

            if (!$tramite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trámite no encontrado'
                ], 404);
            }

            // Verificar que la sección está rechazada
            if (!$tramite->seccionEstaRechazada($seccionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta sección no está rechazada o no requiere corrección'
                ], 400);
            }

            // Cambiar el estado del trámite a pendiente para permitir edición
            $tramite->update([
                'estado' => 'Pendiente',
                'progreso_tramite' => $seccionId, // Retroceder al paso de la sección rechazada
            ]);

            // Marcar la sección como pendiente para permitir nueva revisión
            $revision = $tramite->getEstadoSeccion($seccionId);
            if ($revision) {
                $revision->update(['estado' => 'pendiente']);
            }

            Log::info('✅ Corrección habilitada para sección:', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId,
                'progreso_actualizado' => $seccionId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sección habilitada para corrección',
                'redirect_url' => route('tramites.create.tipo', [
                    'tipo_tramite' => strtolower($tramite->tipo_tramite),
                    'tramite' => $tramite->id
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Error al habilitar corrección de sección:', [
                'tramite_id' => $tramiteId,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al habilitar la corrección de la sección'
            ], 500);
        }
    }

    /**
     * Verifica automáticamente el estado del proveedor del usuario y lo actualiza si está vencido
     */
    private function verificarEstadoProveedorAutomatico($user)
    {
        try {
            $solicitante = Solicitante::where('usuario_id', $user->id)->first();
            
            if (!$solicitante) {
                return;
            }

            $proveedor = Proveedor::where('solicitante_id', $solicitante->id)
                                 ->where('estado', 'Activo')
                                 ->first();

            if (!$proveedor) {
                return;
            }

            $resultado = $proveedor->actualizarEstadoAutomatico();
            
            if ($resultado['cambio']) {
                Log::info('✅ Estado de proveedor actualizado automáticamente:', [
                    'pv' => $resultado['pv'],
                    'estado_anterior' => $resultado['estado_anterior'],
                    'estado_nuevo' => $resultado['estado_nuevo'],
                    'user_id' => $user->id,
                    'metodo' => 'verificacion_automatica_en_index'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error en verificación automática de estado de proveedor:', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verifica si el documento predicho es correcto
     */
    private function esDocumentoCorrecto($tipoPredicho, $tipoEsperado)
    {
        if (!$tipoPredicho || !$tipoEsperado) {
            return false;
        }
        
        // Normalizar nombres para comparación
        $predicho = strtolower(trim($tipoPredicho));
        $esperado = strtolower(trim($tipoEsperado));
        
        // Comparación exacta
        if ($predicho === $esperado) {
            return true;
        }
        
        // Comparación de equivalencias
        return $this->sonTiposEquivalentes($tipoPredicho, $tipoEsperado);
    }

    /**
     * Obtener el estado de validación IA de un documento
     */
    public function obtenerValidacionIA(Request $request)
    {
        // Los modelos de IA fueron removidos del sistema
        return response()->json([
            'success' => false,
            'mensaje' => 'La validación con IA no está disponible en este momento'
        ], 404);
    }

    /**
     * Genera un mensaje contextual más detallado para la validación IA
     */
    private function generarMensajeContextualIA($esDocumentoCorrecto, $confianza, $tipoPredicho, $tipoEsperado, $validationStatus)
    {
        if ($esDocumentoCorrecto) {
            if ($confianza >= 0.95) {
                return "🎯 ¡EXCELENTE! El documento es exactamente lo que se esperaba. Reconocimiento automático con máxima precisión.";
            } elseif ($confianza >= 0.90) {
                return "✅ MUY BIEN. El documento coincide perfectamente con lo esperado. Alta confianza en el reconocimiento.";
            } elseif ($confianza >= 0.80) {
                return "✅ CORRECTO. El documento parece ser el tipo adecuado. Reconocimiento confiable.";
            } elseif ($confianza >= 0.70) {
                return "✅ ACEPTABLE. El documento parece correcto, aunque con algunas pequeñas dudas en el reconocimiento.";
            } elseif ($confianza >= 0.60) {
                return "⚠️ PROBABLEMENTE CORRECTO. El documento parece ser el adecuado, pero el sistema tiene algunas dudas.";
            } else {
                return "⚠️ POSIBLE COINCIDENCIA. El documento podría ser correcto, pero la calidad del reconocimiento es baja.";
            }
        } else {
            if ($confianza >= 0.90) {
                return "❌ PROBLEMA DETECTADO. El documento subido NO es del tipo correcto. El sistema está muy seguro de que es '{$tipoPredicho}' y no '{$tipoEsperado}'.";
            } elseif ($confianza >= 0.80) {
                return "❌ POSIBLE ERROR. El documento parece ser '{$tipoPredicho}' en lugar de '{$tipoEsperado}'. Verifique que subió el archivo correcto.";
            } elseif ($confianza >= 0.70) {
                return "⚠️ DISCREPANCIA. Hay indicios de que el documento podría ser '{$tipoPredicho}' en lugar de '{$tipoEsperado}'.";
            } elseif ($confianza >= 0.60) {
                return "❓ DUDAS. El sistema no está seguro del tipo de documento. Podría ser '{$tipoPredicho}' o algo diferente.";
            } else {
                return "❓ INCIERTO. No se puede determinar con certeza qué tipo de documento es. La calidad o claridad del archivo podría estar afectando el reconocimiento.";
            }
        }
    }

    /**
     * Determina el color contextual basado en el análisis IA
     */
    private function determinarColorContextual($esDocumentoCorrecto, $confianza, $validationStatus)
    {
        if ($validationStatus === 'auto_approved') {
            return 'text-green-700';
        }
        
        if ($esDocumentoCorrecto) {
            if ($confianza >= 0.85) {
                return 'text-green-600';
            } elseif ($confianza >= 0.70) {
                return 'text-green-500';
            } else {
                return 'text-yellow-600';
            }
        } else {
            if ($confianza >= 0.80) {
                return 'text-red-600';
            } elseif ($confianza >= 0.60) {
                return 'text-orange-600';
            } else {
                return 'text-gray-600';
            }
        }
    }

    /**
     * Determina el nivel de certeza del análisis IA
     */
    private function determinarNivelCerteza($confianza, $esDocumentoCorrecto)
    {
        if ($confianza >= 0.95 && $esDocumentoCorrecto) {
            return 'máxima';
        } elseif ($confianza >= 0.90) {
            return 'muy_alta';
        } elseif ($confianza >= 0.80) {
            return 'alta';
        } elseif ($confianza >= 0.70) {
            return 'media_alta';
        } elseif ($confianza >= 0.60) {
            return 'media';
        } elseif ($confianza >= 0.50) {
            return 'baja';
        } else {
            return 'muy_baja';
        }
    }

    /**
     * Genera recomendaciones basadas en el análisis IA
     */
    private function generarRecomendacionIA($esDocumentoCorrecto, $confianza, $validationStatus)
    {
        if ($validationStatus === 'auto_approved') {
            return "✅ El documento ha sido aprobado automáticamente. No se requiere acción adicional.";
        }
        
        if ($esDocumentoCorrecto) {
            if ($confianza >= 0.90) {
                return "✅ Documento válido. Puede proceder con confianza.";
            } elseif ($confianza >= 0.80) {
                return "✅ Documento parece válido. Recomendamos continuar.";
            } elseif ($confianza >= 0.70) {
                return "⚠️ Documento probablemente válido. Si tiene dudas, puede resubir una copia más clara.";
            } else {
                return "⚠️ Documento posiblemente válido. Considere subir una copia de mejor calidad si es posible.";
            }
        } else {
            if ($confianza >= 0.80) {
                return "❌ IMPORTANTE: Verifique que subió el documento correcto. El sistema detecta un tipo diferente con alta confianza.";
            } elseif ($confianza >= 0.60) {
                return "⚠️ Revise el documento subido. Podría no ser del tipo correcto o la calidad afecta el reconocimiento.";
            } else {
                return "❓ Recomendamos subir una copia más clara del documento o verificar que es del tipo correcto.";
            }
        }
    }

    /**
     * Función mejorada para verificar si el documento es correcto con análisis más detallado
     */
    private function esDocumentoCorrectoMejorado($tipoPredicho, $tipoEsperado, $confianza, $alternativas = [])
    {
        // Si la confianza es muy baja, es incierto
        if ($confianza < 0.4) {
            return null; // null = incierto
        }
        
        // Verificación directa con alta confianza
        if (strtolower($tipoPredicho) === strtolower($tipoEsperado) && $confianza >= 0.6) {
            return true;
        }
        
        // Verificación por equivalencias con confianza decente
        if ($this->sonTiposEquivalentes($tipoPredicho, $tipoEsperado) && $confianza >= 0.6) {
            return true;
        }
        
        // Verificar alternativas si están disponibles
        foreach ($alternativas as $alternativa) {
            if (isset($alternativa['document_type']) && isset($alternativa['confidence'])) {
                $tipoAlternativo = $alternativa['document_type'];
                $confianzaAlternativa = $alternativa['confidence'];
                
                // Si una alternativa coincide con el tipo esperado y tiene buena confianza
                if ($confianzaAlternativa >= 0.7 && 
                    (strtolower($tipoAlternativo) === strtolower($tipoEsperado) ||
                     $this->sonTiposEquivalentes($tipoAlternativo, $tipoEsperado))) {
                    return true;
                }
            }
        }
        
        // Si llegamos aquí y la confianza es alta, probablemente es incorrecto
        if ($confianza >= 0.7) {
            return false;
        }
        
        // En casos de confianza media-baja, es incierto
        return null;
    }

    /**
     * ✅ MOSTRAR SELECTOR DE SECCIONES PARA ACTUALIZACIÓN
     */
    public function mostrarSelectorActualizacion()
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No se encontró información del solicitante');
        }

        // Verificar que sea proveedor activo
        $proveedor = Proveedor::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Activo')
            ->first();
            
        if (!$proveedor) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No tiene un registro de proveedor activo');
        }

        // Buscar el trámite aprobado que lo hizo proveedor
        $tramiteAprobado = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Aprobado')
            ->orderBy('fecha_revision', 'desc')
            ->first();
            
        if (!$tramiteAprobado) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No se encontró el trámite base para la actualización');
        }

        // Obtener datos actuales para mostrar en el selector
        $datosActuales = $this->obtenerDatosActualesProveedor($tramiteAprobado);

        Log::info('Mostrando selector de secciones para actualización:', [
            'user_id' => $user->id,
            'proveedor_pv' => $proveedor->pv,
            'tramite_base_id' => $tramiteAprobado->id,
            'tipo_persona' => $tramiteAprobado->solicitante->tipo_persona
        ]);

        return view('tramites.actualizacion.selector', compact(
            'proveedor', 
            'tramiteAprobado', 
            'datosActuales'
        ));
    }

    /**
     * ✅ INICIAR ACTUALIZACIÓN DE SECCIÓN ESPECÍFICA
     */
    public function iniciarActualizacionSeccion(Request $request, $seccionId)
    {
        $request->validate([
            'tramite_base_id' => 'required|exists:tramite,id'
        ]);

        $user = Auth::user();
        $tramiteBase = Tramite::find($request->tramite_base_id);
        
        // Verificar permisos
        if ($tramiteBase->solicitante->usuario_id !== $user->id) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No tiene permisos para este trámite');
        }

        try {
            DB::beginTransaction();

            // Crear nuevo trámite de actualización parcial
            $tramiteActualizacion = Tramite::create([
                'solicitante_id' => $tramiteBase->solicitante_id,
                'tipo_tramite' => 'Actualizacion',
                'estado' => 'Pendiente',
                'progreso_tramite' => 0,
                'fecha_inicio' => now(),
                'observaciones' => "Actualización parcial - Sección {$seccionId} | Trámite base: {$tramiteBase->id}"
            ]);

            // ✅ Pre-cargar TODOS los datos del trámite aprobado
            $this->precargarDatosProveedor($tramiteBase, $tramiteActualizacion);

            DB::commit();

            Log::info('Trámite de actualización parcial creado:', [
                'tramite_nuevo_id' => $tramiteActualizacion->id,
                'tramite_base_id' => $tramiteBase->id,
                'seccion_actualizar' => $seccionId,
                'solicitante_id' => $tramiteBase->solicitante_id
            ]);

            // Redirigir al formulario de la sección específica
            return redirect()->route('tramites.create.tipo', [
                'tipo_tramite' => 'actualizacion',
                'tramite' => $tramiteActualizacion->id
            ])->with([
                'success' => '✅ Datos cargados desde su registro de proveedor. Modifique solo la información que necesita actualizar.',
                'seccion_focus' => $seccionId,
                'es_actualizacion_parcial' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al iniciar actualización parcial:', [
                'tramite_base_id' => $request->tramite_base_id,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al iniciar la actualización: ' . $e->getMessage());
        }
    }

    /**
     * ✅ PRE-CARGAR DATOS DEL TRÁMITE APROBADO AL NUEVO TRÁMITE
     */
    private function precargarDatosProveedor($tramiteBase, $tramiteNuevo)
    {
        // Copiar DetalleTramite
        if ($tramiteBase->detalleTramite) {
            $detalleOriginal = $tramiteBase->detalleTramite->toArray();
            unset($detalleOriginal['id'], $detalleOriginal['tramite_id'], $detalleOriginal['created_at'], $detalleOriginal['updated_at']);
            $detalleOriginal['tramite_id'] = $tramiteNuevo->id;
            
            \App\Models\DetalleTramite::create($detalleOriginal);
        }

        // Copiar dirección
        if ($tramiteBase->direccion) {
            $direccionOriginal = $tramiteBase->direccion->toArray();
            unset($direccionOriginal['id'], $direccionOriginal['tramite_id'], $direccionOriginal['created_at'], $direccionOriginal['updated_at']);
            $direccionOriginal['tramite_id'] = $tramiteNuevo->id;
            
            \App\Models\Direccion::create($direccionOriginal);
        }

        // Copiar datos de constitución si existen
        if ($tramiteBase->datosConstitutivo) {
            $constitutionOriginal = $tramiteBase->datosConstitutivo->toArray();
            unset($constitutionOriginal['id'], $constitutionOriginal['tramite_id'], $constitutionOriginal['created_at'], $constitutionOriginal['updated_at']);
            $constitutionOriginal['tramite_id'] = $tramiteNuevo->id;
            
            \App\Models\DatosConstitutivo::create($constitutionOriginal);
        }

        // Copiar accionistas si existen
        if ($tramiteBase->accionistasSolicitante) {
            foreach ($tramiteBase->accionistasSolicitante as $accionista) {
                $accionistaData = $accionista->toArray();
                unset($accionistaData['id'], $accionistaData['tramite_id'], $accionistaData['created_at'], $accionistaData['updated_at']);
                $accionistaData['tramite_id'] = $tramiteNuevo->id;
                
                \App\Models\AccionistaSolicitante::create($accionistaData);
            }
        }

        // Copiar representante legal si existe
        if ($tramiteBase->representanteLegal) {
            $representanteData = $tramiteBase->representanteLegal->toArray();
            unset($representanteData['id'], $representanteData['tramite_id'], $representanteData['created_at'], $representanteData['updated_at']);
            $representanteData['tramite_id'] = $tramiteNuevo->id;
            
            \App\Models\RepresentanteLegal::create($representanteData);
        }

        // Copiar contacto
        if ($tramiteBase->contactoSolicitante) {
            $contactoData = $tramiteBase->contactoSolicitante->toArray();
            unset($contactoData['id'], $contactoData['tramite_id'], $contactoData['created_at'], $contactoData['updated_at']);
            $contactoData['tramite_id'] = $tramiteNuevo->id;
            
            \App\Models\ContactoSolicitante::create($contactoData);
        }

        Log::info('Datos pre-cargados en trámite de actualización:', [
            'tramite_base_id' => $tramiteBase->id,
            'tramite_nuevo_id' => $tramiteNuevo->id,
            'datos_copiados' => 'detalle_tramite, direccion, constitucion, accionistas, representante, contacto'
        ]);
    }

    /**
     * ✅ OBTENER DATOS ACTUALES PARA MOSTRAR EN EL SELECTOR
     */
    private function obtenerDatosActualesProveedor($tramite)
    {
        $datos = [];

        // Datos generales
        if ($tramite->detalleTramite) {
            $datos['datos_generales'] = [
                'razon_social' => $tramite->solicitante->razon_social ?? 'N/A',
                'giro' => $tramite->detalleTramite->giro ?? 'N/A',
                'actividad_preponderante' => $tramite->detalleTramite->actividad_preponderante ?? 'N/A'
            ];
        }

        // Domicilio
        if ($tramite->direccion) {
            $datos['domicilio'] = [
                'estado_nombre' => $tramite->direccion->estado->nombre ?? 'N/A',
                'municipio_nombre' => $tramite->direccion->municipio->nombre ?? 'N/A',
                'direccion_completa' => ($tramite->direccion->calle ?? '') . ' ' . ($tramite->direccion->numero_exterior ?? '')
            ];
        }

        // Constitución (solo para persona moral)
        if ($tramite->datosConstitutivo) {
            $datos['constitucion'] = [
                'numero_notario' => $tramite->datosConstitutivo->numero_notario ?? 'N/A',
                'fecha_constitucion' => $tramite->datosConstitutivo->fecha_constitucion ?? 'N/A'
            ];
        }

        // Accionistas
        $datos['accionistas'] = $tramite->accionistasSolicitante ? $tramite->accionistasSolicitante->toArray() : [];

        // Apoderado
        if ($tramite->representanteLegal) {
            $datos['apoderado'] = [
                'nombre' => $tramite->representanteLegal->nombre ?? 'N/A',
                'cargo' => $tramite->representanteLegal->cargo ?? 'N/A'
            ];
        }

        // Documentos
        $datos['documentos'] = $tramite->documentosSolicitante ? $tramite->documentosSolicitante->toArray() : [];

        return $datos;
    }

    /**
     * Cancela un trámite y registra el motivo
     */
    public function cancelar(Request $request, Tramite $tramite)
    {
        // Validar que el trámite pueda ser cancelado
        if (!in_array($tramite->estado, [
            Tramite::ESTADOS['Pendiente'],
            Tramite::ESTADOS['En Revision'],
            Tramite::ESTADOS['Por Cotejar']
        ])) {
            return redirect()->back()
                ->with('error', 'Este trámite no puede ser cancelado en su estado actual.');
        }

        // Obtener el motivo de la cancelación
        $motivo = $request->get('motivo', 'No especificado');

        // Cancelar el trámite
        $tramite->cancelar($motivo);

        // Crear notificación para el usuario
        if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
            \App\Models\Notificacion::crearParaUsuario(
                'Trámite Cancelado',
                "Su trámite #{$tramite->id} ha sido cancelado. Motivo: {$motivo}",
                'Error',
                $tramite->solicitante->usuario_id
            );
        }

        // Registrar en el log del sistema
        \App\Services\SystemLogService::registrarAccion(
            'Trámite Cancelado',
            'El trámite #' . $tramite->id . ' fue cancelado. Motivo: ' . $motivo,
            $tramite->id,
            'tramites'
        );

        return redirect()->route('mis-tramites.index')
            ->with('warning', 'El trámite ha sido cancelado. Motivo: ' . $motivo);
    }
}
