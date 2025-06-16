<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Http\Controllers\Formularios\DatosGeneralesController;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\AccionistasController;
use App\Http\Controllers\Formularios\ApoderadoLegalController;
use App\Http\Controllers\Formularios\DocumentosController;
use App\Http\Controllers\TramiteSolicitanteController;

class RevisionController extends Controller
{
    /**
     * Mostrar la lista de trámites pendientes de revisión
     */
    public function index()
    {
        // Obtener trámites pendientes de revisión con información del solicitante
        $tramites = Tramite::with(['solicitante', 'revisor'])
            ->whereIn('estado', ['Pendiente', 'En Revision', 'Por Cotejar'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('revision.index', compact('tramites'));
    }

    /**
     * Mostrar la vista de revisión de un trámite específico
     */
    public function show(Tramite $tramite)
    {
        try {
            Log::info('=== INICIO REVISIÓN DE TRÁMITE ===', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'estado' => $tramite->estado,
                'progreso' => $tramite->progreso_tramite
            ]);

            // Cargar relaciones necesarias
            $tramite->load(['solicitante', 'revisor', 'detalleTramite']);
            
            // 1. Obtener datos generales
            $datosTramite = $this->obtenerDatosGenerales($tramite);
            
            // 2. Obtener datos de domicilio
            $datosDomicilio = $this->obtenerDatosDomicilio($tramite);
            
            // 3. Obtener datos SAT (si existen)
            $datosSAT = $this->obtenerDatosSAT($tramite);
            
            // 4. Obtener documentos
            $documentos = $this->obtenerDocumentos($tramite);
            
            // 5. Para persona moral: obtener datos adicionales
            $constitucion = null;
            $accionistas = [];
            $apoderado = null;
            
            if ($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral') {
                $constitucion = $this->obtenerDatosConstitucion($tramite);
                $accionistas = $this->obtenerDatosAccionistas($tramite);
                $apoderado = $this->obtenerDatosApoderado($tramite);
            }

            Log::info('✅ Datos cargados exitosamente para revisión', [
                'tramite_id' => $tramite->id,
                'tiene_datos_generales' => !empty($datosTramite),
                'tiene_domicilio' => !empty($datosDomicilio),
                'tiene_documentos' => count($documentos),
                'es_persona_moral' => $tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral',
                'tipo_persona' => $tramite->solicitante->tipo_persona ?? 'No definido'
            ]);

            return view('revision.show', compact(
                'tramite',
                'datosTramite',
                'datosDomicilio', 
                'datosSAT',
                'documentos',
                'accionistas',
                'apoderado',
                'constitucion'
            ));

        } catch (\Exception $e) {
            Log::error('❌ Error al cargar datos para revisión:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En caso de error, mostrar vista con datos vacíos
            return view('revision.show', [
                'tramite' => $tramite,
                'datosTramite' => [],
                'datosDomicilio' => [],
                'datosSAT' => [],
                'documentos' => [],
                'accionistas' => [],
                'apoderado' => null,
                'constitucion' => null,
                'error' => 'Error al cargar los datos del trámite'
            ]);
        }
    }

    /**
     * Obtener datos generales del trámite
     */
    private function obtenerDatosGenerales(Tramite $tramite)
    {
        try {
            $controller = new DatosGeneralesController();
            return $controller->obtenerDatos($tramite);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos generales:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener datos de domicilio del trámite
     */
    private function obtenerDatosDomicilio(Tramite $tramite)
    {
        try {
            $controller = new DomicilioController();
            return $controller->obtenerDatos($tramite);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de domicilio:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener datos SAT del trámite
     */
    private function obtenerDatosSAT(Tramite $tramite)
    {
        try {
            // Usar el controlador de trámite solicitante para obtener datos SAT
            $controller = new TramiteSolicitanteController();
            $datosDomicilio = $controller->obtenerDatosDomicilio($tramite);
            
            // Extraer solo los datos SAT si existen
            return $datosDomicilio['datos_sat'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error al obtener datos SAT:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener documentos del trámite
     */
    private function obtenerDocumentos(Tramite $tramite)
    {
        try {
            // Simular request para obtener documentos
            $request = new Request();
            $controller = new DocumentosController();
            
            // Obtener documentos usando el método get del controlador
            $response = $controller->get($request, $tramite->id);
            $responseData = $response->getData(true);
            
            return $responseData['success'] ? $responseData['documentos'] : [];
        } catch (\Exception $e) {
            Log::error('Error al obtener documentos:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener datos de constitución (solo para persona moral)
     */
    private function obtenerDatosConstitucion(Tramite $tramite)
    {
        try {
            $controller = new ConstitucionController();
            return $controller->getIncorporationData($tramite);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de constitución:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtener datos de accionistas (solo para persona moral)
     */
    private function obtenerDatosAccionistas(Tramite $tramite)
    {
        try {
            $controller = new AccionistasController();
            return $controller->getShareholdersData($tramite);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de accionistas:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener datos del apoderado legal (solo para persona moral)
     */
    private function obtenerDatosApoderado(Tramite $tramite)
    {
        try {
            $controller = new ApoderadoLegalController();
            return $controller->getDatosApoderadoLegal($tramite);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos del apoderado legal:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
} 