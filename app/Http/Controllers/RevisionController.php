<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\SeccionRevision;
use App\Models\DocumentoSolicitante;
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
    public function index(Request $request)
    {
        // Obtener trámites con información del solicitante
        $query = Tramite::with(['solicitante', 'revisor']);

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
            $tramite->load(['solicitante', 'revisor', 'detalleTramite', 'seccionesRevision.seccion']);
            
            // 1. Obtener datos generales
            $datosTramite = $this->obtenerDatosGenerales($tramite);
            
            // 2. Obtener datos de domicilio
            $datosDomicilio = $this->obtenerDatosDomicilio($tramite);
            
            // 3. Obtener datos SAT (si existen)
            $datosSAT = $this->obtenerDatosSAT($tramite);
            
            // 4. Obtener documentos
            $documentos = $this->obtenerDocumentos($tramite);
            
            // 4.1. Obtener documentos agrupados por sección
            $documentosPorSeccion = $this->obtenerDocumentosPorSeccion($tramite);
            
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

            return view('revision.show', compact(
                'tramite',
                'datosTramite',
                'datosDomicilio', 
                'datosSAT',
                'documentos',
                'documentosPorSeccion',
                'accionistas',
                'apoderado',
                'constitucion',
                'revisionesExistentes',
                'comentariosGenerales'
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
                'documentosPorSeccion' => [],
                'accionistas' => [],
                'apoderado' => null,
                'constitucion' => null,
                'revisionesExistentes' => [],
                'comentariosGenerales' => [],
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
     * Ver documento desencriptado
     */
    public function verDocumento(Tramite $tramite, $documentoId)
    {
        try {
            // Buscar el documento del solicitante
            $documentoSolicitante = DocumentoSolicitante::where('tramite_id', $tramite->id)
                ->where('documento_id', $documentoId)
                ->first();

            if (!$documentoSolicitante || !$documentoSolicitante->ruta_archivo) {
                abort(404, 'Documento no encontrado');
            }

            // Desencriptar la ruta del archivo
            try {
                $rutaArchivo = \Illuminate\Support\Facades\Crypt::decryptString($documentoSolicitante->ruta_archivo);
            } catch (\Exception $e) {
                // Si no está encriptado, usar la ruta directamente
                $rutaArchivo = $documentoSolicitante->ruta_archivo;
            }

            $rutaCompleta = storage_path('app/public/' . $rutaArchivo);

            if (!file_exists($rutaCompleta)) {
                abort(404, 'El archivo no existe en el servidor');
            }

            return response()->file($rutaCompleta);

        } catch (\Exception $e) {
            Log::error('Error al ver documento:', [
                'tramite_id' => $tramite->id,
                'documento_id' => $documentoId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Error al cargar el documento');
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
                            'id' => $documento->id,
                            'nombre' => $documento->nombre,
                            'descripcion' => $documento->descripcion,
                            'estado' => $docSolicitante->estado,
                            'ruta_archivo' => route('revision.ver-documento', ['tramite' => $tramite->id, 'documentoId' => $documento->id]),
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
        $request->validate([
            'comentario' => 'nullable|string|max:500'
        ]);

        try {
            SeccionRevision::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                    'seccion_id' => $seccionId,
                ],
                [
                    'estado' => 'aprobado',
                    'comentario' => $request->comentario,
                    'revisor_id' => Auth::id(),
                    'fecha_revision' => now()
                ]
            );

            return redirect()->back()->with('success', 'Sección aprobada correctamente');
        } catch (\Exception $e) {
            Log::error('Error al aprobar sección:', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al aprobar la sección');
        }
    }

    /**
     * Rechazar sección específica
     */
    public function rechazarSeccion(Request $request, Tramite $tramite, $seccionId)
    {
        $request->validate([
            'comentario' => 'required|string|max:500'
        ], [
            'comentario.required' => 'Debe proporcionar un comentario para rechazar la sección'
        ]);

        try {
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

            return redirect()->back()->with('success', 'Sección rechazada correctamente');
        } catch (\Exception $e) {
            Log::error('Error al rechazar sección:', [
                'tramite_id' => $tramite->id,
                'seccion_id' => $seccionId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al rechazar la sección');
        }
    }

    /**
     * Aprobar todo el trámite
     */
    public function aprobarTodo(Request $request, Tramite $tramite)
    {
        try {
            // Aprobar todas las secciones
            $secciones = [1, 2, 3, 4, 5, 6];
            
            foreach ($secciones as $seccionId) {
                SeccionRevision::updateOrCreate(
                    [
                        'tramite_id' => $tramite->id,
                        'seccion_id' => $seccionId,
                    ],
                    [
                        'estado' => 'aprobado',
                        'comentario' => 'Aprobado en revisión completa',
                        'revisor_id' => Auth::id(),
                        'fecha_revision' => now()
                    ]
                );
            }

            // Actualizar estado del trámite
            $tramite->update([
                'estado' => 'Aprobado',
                'fecha_revision' => now(),
                'revisado_por' => Auth::id()
            ]);

            return redirect()->route('revision.index')->with('success', 'Trámite aprobado completamente');
        } catch (\Exception $e) {
            Log::error('Error al aprobar todo el trámite:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al aprobar el trámite');
        }
    }

    /**
     * Rechazar todo el trámite
     */
    public function rechazarTodo(Request $request, Tramite $tramite)
    {
        $request->validate([
            'comentario_general' => 'required|string|max:1000'
        ], [
            'comentario_general.required' => 'Debe proporcionar un comentario para rechazar el trámite'
        ]);

        try {
            // Rechazar todas las secciones
            $secciones = [1, 2, 3, 4, 5, 6];
            
            foreach ($secciones as $seccionId) {
                SeccionRevision::updateOrCreate(
                    [
                        'tramite_id' => $tramite->id,
                        'seccion_id' => $seccionId,
                    ],
                    [
                        'estado' => 'rechazado',
                        'comentario' => $request->comentario_general,
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
                'observaciones' => $request->comentario_general
            ]);

            return redirect()->route('revision.index')->with('success', 'Trámite rechazado completamente');
        } catch (\Exception $e) {
            Log::error('Error al rechazar todo el trámite:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al rechazar el trámite');
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
} 