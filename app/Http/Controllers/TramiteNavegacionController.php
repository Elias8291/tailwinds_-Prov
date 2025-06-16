<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Formularios\DatosGeneralesController;
use App\Http\Controllers\Formularios\DomicilioController;

class TramiteNavegacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra un paso específico del trámite
     */
    public function mostrarPaso($tramiteId, $paso)
    {
        try {
            Log::info('Mostrando paso del trámite', [
                'tramite_id' => $tramiteId,
                'paso' => $paso,
                'user_id' => Auth::id(),
                'session_id' => session()->getId()
            ]);

            $tramite = Tramite::with('solicitante', 'detalle.contacto')->findOrFail($tramiteId);
            $solicitante = $tramite->solicitante;
            
            // Verificar que el usuario tenga acceso al trámite
            $user = Auth::user();
            if (!$user) {
                Log::error('Usuario no autenticado al mostrar paso');
                return redirect()->route('login')->with('error', 'Sesión expirada');
            }
            
            $userSolicitante = $user->solicitante;
            if (!$userSolicitante || $userSolicitante->id !== $solicitante->id) {
                Log::error('Usuario sin acceso al trámite', [
                    'user_id' => $user->id,
                    'user_solicitante_id' => $userSolicitante?->id,
                    'tramite_solicitante_id' => $solicitante->id
                ]);
                return redirect()->route('tramites.solicitante.index')
                    ->with('error', 'No tiene permisos para acceder a este trámite');
            }
            
            // Determinar el número total de pasos según el tipo de persona
            $totalPasos = $this->getTotalPasos($solicitante->tipo_persona);
            
            // Validar que el paso solicitado sea válido
            if ($paso < 1 || $paso > $totalPasos) {
                return redirect()->route('tramites.create.tipo', [
                    'tipo_tramite' => strtolower($tramite->tipo_tramite),
                    'tramite' => $tramiteId
                ])->with('warning', 'Paso no válido, redirigido al primer paso');
            }
            
            // Verificar si puede acceder a este paso (no puede saltar pasos)
            if ($paso > $tramite->progreso_tramite + 1) {
                return redirect()->route('tramites.create.tipo', [
                    'tipo_tramite' => strtolower($tramite->tipo_tramite),
                    'tramite' => $tramiteId
                ])->with('warning', 'Debe completar los pasos anteriores primero');
            }
            
            // Preparar datos para la vista según el paso
            $datosVista = $this->prepararDatosPaso($tramite, $paso);
            
            return view('tramites.solicitante.index', array_merge($datosVista, [
                'tramite' => $tramite,
                'solicitante' => $solicitante,
                'paso_actual' => $paso,
                'total_pasos' => $totalPasos,
                'puede_continuar' => $this->puedeAvanzarAlSiguientePaso($tramite, $paso),
                'puede_regresar' => $paso > 1,
                // Variables para mantener compatibilidad con la vista original
                'tipoTramite' => [],
                'tramiteEnProgreso' => null,
                'datosDomicilio' => isset($datosVista['datosDomicilio']) ? $datosVista['datosDomicilio'] : []
            ]));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar paso del trámite:', [
                'tramite_id' => $tramiteId,
                'paso' => $paso,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'Error al cargar el paso del trámite');
        }
    }
    
    /**
     * Navega al siguiente paso (con guardado automático si es POST)
     */
    public function siguientePaso($tramiteId, $pasoActual)
    {
        try {
            $tramite = Tramite::findOrFail($tramiteId);
            $totalPasos = $this->getTotalPasos($tramite->solicitante->tipo_persona);
            
            Log::info('Navegando al siguiente paso', [
                'tramite_id' => $tramiteId,
                'paso_actual' => $pasoActual,
                'method' => request()->method(),
                'has_form_data' => request()->isMethod('post')
            ]);
            
            // Si es una petición POST (viene del formulario), guardar datos primero
            if (request()->isMethod('post')) {
                $this->guardarDatosPaso($tramite, $pasoActual);
            }
            
            $siguientePaso = min($pasoActual + 1, $totalPasos);
            
            // Actualizar progreso del trámite
            if ($siguientePaso > $tramite->progreso_tramite) {
                $tramite->progreso_tramite = $siguientePaso;
                $tramite->save();
            }
            
            // Redirigir siempre a la vista create manteniendo la misma URL
            return redirect()->route('tramites.create.tipo', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramiteId
            ])->with('success', 'Datos guardados correctamente.');
            
        } catch (\Exception $e) {
            Log::error('Error al navegar al siguiente paso', [
                'tramite_id' => $tramiteId,
                'paso_actual' => $pasoActual,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al guardar los datos: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Navega al paso anterior
     */
    public function pasoAnterior($tramiteId, $pasoActual)
    {
        $tramite = Tramite::findOrFail($tramiteId);
        
        // Redirigir siempre a la vista create manteniendo la misma URL
        return redirect()->route('tramites.create.tipo', [
            'tipo_tramite' => strtolower($tramite->tipo_tramite),
            'tramite' => $tramiteId
        ])->with('info', 'Navegando al paso anterior.');
    }
    
    /**
     * Actualiza el progreso del trámite
     */
    public function actualizarProgreso($tramiteId, $paso)
    {
        $tramite = Tramite::findOrFail($tramiteId);
        
        // Solo actualizar si el paso es mayor al progreso actual
        if ($paso > $tramite->progreso_tramite) {
            $tramite->progreso_tramite = $paso;
            $tramite->save();
        }
        
        return $tramite;
    }
    
    /**
     * Determina el total de pasos según el tipo de persona
     */
    private function getTotalPasos($tipoPersona)
    {
        return $tipoPersona === 'Física' ? 3 : 6;
    }
    
    /**
     * Prepara los datos específicos para cada paso
     */
    private function prepararDatosPaso($tramite, $paso)
    {
        $datos = [];
        
        switch ($paso) {
            case 1: // Datos Generales
                $controller = new DatosGeneralesController();
                $datos['datosTramite'] = $controller->obtenerDatos($tramite);
                $datos['datosSolicitante'] = [
                    'rfc' => $tramite->solicitante->rfc,
                    'tipo_persona' => $tramite->solicitante->tipo_persona,
                    'curp' => $tramite->solicitante->curp,
                    'razon_social' => $tramite->solicitante->razon_social,
                ];
                
                // Obtener datos de domicilio para mostrar en datos generales
                $domicilioController = new DomicilioController();
                $datosDomicilioCompletos = $domicilioController->obtenerDatos($tramite);
                $datos['datosDomicilio'] = $datosDomicilioCompletos;
                
                $datos['paso_componente'] = 'datos-generales';
                break;
                
            case 2: // Domicilio
                $controller = new DomicilioController();
                $datosDomicilio = $controller->obtenerDatos($tramite);
                $datos['datosDomicilio'] = $datosDomicilio;
                $datos['paso_componente'] = 'domicilio';

                break;
                
            case 3: // Documentos (Física) o Constitución (Moral)
                if ($tramite->solicitante->tipo_persona === 'Física') {
                    $datos['paso_componente'] = 'documentos';
                } else {
                    $datos['paso_componente'] = 'constitucion';
                }
                break;
                
            case 4: // Accionistas (solo Moral)
                $datos['paso_componente'] = 'accionistas';
                break;
                
            case 5: // Apoderado Legal (solo Moral)
                $datos['paso_componente'] = 'apoderado';
                break;
                
            case 6: // Documentos (solo Moral)
                $datos['paso_componente'] = 'documentos';
                break;
        }
        
        return $datos;
    }
    
    /**
     * Guarda los datos del paso actual
     */
    private function guardarDatosPaso($tramite, $paso)
    {
        switch ($paso) {
            case 1: // Datos Generales
                $controller = new DatosGeneralesController();
                $request = request();
                $request->merge(['action' => 'next']);
                $result = $controller->guardar($request);
                return $result;
                
                         case 2: // Domicilio
                 $controller = new DomicilioController();
                 $request = request();
                 $result = $controller->guardar($request, $tramite);
                 return $result;
                
            case 3: // Documentos o Constitución
                // Implementar según el tipo de persona
                if ($tramite->solicitante->tipo_persona === 'Física') {
                    // Guardar documentos para persona física
                    return $this->guardarDocumentosPersonaFisica($tramite);
                } else {
                    // Guardar datos de constitución para persona moral
                    return $this->guardarConstitucion($tramite);
                }
                
            case 4: // Accionistas (solo Moral)
                return $this->guardarAccionistas($tramite);
                
            case 5: // Apoderado Legal (solo Moral)
                return $this->guardarApoderado($tramite);
                
            case 6: // Documentos (solo Moral)
                return $this->guardarDocumentosPersonaMoral($tramite);
                
            default:
                Log::warning('Paso no implementado para guardado', ['paso' => $paso]);
                return true;
        }
    }
    
    /**
     * Métodos placeholder para los pasos no implementados aún
     */
    private function guardarDocumentosPersonaFisica($tramite)
    {
        // TODO: Implementar guardado de documentos para persona física
        Log::info('Guardando documentos persona física - pendiente implementación');
        return true;
    }
    
    private function guardarConstitucion($tramite)
    {
        // TODO: Implementar guardado de constitución
        Log::info('Guardando constitución - pendiente implementación');
        return true;
    }
    
    private function guardarAccionistas($tramite)
    {
        // TODO: Implementar guardado de accionistas
        Log::info('Guardando accionistas - pendiente implementación');
        return true;
    }
    
    private function guardarApoderado($tramite)
    {
        // TODO: Implementar guardado de apoderado
        Log::info('Guardando apoderado - pendiente implementación');
        return true;
    }
    
    private function guardarDocumentosPersonaMoral($tramite)
    {
        // TODO: Implementar guardado de documentos para persona moral
        Log::info('Guardando documentos persona moral - pendiente implementación');
        return true;
    }

    /**
     * Verifica si puede avanzar al siguiente paso
     */
    private function puedeAvanzarAlSiguientePaso($tramite, $pasoActual)
    {
        // Lógica para determinar si el paso actual está completo
        switch ($pasoActual) {
            case 1: // Datos Generales
                return $tramite->detalle && 
                       $tramite->detalle->giro && 
                       $tramite->detalle->contacto;
                       
            case 2: // Domicilio
                return $tramite->detalle && 
                       $tramite->detalle->direccion_id;
                       
            case 3: // Documentos o Constitución
                if ($tramite->solicitante->tipo_persona === 'Física') {
                    return $tramite->documentos()->count() > 0;
                } else {
                    // Lógica para verificar constitución
                    return true; // Por ahora siempre verdadero
                }
                
            default:
                return true;
        }
    }
} 