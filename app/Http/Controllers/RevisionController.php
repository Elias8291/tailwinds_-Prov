<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\SeccionRevision;
use App\Models\Proveedor;
use App\Models\DocumentoSolicitante;
use App\Models\Documento;
use App\Models\Notificacion;
use App\Models\Cita;
use App\Http\Controllers\Formularios\DatosGeneralesController;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\AccionistasController;
use App\Http\Controllers\Formularios\ApoderadoLegalController;
use App\Http\Controllers\Formularios\DocumentosController;
use App\Http\Controllers\TramiteSolicitanteController;
use Illuminate\Http\JsonResponse;
use App\Events\SolicitudCorreccionesEvent;
use App\Models\Seccion;

class RevisionController extends Controller
{
    /**
     * Mostrar la lista de trámites pendientes de revisión
     */
    public function index(Request $request)
    {
        // Obtener trámites con información del solicitante
        $query = Tramite::with(['solicitante', 'revisor']);

        // Excluir trámites aprobados por defecto (solo mostrar si se filtra específicamente por "Aprobado")
        if (!$request->filled('estado') || $request->estado !== 'Aprobado') {
            $query->where('estado', '!=', 'Aprobado');
        }

        // Aplicar filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_tramite')) {
            $query->where('tipo_tramite', $request->tipo_tramite);
        }

        if ($request->filled('rfc')) {
            $query->whereHas('solicitante', function($q) use ($request) {
                $q->where('rfc', 'like', '%' . $request->rfc . '%');
            });
        }

        // Filtro por tiempo de revisión usando fecha_finalizacion
        if ($request->filled('tiempo_revision')) {
            $fecha = now();
            switch ($request->tiempo_revision) {
                case 'hoy':
                    $query->whereDate('fecha_finalizacion', $fecha);
                    break;
                case 'semana':
                    $query->whereBetween('fecha_finalizacion', [
                        $fecha->copy()->startOfWeek(),
                        $fecha->copy()->endOfWeek()
                    ]);
                    break;
                case 'mes':
                    $query->whereBetween('fecha_finalizacion', [
                        $fecha->copy()->startOfMonth(),
                        $fecha->copy()->endOfMonth()
                    ]);
                    break;
                case 'todos':
                    // No aplicar filtro de fecha
                    break;
            }
        } else {
            // Por defecto, mostrar solo trámites finalizados
            $query->whereNotNull('fecha_finalizacion');
        }

        // Ordenamiento por fecha de finalización
        $query->orderBy('fecha_finalizacion', 'desc');

        $tramites = $query->paginate(15);

        return view('revision.index', compact('tramites'));
    }

    /**
     * Mostrar la vista de revisión de un trámite específico
     */
    public function show(Request $request, Tramite $tramite)
    {
        // Obtener el tipo de revisión desde la request (por defecto 'digital')
        $tipo_revision = $request->get('tipo_revision', 'digital');
        
        try {
            
            Log::info('=== INICIO REVISIÓN DE TRÁMITE ===', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'estado' => $tramite->estado,
                'progreso' => $tramite->progreso_tramite,
                'tipo_revision' => $tipo_revision
            ]);

            // Cargar relaciones necesarias
            $tramite->load(['solicitante', 'revisor', 'detalleTramite', 'seccionesRevision.seccion']);
            
            // 1. Obtener datos generales usando el controlador
            $datosGenerales = $this->obtenerDatosGenerales($tramite);
            
            // Separar datos generales en las variables que esperan los componentes
            $datosTramite = $datosGenerales;
            $datosSolicitante = [
                'tipo_persona' => $tramite->solicitante->tipo_persona ?? '',
                'rfc' => $tramite->solicitante->rfc ?? '',
                'curp' => $tramite->solicitante->curp ?? '',
                'nombre_completo' => $tramite->solicitante->nombre_completo ?? '',
                'razon_social' => $tramite->solicitante->razon_social ?? ''
            ];
            
            // 2. Obtener datos de domicilio usando el controlador
            $datosDomicilio = $this->obtenerDatosDomicilio($tramite);
            
            // 3. Obtener datos SAT (si existen)
            $datosSAT = $this->obtenerDatosSAT($tramite);
            
            // 4. Obtener documentos usando el controlador
            $documentos = $this->obtenerDocumentos($tramite);
            
            // 4.1. Obtener documentos agrupados por sección
            $documentosPorSeccion = $this->obtenerDocumentosPorSeccion($tramite);
            
            // 5. Para persona moral: obtener datos adicionales usando los controladores
            $datosConstitucion = null;
            $datosAccionistas = [];
            $accionistas = [];
            $datosApoderado = null;
            
            if ($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral') {
                $datosConstitucion = $this->obtenerDatosConstitucion($tramite);
                $accionistasData = $this->obtenerDatosAccionistas($tramite);
                $datosAccionistas = $accionistasData;
                $accionistas = $accionistasData;
                $datosApoderado = $this->obtenerDatosApoderado($tramite);
            }

            Log::info('✅ Datos cargados exitosamente para revisión', [
                'tramite_id' => $tramite->id,
                'tiene_datos_generales' => !empty($datosTramite),
                'tiene_datos_solicitante' => !empty($datosSolicitante),
                'tiene_domicilio' => !empty($datosDomicilio),
                'tiene_documentos' => count($documentos),
                'es_persona_moral' => $tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral',
                'tipo_persona' => $tramite->solicitante->tipo_persona ?? 'No definido'
            ]);

            // 6. Obtener revisiones existentes
            $revisionesExistentes = $tramite->seccionesRevision->mapWithKeys(function ($revision) {
                return [$revision->seccion_id => [
                    'estado' => $revision->estado,
                    'comentario' => $revision->comentario,
                    'revisor' => $revision->revisor->name ?? 'N/A',
                    'fecha' => $revision->updated_at->format('d/m/Y H:i')
                ]];
            });

            // 7. Obtener comentarios generales del trámite
            $comentariosGenerales = $this->obtenerComentariosGenerales($tramite);

            // 8. Verificar si existe una cita de cotejo para este trámite
            $citaCotejo = \App\Models\Cita::where('tramite_id', $tramite->id)
                ->where('motivo', 'like', '%Cotejo físico%')
                ->first();

            // 9. Verificar si todas las secciones están revisadas (aprobadas)
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $seccionesRequeridas = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 3];
            
            $seccionesAprobadas = $tramite->seccionesRevision()
                ->whereIn('seccion_id', $seccionesRequeridas)
                ->where('estado', 'aprobado')
                ->count();
            
            $todasSeccionesAprobadas = $seccionesAprobadas === count($seccionesRequeridas);

            return view('revision.show', compact(
                'tramite',
                'datosTramite',
                'datosSolicitante',
                'datosDomicilio', 
                'datosSAT',
                'documentos',
                'documentosPorSeccion',
                'accionistas',
                'datosAccionistas',
                'datosApoderado',
                'datosConstitucion',
                'revisionesExistentes',
                'comentariosGenerales',
                'citaCotejo',
                'todasSeccionesAprobadas',
                'tipo_revision'
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
                'datosSolicitante' => [],
                'datosDomicilio' => [],
                'datosSAT' => [],
                'documentos' => [],
                'documentosPorSeccion' => [],
                'accionistas' => [],
                'datosAccionistas' => [],
                'datosApoderado' => null,
                'datosConstitucion' => null,
                'revisionesExistentes' => [],
                'comentariosGenerales' => [],
                'tipo_revision' => $tipo_revision,
                'error' => 'Error al cargar los datos del trámite'
            ]);
        }
    }

    /**
     * Mostrar la vista de revisión digital
     */
    public function revisionDigital(Tramite $tramite)
    {
        try {
            Log::info('=== INICIO REVISIÓN DIGITAL ===', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'estado' => $tramite->estado
            ]);

            // Cargar relaciones necesarias
            $tramite->load(['solicitante', 'revisor', 'detalleTramite', 'seccionesRevision.seccion']);
            
            // Obtener todos los datos necesarios usando los métodos existentes
            $datosTramite = $this->obtenerDatosGenerales($tramite);
            $datosSolicitante = [
                'tipo_persona' => $tramite->solicitante->tipo_persona ?? '',
                'rfc' => $tramite->solicitante->rfc ?? '',
                'curp' => $tramite->solicitante->curp ?? '',
                'nombre_completo' => $tramite->solicitante->nombre_completo ?? '',
                'razon_social' => $tramite->solicitante->razon_social ?? ''
            ];
            $datosDomicilio = $this->obtenerDatosDomicilio($tramite);
            $datosSAT = $this->obtenerDatosSAT($tramite);
            $documentos = $this->obtenerDocumentos($tramite);
            $documentosPorSeccion = $this->obtenerDocumentosPorSeccion($tramite);
            
            // Para persona moral: obtener datos adicionales
            $datosConstitucion = null;
            $datosAccionistas = [];
            $accionistas = [];
            $datosApoderado = null;
            
            if ($tramite->solicitante && strtolower($tramite->solicitante->tipo_persona) === 'moral') {
                $datosConstitucion = $this->obtenerDatosConstitucion($tramite);
                $accionistasData = $this->obtenerDatosAccionistas($tramite);
                $datosAccionistas = $accionistasData;
                $accionistas = $accionistasData;
                $datosApoderado = $this->obtenerDatosApoderado($tramite);
            }

            // Obtener revisiones existentes
            $revisionesExistentes = $tramite->seccionesRevision->mapWithKeys(function ($revision) {
                return [$revision->seccion_id => [
                    'estado' => $revision->estado,
                    'comentario' => $revision->comentario,
                    'revisor' => $revision->revisor->name ?? 'N/A',
                    'fecha' => $revision->updated_at->format('d/m/Y H:i')
                ]];
            });

            $comentariosGenerales = $this->obtenerComentariosGenerales($tramite);
            $citaCotejo = \App\Models\Cita::where('tramite_id', $tramite->id)
                ->where('motivo', 'like', '%Cotejo físico%')
                ->first();

            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $seccionesRequeridas = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 3];
            
            $seccionesAprobadas = $tramite->seccionesRevision()
                ->whereIn('seccion_id', $seccionesRequeridas)
                ->where('estado', 'aprobado')
                ->count();
            
            $todasSeccionesAprobadas = $seccionesAprobadas === count($seccionesRequeridas);

            return view('revision.revision-digital', compact(
                'tramite',
                'datosTramite',
                'datosSolicitante',
                'datosDomicilio', 
                'datosSAT',
                'documentos',
                'documentosPorSeccion',
                'accionistas',
                'datosAccionistas',
                'datosApoderado',
                'datosConstitucion',
                'revisionesExistentes',
                'comentariosGenerales',
                'citaCotejo',
                'todasSeccionesAprobadas'
            ));

        } catch (\Exception $e) {
            Log::error('❌ Error al cargar revisión digital:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('revision.show', $tramite)
                ->with('error', 'Error al cargar la revisión digital');
        }
    }

    /**
     * Mostrar la vista de cotejo presencial
     */
    public function revisionPresencial(Tramite $tramite)
    {
        try {
            Log::info('=== INICIO COTEJO PRESENCIAL ===', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'estado' => $tramite->estado
            ]);

            // Cargar relaciones necesarias
            $tramite->load(['solicitante', 'revisor', 'detalleTramite', 'seccionesRevision.seccion']);
            
            // Obtener documentos para cotejo
            $documentos = $this->obtenerDocumentos($tramite);
            
            // Obtener revisiones existentes (especialmente para sección 6 - cotejo)
            $revisionesExistentes = $tramite->seccionesRevision->mapWithKeys(function ($revision) {
                return [$revision->seccion_id => [
                    'estado' => $revision->estado,
                    'comentario' => $revision->comentario,
                    'observaciones' => $revision->observaciones ?? '',
                    'revisor' => $revision->revisor->name ?? 'N/A',
                    'fecha' => $revision->updated_at->format('d/m/Y H:i')
                ]];
            });

            return view('revision.cotejo-presencial', compact(
                'tramite',
                'documentos',
                'revisionesExistentes'
            ));

        } catch (\Exception $e) {
            Log::error('❌ Error al cargar cotejo presencial:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('revision.show', $tramite)
                ->with('error', 'Error al cargar el cotejo presencial');
        }
    }

    /**
     * Obtener datos generales del trámite
     */
    private function obtenerDatosGenerales(Tramite $tramite)
    {
        try {
            Log::info('Obteniendo datos generales para el trámite:', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tramite->tipo_tramite,
                'solicitante_id' => $tramite->solicitante_id
            ]);
            
            $controller = new DatosGeneralesController();
            $datos = $controller->obtenerDatos($tramite);
            
            Log::info('Datos generales obtenidos:', [
                'tramite_id' => $tramite->id,
                'datos_count' => count($datos),
                'datos_keys' => array_keys($datos)
            ]);
            
            return $datos;
        } catch (\Exception $e) {
            Log::error('Error al obtener datos generales:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            Log::info('Obteniendo datos de domicilio para el trámite:', [
                'tramite_id' => $tramite->id
            ]);
            
            $controller = new DomicilioController();
            $datos = $controller->obtenerDatos($tramite);
            
            Log::info('Datos de domicilio obtenidos:', [
                'tramite_id' => $tramite->id,
                'datos_count' => count($datos),
                'tiene_codigo_postal' => !empty($datos['codigo_postal'])
            ]);
            
            return $datos;
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de domicilio:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            // Obtener documentos directamente de la base de datos
            $documentos = DocumentoSolicitante::where('tramite_id', $tramite->id)
                ->with('documento')
                ->get()
                ->map(function($doc) {
                    try {
                        // Intentar desencriptar la ruta del archivo
                        $rutaDesencriptada = \Illuminate\Support\Facades\Crypt::decryptString($doc->ruta_archivo);
                        $rutaArchivo = asset('storage/' . $rutaDesencriptada);
                    } catch (\Exception $e) {
                        // Si no se puede desencriptar, usar la ruta directamente
                        $rutaArchivo = asset('storage/' . $doc->ruta_archivo);
                    }
                    
                    return [
                        'id' => $doc->id,
                        'documento_id' => $doc->documento_id,
                        'nombre' => $doc->documento->nombre ?? 'Documento',
                        'tipo' => $doc->documento->tipo ?? 'No especificado',
                        'fecha_entrega' => $doc->fecha_entrega 
                            ? \Carbon\Carbon::parse($doc->fecha_entrega)->toIso8601String() 
                            : null,
                        'estado' => $doc->estado ?? 'Pendiente',
                        'version_documento' => $doc->version_documento ?? 1,
                        'ruta_archivo' => $rutaArchivo,
                        'observaciones' => $doc->observaciones,
                        'documento_cotejado' => $doc->documento_cotejado ?? false
                    ];
                })
                ->toArray();
                
            Log::info('Documentos obtenidos exitosamente:', [
                'tramite_id' => $tramite->id,
                'total_documentos' => count($documentos)
            ]);
            
            return $documentos;
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
            $constitucion = $controller->getIncorporationData($tramite);
            
            Log::info('Datos de constitución obtenidos:', [
                'tramite_id' => $tramite->id,
                'constitucion' => $constitucion,
                'tiene_datos' => !empty($constitucion) && !$this->sonDatosPorDefecto($constitucion)
            ]);
            
            // Si solo tiene datos por defecto ("No disponible"), devolver array vacío
            if ($this->sonDatosPorDefecto($constitucion)) {
                return [];
            }
            
            return $constitucion;
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de constitución:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Verifica si los datos de constitución son solo valores por defecto
     */
    private function sonDatosPorDefecto($datos): bool
    {
        if (empty($datos) || !is_array($datos)) {
            return true;
        }
        
        // Campos principales que deben tener valor para considerar que hay datos reales
        $camposPrincipales = ['numero_escritura', 'nombre_notario', 'fecha_constitucion'];
        
        foreach ($camposPrincipales as $campo) {
            if (isset($datos[$campo]) && !empty($datos[$campo]) && $datos[$campo] !== null) {
                return false; // Hay al menos un campo con datos reales
            }
        }
        
        return true; // Todos los campos principales están vacíos o null
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

    /**
     * Obtener documentos agrupados por sección
     */
    private function obtenerDocumentosPorSeccion(Tramite $tramite)
    {
        try {
            // Obtener documentos del trámite con sus archivos subidos
            $documentosSolicitante = $tramite->documentosSolicitante()
                ->with(['documento.secciones'])
                ->get();

            $documentosPorSeccion = [];

            // Mapear secciones con sus IDs
            $secciones = [
                1 => 'datos_generales',
                2 => 'domicilio', 
                3 => 'constitucion',
                4 => 'accionistas',
                5 => 'apoderado',
                6 => 'documentos'
            ];

            foreach ($documentosSolicitante as $docSolicitante) {
                $documento = $docSolicitante->documento;
                
                if ($documento && $documento->secciones) {
                    foreach ($documento->secciones as $seccion) {
                        $seccionNombre = $secciones[$seccion->id] ?? 'documentos';
                        
                        if (!isset($documentosPorSeccion[$seccionNombre])) {
                            $documentosPorSeccion[$seccionNombre] = [];
                        }
                        
                        $documentosPorSeccion[$seccionNombre][] = [
                            'id' => $docSolicitante->id, // ID del DocumentoSolicitante, no del Documento
                            'nombre' => $documento->nombre,
                            'descripcion' => $documento->descripcion,
                            'estado' => $docSolicitante->estado,
                            'ruta_archivo' => $docSolicitante->ruta_archivo ? route('documentos.ver', $docSolicitante->id) : null,
                            'fecha_entrega' => $docSolicitante->fecha_entrega,
                            'observaciones' => $docSolicitante->observaciones,
                            'seccion_id' => $seccion->id,
                            'seccion_nombre' => $seccion->nombre
                        ];
                    }
                }
            }

            return $documentosPorSeccion;
        } catch (\Exception $e) {
            Log::error('Error al obtener documentos por sección:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener comentarios generales del trámite
     */
    private function obtenerComentariosGenerales(Tramite $tramite)
    {
        $comentarios = [];
        
        if (!empty($tramite->observaciones)) {
            // Parsear los comentarios existentes del campo observaciones
            $lineas = explode("\n", $tramite->observaciones);
            $comentarioActual = null;
            
            foreach ($lineas as $linea) {
                if (preg_match('/\[(.*?) - (.*?)\]/', $linea, $matches)) {
                    // Si hay un comentario anterior, guardarlo
                    if ($comentarioActual) {
                        $comentarios[] = $comentarioActual;
                    }
                    
                    // Iniciar nuevo comentario
                    $comentarioActual = [
                        'fecha' => $matches[1],
                        'autor' => $matches[2],
                        'texto' => ''
                    ];
                } elseif ($comentarioActual && !empty(trim($linea))) {
                    $comentarioActual['texto'] .= trim($linea) . ' ';
                }
            }
            
            // Agregar el último comentario si existe
            if ($comentarioActual && !empty(trim($comentarioActual['texto']))) {
                $comentarios[] = $comentarioActual;
            }
        }
        
        return $comentarios;
    }

    /**
     * Agregar comentario general de revisión
     */
    public function agregarComentario(Request $request, Tramite $tramite)
    {
        $request->validate([
            'comentario_general' => 'required|string|max:1000',
        ], [
            'comentario_general.required' => 'El comentario es obligatorio',
            'comentario_general.max' => 'El comentario no puede exceder 1000 caracteres'
        ]);

        try {
            // Aquí puedes agregar la lógica para guardar el comentario en la base de datos
            // Por ejemplo, crear una tabla de comentarios_revision o agregarlo al trámite
            
            $tramite->update([
                'observaciones' => $tramite->observaciones . "\n\n[" . now()->format('d/m/Y H:i') . " - " . Auth::user()->name . "]\n" . $request->comentario_general
            ]);

            return redirect()->back()->with('success', 'Comentario agregado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al agregar comentario:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al agregar el comentario');
        }
    }

    /**
     * Aprobar sección específica
     */
    public function aprobarSeccion(Request $request, Tramite $tramite, $seccionId)
    {
        // Verificar permisos siempre
        if (!Gate::allows('revision-tramites.aprobar')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para aprobar secciones'
            ], 403);
        }

        $request->validate([
            'comentario' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar la revisión de la sección
            $seccionRevision = SeccionRevision::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'seccion_id' => $seccionId,
                ],
                [
                    'estado' => 'aprobado',
                    'comentario' => $request->comentario,
                    'revisado_por' => Auth::id(),
                    'fecha_revision' => now()
                ]
            );

            // Actualizar el progreso del trámite
            $progresoTramite = $tramite->progresoSecciones()->where('seccion_id', $seccionId)->first();
            
            if ($progresoTramite) {
                $progresoTramite->update([
                    'estado' => 'aprobado',
                    'observaciones' => $request->comentario,
                    'fecha_completado' => now()
                ]);
            } else {
                $tramite->progresoSecciones()->create([
                    'seccion_id' => $seccionId,
                    'estado' => 'aprobado',
                    'observaciones' => $request->comentario,
                    'fecha_inicio' => now(),
                    'fecha_completado' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sección aprobada correctamente',
                'estado' => 'aprobado'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aprobar sección:', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la sección: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar sección específica
     */
    public function rechazarSeccion(Request $request, Tramite $tramite, $seccionId)
    {
        // Verificar permisos siempre
        if (!Gate::allows('revision-tramites.rechazar')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para rechazar secciones'
            ], 403);
        }

        $request->validate([
            'comentario' => 'required|string|max:500'
        ], [
            'comentario.required' => 'Debe proporcionar un comentario para rechazar la sección'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar la revisión de la sección
            $seccionRevision = SeccionRevision::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'seccion_id' => $seccionId,
                ],
                [
                    'estado' => 'rechazado',
                    'comentario' => $request->comentario,
                    'revisado_por' => Auth::id(),
                    'fecha_revision' => now()
                ]
            );

            // Actualizar el progreso del trámite
            $progresoTramite = $tramite->progresoSecciones()->where('seccion_id', $seccionId)->first();
            
            if ($progresoTramite) {
                $progresoTramite->update([
                    'estado' => 'rechazado',
                    'observaciones' => $request->comentario,
                    'fecha_completado' => now()
                ]);
            } else {
                $tramite->progresoSecciones()->create([
                    'seccion_id' => $seccionId,
                    'estado' => 'rechazado',
                    'observaciones' => $request->comentario,
                    'fecha_inicio' => now(),
                    'fecha_completado' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sección rechazada correctamente',
                'estado' => 'rechazado'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al rechazar sección:', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la sección: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Completar revisión digital y agendar cita para cotejo físico
     */
    public function aprobarTodo(Request $request, Tramite $tramite)
    {
        try {
            DB::beginTransaction();

            // Determinar qué secciones aprobar según el tipo de persona
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $seccionesAAprobar = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 3]; // 3 es documentos para persona física
            
            Log::info('Iniciando revisión digital completa de trámite:', [
                'tramite_id' => $tramite->id,
                'tipo_persona' => $tipoPersona,
                'secciones_a_aprobar' => $seccionesAAprobar,
                'revisor' => Auth::id()
            ]);

            // Aprobar las secciones correspondientes
            foreach ($seccionesAAprobar as $seccionId) {
                SeccionRevision::updateOrCreate(
                    [
                        'tramite_id' => $tramite->id,
                        'seccion_id' => $seccionId,
                    ],
                    [
                        'estado' => 'aprobado',
                        'comentario' => 'Aprobado en revisión digital completa',
                        'revisor_id' => Auth::id(),
                        'fecha_revision' => now()
                    ]
                );
            }

            // Actualizar estado del trámite a "Revisión Digital Completada"
            $tramite->update([
                'estado' => 'Revision Digital Completada',
                'fecha_revision' => now(),
                'revisado_por' => Auth::id(),
                'observaciones' => 'Revisión digital aprobada. Pendiente cotejo físico de documentos.'
            ]);

            // ✅ AGENDAR CITA AUTOMÁTICAMENTE PARA COTEJO FÍSICO
            $cita = \App\Models\Cita::agendarCotejoAutomatico($tramite);

            // 🔔 CREAR NOTIFICACIÓN PARA EL USUARIO
            if ($tramite->solicitante && $tramite->solicitante->usuario_id && $cita) {
                try {
                    $fechaCita = $cita->fecha_hora->format('d/m/Y');
                    $horaCita = $cita->fecha_hora->format('H:i');
                    
                    \App\Models\Notificacion::crearParaUsuario(
                        '✅ Revisión Digital Aprobada - Cita Agendada',
                        "¡Excelente noticia! Su trámite #" . str_pad($tramite->id, 6, '0', STR_PAD_LEFT) . " ha completado exitosamente la revisión digital. " .
                        "Se ha agendado automáticamente una cita para el cotejo físico de sus documentos:\n\n" .
                        "📅 Fecha: {$fechaCita}\n⏰ Hora: {$horaCita}\n📍 Ubicación: Oficinas del Gobierno del Estado\n\n" .
                        "Por favor, traiga TODOS los documentos originales para su verificación. " .
                        "Después del cotejo exitoso, se le otorgará su código de proveedor oficial.",
                        'Informativo',
                        $tramite->solicitante->usuario_id
                    );
                    
                    Log::info('Notificación de cita agendada enviada:', [
                        'tramite_id' => $tramite->id,
                        'usuario_id' => $tramite->solicitante->usuario_id,
                        'cita_id' => $cita->id,
                        'fecha_cita' => $cita->fecha_hora->format('Y-m-d H:i')
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al crear notificación de cita:', [
                        'tramite_id' => $tramite->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Revisión digital completada y cita agendada:', [
                'tramite_id' => $tramite->id,
                'revisor' => Auth::id(),
                'tipo_persona' => $tipoPersona,
                'cita_id' => $cita->id,
                'fecha_cita' => $cita->fecha_hora->format('Y-m-d H:i'),
                'nuevo_estado' => 'Revision Digital Completada'
            ]);

            return redirect()->route('revision.index')->with('success', 
                'Revisión digital completada exitosamente. Cita agendada para el ' . $cita->fecha_hora->format('d/m/Y') . ' a las ' . $cita->fecha_hora->format('H:i') . ' para cotejo físico de documentos.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al completar revisión digital:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al completar la revisión digital: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar todo el trámite
     */
    public function rechazarTodo(Request $request, Tramite $tramite)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000'
        ], [
            'comentario.required' => 'Debe proporcionar un comentario para rechazar el trámite'
        ]);

        try {
            DB::beginTransaction();

            // Determinar qué secciones rechazar según el tipo de persona
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $seccionesARechazar = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 3]; // 3 es documentos para persona física
            
            Log::info('Iniciando rechazo completo de trámite:', [
                'tramite_id' => $tramite->id,
                'tipo_persona' => $tipoPersona,
                'secciones_a_rechazar' => $seccionesARechazar,
                'revisor' => Auth::id(),
                'comentario' => $request->comentario
            ]);

            // Rechazar las secciones correspondientes
            foreach ($seccionesARechazar as $seccionId) {
                SeccionRevision::updateOrCreate(
                    [
                        'tramite_id' => $tramite->id,
                        'seccion_id' => $seccionId,
                    ],
                    [
                        'estado' => 'rechazado',
                        'comentario' => $request->comentario,
                        'revisor_id' => Auth::id(),
                        'fecha_revision' => now()
                    ]
                );
            }

            // Actualizar estado del trámite
            $tramite->update([
                'estado' => 'Rechazado',
                'fecha_revision' => now(),
                'revisado_por' => Auth::id(),
                'observaciones' => $request->comentario
            ]);

            // 🔔 CREAR NOTIFICACIÓN PARA EL USUARIO
            if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
                try {
                    Notificacion::crearParaUsuario(
                        'Trámite Rechazado',
                        "Su trámite #" . str_pad($tramite->id, 6, '0', STR_PAD_LEFT) . " ha sido rechazado. " .
                        "Motivo del rechazo: " . $request->comentario . ". " .
                        "Por favor, corrija los puntos observados y vuelva a enviar su solicitud.",
                        'Advertencia',
                        $tramite->solicitante->usuario_id
                    );
                    
                    Log::info('Notificación de rechazo enviada:', [
                        'tramite_id' => $tramite->id,
                        'usuario_id' => $tramite->solicitante->usuario_id,
                        'comentario' => $request->comentario
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al crear notificación de rechazo:', [
                        'tramite_id' => $tramite->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Trámite rechazado completamente:', [
                'tramite_id' => $tramite->id,
                'revisor' => Auth::id(),
                'tipo_persona' => $tipoPersona
            ]);

            return redirect()->route('revision.index')->with('success', 'Trámite rechazado completamente');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al rechazar todo el trámite:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al rechazar el trámite: ' . $e->getMessage());
        }
    }

    /**
     * Solicitar correcciones para el trámite
     */
    public function solicitarCorrecciones(Request $request, Tramite $tramite)
    {
        try {
            // Validar que el trámite esté en un estado válido para solicitar correcciones
            if (!in_array($tramite->estado, ['en_revision', 'en_correccion'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El trámite no está en un estado válido para solicitar correcciones'
                ], 400);
            }

            // Actualizar el estado del trámite
            $tramite->update([
                'estado' => 'en_correccion',
                'fecha_ultima_actualizacion' => now()
            ]);

            // Registrar el evento en el historial
            $this->registrarHistorial($tramite, 'Se solicitaron correcciones', $request->comentario);

            // Notificar al solicitante
            event(new SolicitudCorreccionesEvent($tramite));

            return response()->json([
                'success' => true,
                'message' => 'Se han solicitado las correcciones correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al solicitar correcciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registra un evento en el historial del trámite.
     *
     * @param Tramite $tramite
     * @param string $accion
     * @param string|null $comentario
     * @return void
     */
    protected function registrarHistorial(Tramite $tramite, string $accion, ?string $comentario = null)
    {
        Log::info("Registrando historial de trámite", [
            'tramite_id' => $tramite->id,
            'accion' => $accion,
            'comentario' => $comentario
        ]);
        
        // Registrar en el log del sistema con el formato correcto
        return \App\Models\Log::create([
            'level' => 'info',
            'message' => "Trámite {$tramite->id}: {$accion}",
            'context' => [
                'tramite_id' => $tramite->id,
                'usuario_id' => Auth::id(),
                'accion' => $accion,
                'comentario' => $comentario
            ],
            'channel' => 'tramites',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'url' => request()->url(),
            'method' => request()->method(),
        ]);
    }

    /**
     * Obtener datos de una sección específica de un trámite
     */
    public function obtenerDatosSeccion($tramiteId)
    {
        try {
            Log::info('Obteniendo datos de sección', [
                'tramite_id' => $tramiteId,
                'seccion' => request()->segment(5)
            ]);

            $tramite = Tramite::with([
                'solicitante',
                'detalleTramite.contacto',
                'direccion.municipio',
                'direccion.estado',
                'direccion.pais',
                'datosConstitucion',
                'representanteLegal',
                'accionistas',
                'documentos'
            ])->findOrFail($tramiteId);

            // Obtener la sección de la URL
            $seccion = request()->segment(5);

            // Mapear secciones a sus respectivos métodos
            $datos = match ($seccion) {
                'datos-generales' => $this->obtenerDatosGenerales($tramite),
                'domicilio' => $this->obtenerDatosDomicilio($tramite),
                'constitucion' => $this->obtenerDatosConstitucion($tramite),
                'apoderado' => $this->obtenerDatosApoderado($tramite),
                'accionistas' => $this->obtenerDatosAccionistas($tramite),
                'documentos' => $this->obtenerDocumentos($tramite),
                default => throw new \Exception("Sección no válida: {$seccion}")
            };

            return response()->json([
                'success' => true,
                'data' => $datos
            ]);

        } catch (\Exception $e) {
            Log::error("Error al obtener datos de sección para trámite {$tramiteId}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pausar revisión del trámite
     */
    public function pausarRevision(Request $request, Tramite $tramite)
    {
        try {
            $tramite->update([
                'estado' => 'Por Cotejar',
                'observaciones' => $request->comentario ?? 'Revisión pausada'
            ]);

            return redirect()->route('revision.index')->with('success', 'Revisión pausada correctamente');
        } catch (\Exception $e) {
            Log::error('Error al pausar revisión:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al pausar la revisión');
        }
    }

    /**
     * Obtener estado actual de las revisiones por sección (AJAX)
     */
    public function obtenerEstadoRevisiones(Tramite $tramite)
    {
        try {
            // Determinar secciones según tipo de persona
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $seccionesRequeridas = $tipoPersona === 'Moral' 
                ? [1 => 'General', 2 => 'Domicilio', 3 => 'Constitución', 4 => 'Accionistas', 5 => 'Apoderado', 6 => 'Documentos']
                : [1 => 'General', 2 => 'Domicilio', 6 => 'Documentos'];
            
            // Obtener revisiones existentes
            $revisiones = $tramite->seccionesRevision()
                ->whereIn('seccion_id', array_keys($seccionesRequeridas))
                ->get()
                ->keyBy('seccion_id');
            
            $estadoSecciones = [];
            $todasAprobadas = true;
            $algunaRechazada = false;
            
            foreach ($seccionesRequeridas as $seccionId => $nombre) {
                $revision = $revisiones->get($seccionId);
                $estado = $revision ? $revision->estado : 'pendiente';
                $comentario = $revision ? $revision->comentario : '';
                $fechaRevision = $revision ? $revision->fecha_revision : null;
                $revisor = $revision && $revision->revisor ? $revision->revisor->name : '';
                
                $estadoSecciones[$seccionId] = [
                    'seccion_id' => $seccionId,
                    'nombre' => $nombre,
                    'estado' => $estado,
                    'comentario' => $comentario,
                    'fecha_revision' => $fechaRevision ? $fechaRevision->format('d/m/Y H:i') : null,
                    'revisor' => $revisor
                ];
                
                if ($estado !== 'aprobado') {
                    $todasAprobadas = false;
                }
                
                if ($estado === 'rechazado') {
                    $algunaRechazada = true;
                }
            }
            
            return response()->json([
                'success' => true,
                'secciones' => $estadoSecciones,
                'todas_aprobadas' => $todasAprobadas,
                'alguna_rechazada' => $algunaRechazada,
                'puede_aprobar_todo' => $todasAprobadas && !$algunaRechazada,
                'tipo_persona' => $tipoPersona,
                'total_secciones' => count($seccionesRequeridas),
                'aprobadas' => collect($estadoSecciones)->where('estado', 'aprobado')->count(),
                'rechazadas' => collect($estadoSecciones)->where('estado', 'rechazado')->count(),
                'pendientes' => collect($estadoSecciones)->where('estado', 'pendiente')->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener estado de revisiones:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado de las revisiones'
            ], 500);
        }
    }

    /**
     * Guardar comentario de sección via AJAX
     */
    public function guardarComentarioSeccion(Request $request, Tramite $tramite, $seccionId)
    {
        $request->validate([
            'comentario' => 'required|string|max:500'
        ]);

        try {
            $revision = SeccionRevision::where('tramite_id', $tramite->id)
                ->where('seccion_id', $seccionId)
                ->first();
                
            if ($revision) {
                $revision->update([
                    'comentario' => $request->comentario,
                    'revisor_id' => Auth::id()
                ]);
            } else {
                SeccionRevision::create([
                    'tramite_id' => $tramite->id,
                    'seccion_id' => $seccionId,
                    'estado' => 'pendiente',
                    'comentario' => $request->comentario,
                    'revisor_id' => Auth::id(),
                    'fecha_revision' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Comentario guardado correctamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al guardar comentario de sección:', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el comentario'
            ], 500);
        }
    }

    /**
     * Obtiene los documentos asociados a una sección específica del trámite
     *
     * @param Tramite $tramite
     * @param int $seccionId
     * @return array
     */
    private function getDocumentosPorSeccion(Tramite $tramite, $seccionId)
    {
        // Obtener documentos que pertenecen a la sección
        $documentos = Documento::whereHas('secciones', function($query) use ($seccionId) {
            $query->where('seccion_id', $seccionId);
        })->get();

        // Obtener los documentos del solicitante para este trámite
        $documentosSolicitante = DocumentoSolicitante::where('tramite_id', $tramite->id)
            ->whereIn('documento_id', $documentos->pluck('id'))
            ->with('documento')
            ->get();

        // Mapear los documentos con su estado y ruta
        $documentosMapeados = $documentos->map(function($documento) use ($documentosSolicitante) {
            $docSolicitante = $documentosSolicitante->firstWhere('documento_id', $documento->id);
            
            return [
                'id' => $docSolicitante ? $docSolicitante->id : null, // ID del DocumentoSolicitante, no del Documento
                'nombre' => $documento->nombre,
                'descripcion' => $documento->descripcion,
                'estado' => $docSolicitante ? $docSolicitante->estado : 'pendiente',
                'ruta_archivo' => $docSolicitante ? $docSolicitante->ruta_archivo : null,
                'comentario' => $docSolicitante ? $docSolicitante->observaciones : null,
                'version' => $docSolicitante ? $docSolicitante->version_documento : 1
            ];
        });

        // Filtrar solo los documentos que tienen DocumentoSolicitante asociado
        return $documentosMapeados->filter(function($doc) {
            return $doc['id'] !== null;
        })->values();
    }

    /**
     * Obtiene los documentos de una sección vía AJAX
     */
    public function getDocumentosSeccion(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Solicitando documentos de sección', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $request->query('seccion_id')
            ]);

            // Validar que el ID de sección sea válido
            $seccionId = $request->query('seccion_id');
            if (!$seccionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de sección no proporcionado'
                ], 400);
            }

            // Obtener documentos de la sección específica usando la tabla intermedia documento_seccion
            $documentos = DocumentoSolicitante::with(['documento'])
                ->where('tramite_id', $tramite->id)
                ->whereExists(function ($query) use ($seccionId) {
                    $query->select(DB::raw(1))
                          ->from('documento_seccion')
                          ->whereColumn('documento_seccion.documento_id', 'documento_solicitante.documento_id')
                          ->where('documento_seccion.seccion_id', $seccionId);
                })
                ->get();

            Log::info('Documentos encontrados', [
                'cantidad' => $documentos->count(),
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId
            ]);

            // Transformar los documentos para la respuesta
            $documentosFormateados = $documentos->map(function ($documentoSolicitante) {
                return [
                    'id' => $documentoSolicitante->id,
                    'nombre' => $documentoSolicitante->documento->nombre ?? 'Sin nombre',
                    'descripcion' => $documentoSolicitante->documento->descripcion ?? '',
                    'estado' => $documentoSolicitante->estado ?? 'Pendiente',
                    'fecha_carga' => optional($documentoSolicitante->fecha_entrega)->format('d/m/Y'),
                    'version' => $documentoSolicitante->version_documento,
                    'observaciones' => $documentoSolicitante->observaciones,
                    'url' => $documentoSolicitante->ruta_archivo ? route('documentos.ver', $documentoSolicitante->id) : null
                ];
            });

            return response()->json([
                'success' => true,
                'documentos' => $documentosFormateados
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener documentos de sección', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $request->query('seccion_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los documentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver documento en contexto de revisión
     * Permite a los revisores ver cualquier documento sin restricciones de propietario
     */
    public function verDocumento(Request $request, Tramite $tramite, $documentoSolicitanteId)
    {
        try {
            Log::info('Revisor intentando ver documento', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'revisor_id' => auth()->id(),
                'revisor_name' => auth()->user()->name
            ]);

            // Verificar que el usuario tiene permisos de revisión
            if (!auth()->user()->can('revision-tramites.ver')) {
                Log::warning('Usuario sin permisos de revisión intenta ver documento', [
                    'user_id' => auth()->id(),
                    'tramite_id' => $tramite->id,
                    'documento_solicitante_id' => $documentoSolicitanteId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para ver este documento'
                ], 403);
            }

            // Buscar el documento solicitante por su ID directo y verificar que pertenece al trámite
            $documentoSolicitante = DocumentoSolicitante::where('id', $documentoSolicitanteId)
                ->where('tramite_id', $tramite->id)
                ->first();

            if (!$documentoSolicitante || !$documentoSolicitante->ruta_archivo) {
                Log::warning('Documento no encontrado para revisión', [
                    'tramite_id' => $tramite->id,
                    'documento_solicitante_id' => $documentoSolicitanteId,
                    'existe_registro' => $documentoSolicitante ? 'SI' : 'NO',
                    'tiene_ruta' => $documentoSolicitante && $documentoSolicitante->ruta_archivo ? 'SI' : 'NO'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            Log::info('Documento encontrado para revisión', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'documento_id' => $documentoSolicitante->documento_id,
                'ruta_encriptada' => $documentoSolicitante->ruta_archivo
            ]);

            // Desencriptar la ruta del archivo
            $rutaArchivo = '';
            try {
                // Intentar desencriptar con el nuevo método (encrypt/decrypt)
                $rutaArchivo = decrypt($documentoSolicitante->ruta_archivo);
                Log::info('Ruta desencriptada con decrypt()', ['ruta' => $rutaArchivo]);
            } catch (\Exception $e1) {
                try {
                    // Intentar desencriptar con Crypt
                    $rutaArchivo = \Illuminate\Support\Facades\Crypt::decryptString($documentoSolicitante->ruta_archivo);
                    Log::info('Ruta desencriptada con Crypt::decryptString()', ['ruta' => $rutaArchivo]);
                } catch (\Exception $e2) {
                    // Si no se puede desencriptar, usar la ruta directamente
                    $rutaArchivo = $documentoSolicitante->ruta_archivo;
                    Log::info('Usando ruta sin encriptar para revisión', ['ruta' => $rutaArchivo]);
                }
            }

            // Probar diferentes ubicaciones posibles del archivo
            $rutasPosibles = [
                storage_path('app/public/' . $rutaArchivo),
                storage_path('app/public/documentos_solicitante/' . $tramite->id . '/' . basename($rutaArchivo)),
                storage_path('app/public/documentos_tramite/' . $tramite->id . '/' . basename($rutaArchivo)),
                storage_path('app/' . $rutaArchivo),
                storage_path('app/public/documentos/' . basename($rutaArchivo))
            ];

            $rutaCompleta = null;
            foreach ($rutasPosibles as $ruta) {
                Log::info('Verificando ruta para revisión', ['ruta' => $ruta, 'existe' => file_exists($ruta)]);
                if (file_exists($ruta)) {
                    $rutaCompleta = $ruta;
                    break;
                }
            }

            if (!$rutaCompleta) {
                Log::error('Archivo no encontrado para revisión en ninguna ubicación', [
                    'tramite_id' => $tramite->id,
                    'documento_solicitante_id' => $documentoSolicitanteId,
                    'documento_id' => $documentoSolicitante->documento_id,
                    'ruta_original' => $rutaArchivo,
                    'rutas_probadas' => $rutasPosibles
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no existe en el servidor'
                ], 404);
            }

            Log::info('Archivo encontrado para revisión', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'documento_id' => $documentoSolicitante->documento_id,
                'ruta_final' => $rutaCompleta,
                'revisor' => auth()->user()->name
            ]);

            // Obtener información del documento
            $documento = \App\Models\Documento::find($documentoSolicitante->documento_id);
            $nombreArchivo = ($documento ? $documento->nombre : 'Documento') . '.pdf';

            // Detectar si es móvil para forzar descarga
            $userAgent = $request->header('User-Agent');
            $esMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);

            if ($esMobile || $request->get('download') === '1') {
                // Forzar descarga en móviles
                return response()->download($rutaCompleta, $nombreArchivo);
            } else {
                // Mostrar en el navegador (desktop)
                return response()->file($rutaCompleta, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al ver documento en revisión:', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'revisor_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al acceder al documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprobar documento individual
     */
    public function aprobarDocumento(Request $request, Tramite $tramite, $documentoSolicitanteId)
    {
        try {
            // Verificar permisos
            if (!Gate::allows('revision-tramites.aprobar')) {
                $isAjax = $request->expectsJson() || 
                         $request->header('Accept') === 'application/json' || 
                         $request->header('X-Requested-With') === 'XMLHttpRequest';
                         
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para aprobar documentos'
                    ], 403);
                }
                abort(403, 'No tienes permisos para aprobar documentos');
            }

            // Buscar el documento
            $documentoSolicitante = DocumentoSolicitante::where('id', $documentoSolicitanteId)
                ->where('tramite_id', $tramite->id)
                ->first();

            if (!$documentoSolicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            // Actualizar estado del documento
            $documentoSolicitante->update([
                'estado' => 'Aprobado',
                'observaciones' => $request->input('comentario', 'Documento aprobado'),
                'documento_cotejado' => $request->input('documento_cotejado', false)
            ]);

            Log::info('Documento aprobado individualmente', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'revisor_id' => auth()->id(),
                'comentario' => $request->input('comentario')
            ]);

            $isAjax = $request->expectsJson() || 
                     $request->header('Accept') === 'application/json' || 
                     $request->header('X-Requested-With') === 'XMLHttpRequest';
                     
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento aprobado correctamente'
                ]);
            }

            return redirect()->back()->with('success', 'Documento aprobado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al aprobar documento:', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'error' => $e->getMessage()
            ]);

            $isAjax = $request->expectsJson() || 
                     $request->header('Accept') === 'application/json' || 
                     $request->header('X-Requested-With') === 'XMLHttpRequest';
                     
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar el documento'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al aprobar el documento');
        }
    }

    /**
     * Rechazar documento individual
     */
    public function rechazarDocumento(Request $request, Tramite $tramite, $documentoSolicitanteId)
    {
        try {
            // Verificar permisos
            if (!Gate::allows('revision-tramites.rechazar')) {
                $isAjax = $request->expectsJson() || 
                         $request->header('Accept') === 'application/json' || 
                         $request->header('X-Requested-With') === 'XMLHttpRequest';
                         
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para rechazar documentos'
                    ], 403);
                }
                abort(403, 'No tienes permisos para rechazar documentos');
            }

            // Validar que se proporcione un comentario
            $request->validate([
                'comentario' => 'required|string|min:10'
            ]);

            // Buscar el documento
            $documentoSolicitante = DocumentoSolicitante::where('id', $documentoSolicitanteId)
                ->where('tramite_id', $tramite->id)
                ->first();

            if (!$documentoSolicitante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado'
                ], 404);
            }

            // Actualizar estado del documento
            $documentoSolicitante->update([
                'estado' => 'Rechazado',
                'observaciones' => $request->input('comentario')
            ]);

            Log::info('Documento rechazado individualmente', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'revisor_id' => auth()->id(),
                'comentario' => $request->input('comentario')
            ]);

            $isAjax = $request->expectsJson() || 
                     $request->header('Accept') === 'application/json' || 
                     $request->header('X-Requested-With') === 'XMLHttpRequest';
                     
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento rechazado correctamente'
                ]);
            }

            return redirect()->back()->with('success', 'Documento rechazado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al rechazar documento:', [
                'tramite_id' => $tramite->id,
                'documento_solicitante_id' => $documentoSolicitanteId,
                'error' => $e->getMessage()
            ]);

            $isAjax = $request->expectsJson() || 
                     $request->header('Accept') === 'application/json' || 
                     $request->header('X-Requested-With') === 'XMLHttpRequest';
                     
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al rechazar el documento'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al rechazar el documento');
        }
    }

    /**
     * Agendar cita de cotejo presencial general desde revisión digital
     */
    public function agendarCitaRevision(Request $request, Tramite $tramite)
    {
        $request->validate([
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'required|string|max:255',
            'notas' => 'nullable|string',
            'general' => 'boolean',
        ]);

        try {
            // Cargar la relación del solicitante
            $tramite->load('solicitante');
            
            // Verificar que el trámite tenga un solicitante válido
            if (!$tramite->solicitante || !$tramite->solicitante->usuario_id) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'No se pudo encontrar información del solicitante para este trámite'
                ], 200);
            }
            
            // Verificar que no exista ya una cita para este trámite
            $citaExistente = Cita::where('tramite_id', $tramite->id)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->first();

            if ($citaExistente) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'Ya existe una cita programada para este trámite. Fecha: ' . 
                               $citaExistente->fecha_hora->format('d/m/Y H:i')
                ], 200); // Código 200 para evitar logs en consola
            }

            // Verificar que la fecha no sea fin de semana
            $fechaCita = \Carbon\Carbon::parse($request->fecha_hora);
            if ($fechaCita->isWeekend()) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'No se pueden agendar citas en fines de semana'
                ], 200);
            }

            // Verificar que no haya conflicto de horario (máximo 4 citas por hora)
            $citasEnHorario = Cita::where('fecha_hora', $request->fecha_hora)
                ->where('estado', '!=', 'cancelada')
                ->count();

            if ($citasEnHorario >= 4) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'validation',
                    'message' => 'No hay disponibilidad en ese horario. Máximo 4 citas por hora. Por favor, seleccione otro horario.'
                ], 200);
            }

            // Crear la cita asignada al solicitante del trámite
            $cita = Cita::create([
                'user_id' => $tramite->solicitante->usuario_id,
                'tramite_id' => $tramite->id,
                'fecha_hora' => $request->fecha_hora,
                'motivo' => $request->motivo,
                'notas' => $request->notas,
                'estado' => 'pendiente',
            ]);

            // Registrar en el historial
            $this->registrarHistorial($tramite, 'cita_agendada', 
                "Cita general agendada para cotejo presencial - Fecha: {$fechaCita->format('d/m/Y H:i')}");

            // Log de la acción
            Log::info('🗓️ CITA GENERAL AGENDADA desde revisión', [
                'tramite_id' => $tramite->id,
                'cita_id' => $cita->id,
                'fecha_hora' => $request->fecha_hora,
                'motivo' => $request->motivo,
                'usuario_id' => Auth::id(),
                'tipo' => 'general'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cita agendada correctamente para el cotejo presencial del trámite completo',
                'cita' => [
                    'id' => $cita->id,
                    'fecha_hora' => $fechaCita->format('d/m/Y H:i'),
                    'motivo' => $cita->motivo,
                    'estado' => $cita->estado
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR al agendar cita general desde revisión', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al agendar la cita'
            ], 500);
        }
    }
} 