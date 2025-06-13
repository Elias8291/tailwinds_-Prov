<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\Documento;
use Carbon\Carbon;

class TramiteSolicitanteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tipoTramite = $this->determinarTipoTramite($user);
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        return view('tramites.solicitante.index', compact('tipoTramite', 'user', 'tramiteEnProgreso'));
    }

    private function determinarTipoTramite($user)
    {
        // Simulando lógica de negocio - ajustar según tus modelos reales
        
        // Si no tiene proveedor o su PV ya venció: INSCRIPCIÓN
        if (!$user->proveedor || $this->proveedorVencido($user)) {
            return [
                'inscripcion' => true,
                'renovacion' => false,
                'actualizacion' => false
            ];
        }
        
        // Si está cerca de vencer (7 días): RENOVACIÓN
        if ($this->proveedorProximoAVencer($user)) {
            return [
                'inscripcion' => false,
                'renovacion' => true,
                'actualizacion' => false
            ];
        }
        
        // Si ya es proveedor y está vigente: ACTUALIZACIÓN
        return [
            'inscripcion' => false,
            'renovacion' => false,
            'actualizacion' => true
        ];
    }

    private function proveedorVencido($user)
    {
        // Lógica para verificar si el proveedor está vencido
        if (!$user->proveedor) return true;
        
        // Asumiendo que hay un campo fecha_vencimiento en el modelo proveedor
        // return $user->proveedor->fecha_vencimiento < Carbon::now();
        return false; // Temporal
    }

    private function proveedorProximoAVencer($user)
    {
        // Lógica para verificar si está próximo a vencer (7 días)
        if (!$user->proveedor) return false;
        
        // Asumiendo que hay un campo fecha_vencimiento en el modelo proveedor
        // return $user->proveedor->fecha_vencimiento <= Carbon::now()->addDays(7);
        return false; // Temporal
    }

    private function verificarTramiteEnProgreso($user)
    {
        // Verificar si el usuario tiene un trámite en progreso
        // Asumiendo que existe un modelo Tramite relacionado con el usuario
        // return $user->tramites()->where('estado', 'en_progreso')->first();
        
        // Simulación para propósitos de demostración
        // En una implementación real, esto vendría de la base de datos
        
        // Simulamos un trámite en progreso (descomenta para probar)
        // return (object) [
        //     'id' => 1,
        //     'tipo_tramite' => 'inscripcion',
        //     'tipo_persona' => 'Física',
        //     'rfc' => 'XAXX010101000',
        //     'paso_actual' => 2,
        //     'estado' => 'en_progreso'
        // ];
        
        return null;
    }

    public function iniciarInscripcion(Request $request)
    {
        $user = Auth::user();
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        if ($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'inscripcion') {
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        // Crear nuevo trámite de inscripción
        return $this->crearNuevoTramite('inscripcion', $user);
    }

    public function iniciarRenovacion(Request $request)
    {
        $user = Auth::user();
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        if ($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'renovacion') {
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        // Crear nuevo trámite de renovación
        return $this->crearNuevoTramite('renovacion', $user);
    }

    public function iniciarActualizacion(Request $request)
    {
        $user = Auth::user();
        $tramiteEnProgreso = $this->verificarTramiteEnProgreso($user);
        
        if ($tramiteEnProgreso && $tramiteEnProgreso->tipo_tramite === 'actualizacion') {
            // Continuar trámite existente
            return $this->continuarTramite($tramiteEnProgreso);
        }
        
        // Crear nuevo trámite de actualización
        return $this->crearNuevoTramite('actualizacion', $user);
    }

    private function continuarTramite($tramite)
    {
        // Preparar datos del trámite existente para continuar
        $datosTramite = [
            'tramite_id' => $tramite->id,
            'tipo_tramite' => $tramite->tipo_tramite,
            'tipo_persona' => $tramite->tipo_persona ?? 'Física',
            'rfc' => $tramite->rfc ?? '',
            'paso_inicial' => $tramite->paso_actual ?? 1,
            'datos_existentes' => [
                'direccion' => $tramite->direccion ?? null,
                'datos_generales' => $tramite->datos_generales ?? null,
            ]
        ];
        
        return view('tramites.create', compact('datosTramite'));
    }

    private function crearNuevoTramite($tipoTramite, $user)
    {
        // Datos básicos para la vista - los datos reales se cargarán via AJAX
        $datosTramite = [
            'tramite_id' => null,
            'tipo_tramite' => $tipoTramite,
            'tipo_persona' => 'Física',
            'rfc' => '',
            'paso_inicial' => 1,
            'datos_existentes' => []
        ];
        
        return view('tramites.create', compact('datosTramite'));
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
                'tipo_persona' => $solicitante->tipo_persona ?? 'Física',
                'rfc' => $solicitante->rfc ?? '',
                'curp' => $solicitante->curp ?? '',
                'tramite_id' => null,
                'tipo_tramite' => 'inscripcion'
            ];

            if ($tramite) {
                $datosTramite = [
                    'paso_inicial' => $tramite->progreso_tramite ?? 1,
                    'tipo_persona' => $solicitante->tipo_persona,
                    'rfc' => $solicitante->rfc,
                    'curp' => $solicitante->curp,
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

            // Obtener documentos según el tipo de persona
            $documentos = Documento::where(function($query) use ($tipoPersona) {
                $query->where('tipo_persona', $tipoPersona)
                      ->orWhere('tipo_persona', 'Ambas');
            })
            ->where('es_visible', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'nombre', 'descripcion', 'tipo_persona']);

            return response()->json([
                'success' => true,
                'documentos' => $documentos,
                'tipo_persona' => $tipoPersona
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
                'documento' => 'required|file|mimes:pdf|max:10240', // 10MB máximo
                'documento_id' => 'required|integer|exists:documento,id'
            ]);

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

            $file = $request->file('documento');
            $documentoId = $request->documento_id;

            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid('doc_' . $documentoId . '_') . '.' . $extension;
            
            // Almacenar archivo
            $ruta = $file->storeAs('documentos_tramite/' . $tramite->id, $nombreArchivo, 'public');

            // Crear o actualizar el registro del documento
            \App\Models\DocumentoSolicitante::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'documento_id' => $documentoId
                ],
                [
                    'fecha_entrega' => now(),
                    'estado' => 'Pendiente',
                    'version_documento' => 1,
                    'ruta_archivo' => $ruta,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Documento subido correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el documento: ' . $e->getMessage()
            ], 500);
        }
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
                'estado' => 'Enviado',
                'fecha_finalizacion' => now(),
                'progreso_tramite' => $solicitante->tipo_persona === 'Física' ? 3 : 6
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trámite finalizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar el trámite: ' . $e->getMessage()
            ], 500);
        }
    }
}
