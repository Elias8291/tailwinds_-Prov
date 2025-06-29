<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\SeccionRevision;
use App\Models\Proveedor;
use App\Models\DocumentoSolicitante;
use App\Models\Documento;
use App\Models\Notificacion;
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
                        'observaciones' => $doc->observaciones
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
     * Aprobar todo el trámite y crear proveedor automáticamente
     */
    public function aprobarTodo(Request $request, Tramite $tramite)
    {
        try {
            DB::beginTransaction();

            // Determinar qué secciones aprobar según el tipo de persona
            $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
            $seccionesAAprobar = $tipoPersona === 'Moral' ? [1, 2, 3, 4, 5, 6] : [1, 2, 3]; // 3 es documentos para persona física
            
            Log::info('Iniciando aprobación completa de trámite:', [
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

            // ✅ CREAR AUTOMÁTICAMENTE EL PROVEEDOR
            $proveedor = Proveedor::crearDesdeTramiite($tramite);

            // 🔄 CAMBIAR ROL DE SOLICITANTE A PROVEEDOR
            if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
                try {
                    $usuario = \App\Models\User::find($tramite->solicitante->usuario_id);
                    if ($usuario) {
                        // Remover el rol de Solicitante y asignar el rol de Proveedor
                        $usuario->removeRole('Solicitante');
                        $usuario->assignRole('Proveedor');
                        
                        Log::info('Usuario cambió de rol automáticamente:', [
                            'usuario_id' => $usuario->id,
                            'tramite_id' => $tramite->id,
                            'rol_anterior' => 'Solicitante',
                            'rol_nuevo' => 'Proveedor',
                            'proveedor_pv' => $proveedor->pv
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error al cambiar rol de usuario:', [
                        'tramite_id' => $tramite->id,
                        'usuario_id' => $tramite->solicitante->usuario_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // 🔔 CREAR NOTIFICACIÓN PARA EL USUARIO
            if ($tramite->solicitante && $tramite->solicitante->usuario_id) {
                try {
                    Notificacion::crearParaUsuario(
                        '¡Felicitaciones! Ya eres Proveedor Oficial',
                        "Su trámite #" . str_pad($tramite->id, 6, '0', STR_PAD_LEFT) . " ha sido aprobado completamente. " .
                        "¡Bienvenido al Padrón de Proveedores! Su código oficial es: " . $proveedor->pv . ". " .
                        "Ahora puede participar en licitaciones del Gobierno del Estado de Oaxaca y gestionar renovaciones desde su panel.",
                        'Informativo',
                        $tramite->solicitante->usuario_id
                    );
                    
                    Log::info('Notificación de aprobación enviada:', [
                        'tramite_id' => $tramite->id,
                        'usuario_id' => $tramite->solicitante->usuario_id,
                        'proveedor_pv' => $proveedor->pv
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al crear notificación de aprobación:', [
                        'tramite_id' => $tramite->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Trámite aprobado completamente y proveedor creado:', [
                'tramite_id' => $tramite->id,
                'revisor' => Auth::id(),
                'tipo_persona' => $tipoPersona,
                'proveedor_pv' => $proveedor->pv,
                'proveedor_estado' => $proveedor->estado
            ]);

            return redirect()->route('revision.index')->with('success', 
                'Trámite aprobado completamente. Proveedor creado con código: ' . $proveedor->pv);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al aprobar todo el trámite:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al aprobar el trámite: ' . $e->getMessage());
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