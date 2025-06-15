<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\ContactoSolicitante;
use App\Models\ActividadSolicitante;
use App\Models\Direccion;
use App\Models\Asentamiento;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\DatosGeneralesController;

class TramiteController extends Controller
{
    public function index()
    {
        $tramites = [
            [
                'tipo' => 'inscripcion',
                'nombre' => 'Inscripción',
                'descripcion' => 'Registro inicial en el padrón de proveedores',
                'icono' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                'color' => 'blue'
            ],
            [
                'tipo' => 'renovacion',
                'nombre' => 'Renovación',
                'descripcion' => 'Renovación anual de registro en el padrón',
                'icono' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                'color' => 'green'
            ],
            [
                'tipo' => 'actualizacion',
                'nombre' => 'Actualización',
                'descripcion' => 'Actualización de datos o documentos',
                'icono' => 'M4 4v16l12-6-12-6zm12 6v10m4-16v12',
                'color' => 'purple'
            ]
        ];
        
        return view('tramites.index', compact('tramites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($tipoTramite, $tramiteId, $pasoInicial = null)
    {
        try {
            // Buscar el trámite con todas las relaciones necesarias
            $tramite = Tramite::with([
                'solicitante',
                'detalleTramite.direccion.asentamiento.localidad.municipio.estado',
                'detalleTramite.contacto',
                'actividades'
            ])->find($tramiteId);

            if (!$tramite) {
                return redirect()->route('tramites.index')->with('error', 'Trámite no encontrado.');
            }

            $solicitante = $tramite->solicitante;
            if (!$solicitante) {
                return redirect()->route('tramites.index')->with('error', 'Solicitante no encontrado.');
            }

            Log::info('Cargando formulario de trámite:', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tipoTramite,
                'solicitante_id' => $solicitante->id,
                'tiene_detalle' => $tramite->detalleTramite ? 'SI' : 'NO',
                'direccion_id' => $tramite->detalleTramite->direccion_id ?? 'NULL'
            ]);

            $tipos_tramite = [
                'inscripcion' => 'Inscripción',
                'renovacion' => 'Renovación',
                'actualizacion' => 'Actualización'
            ];

            $datosExistentes = [];

            // Cargar datos existentes si el trámite ya tiene progreso
            if ($tramite->progreso_tramite >= 2) {
                // Cargar datos de la sección 1 (datos generales)
                if ($tramite->detalleTramite) {
                    $datosExistentes['razon_social'] = $tramite->detalleTramite->razon_social;
                    $datosExistentes['objeto_social'] = $solicitante->objeto_social;
                    
                    if ($tramite->detalleTramite->contacto) {
                        $datosExistentes['contacto_nombre'] = $tramite->detalleTramite->contacto->nombre;
                        $datosExistentes['contacto_puesto'] = $tramite->detalleTramite->contacto->puesto;
                        $datosExistentes['contacto_correo'] = $tramite->detalleTramite->contacto->correo;
                        $datosExistentes['contacto_telefono'] = $tramite->detalleTramite->contacto->telefono;
                    }
                }
                
                // Cargar actividades seleccionadas
                $actividades = ActividadSolicitante::where('tramite_id', $tramite->id)->get();
                $datosExistentes['actividades_seleccionadas'] = $actividades->pluck('actividad_id')->toArray();
            }

            // Obtener datos usando los controladores especializados
            $datosGeneralesController = new \App\Http\Controllers\Formularios\DatosGeneralesController();
            $domicilioController = new DomicilioController();
            
            // Preparar datos base del trámite
            $datosTramiteBase = [
                'tipo_tramite' => $tipoTramite,
                'titulo' => $tipos_tramite[$tipoTramite],
                'rfc' => $solicitante->rfc,
                'tipo_persona' => $solicitante->tipo_persona,
                'curp' => $solicitante->curp,
                'mostrar_razon_social' => $tipoTramite !== 'inscripcion',
                'tramite_id' => $tramite->id,
                'progreso_tramite' => $tramite->progreso_tramite,
                'paso_inicial' => $pasoInicial ?? ($tramite->progreso_tramite >= 2 ? 2 : 1),
                'datos_existentes' => $datosExistentes
            ];

            // Obtener datos completos de datos generales si el trámite ya está en progreso
            if ($tramite->progreso_tramite >= 1) {
                // Siempre intentar cargar datos existentes, aunque el progreso sea 1
                $datosGeneralesCompletos = $datosGeneralesController->obtenerDatos($tramite);
                $datosTramite = array_merge($datosTramiteBase, $datosGeneralesCompletos);
            } else {
                $datosTramite = $datosTramiteBase;
            }

            // Obtener datos de domicilio usando la cadena: tramite_id → detalle_tramite → direccion_id → codigo_postal
            $datosDomicilio = $domicilioController->obtenerDatos($tramite);
            
            // Obtener código postal específicamente usando DetalleTramiteController
            $detalleTramiteController = new \App\Http\Controllers\DetalleTramiteController();
            $codigoPostalDomicilio = $detalleTramiteController->getCodigoPostalByTramiteId($tramite->id);
            


            return view("tramites.create", [
                'tramite' => $tramite,
                'solicitante' => $solicitante,
                'datosTramite' => $datosTramite,
                'datosDomicilio' => $datosDomicilio,
                'codigoPostalDomicilio' => $codigoPostalDomicilio
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de trámite:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra un paso específico del trámite
     */
    public function mostrarPaso($tramiteId, $paso)
    {
        $tramite = Tramite::findOrFail($tramiteId);
        $tipoTramite = strtolower($tramite->tipo_tramite);
        
        return $this->create($tipoTramite, $tramiteId, $paso);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_tramite' => 'required|in:inscripcion,renovacion,actualizacion',
            'nombre_solicitante' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'descripcion' => 'required|string',
            'documentos.*' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        // Aquí irá la lógica para guardar el trámite
        // Por ahora solo redireccionamos con un mensaje de éxito
        return redirect()->back()->with('success', '¡Trámite enviado exitosamente!');
    }

    /**
     * Inicia un trámite (inscripción, renovación, actualización) para un RFC dado.
     * Si el usuario o solicitante no existen, los crea y los asocia.
     * Redirige a la vista de términos y condiciones.
     */
    public function iniciarTramite(Request $request)
    {
        try {
            Log::info('Iniciando trámite con datos:', $request->all());

            $validated = $request->validate([
                'rfc' => 'required|string|max:13',
                'tipo_tramite' => 'required|in:inscripcion,renovacion,actualizacion',
                'tipo_persona' => 'required|in:Física,Moral,Fisica,FÍSICA,MORAL,fisica,moral',
            ]);

            // Normalize tipo_persona value
            $validated['tipo_persona'] = ucfirst(strtolower($validated['tipo_persona']));
            if ($validated['tipo_persona'] === 'Fisica') {
                $validated['tipo_persona'] = 'Física';
            }

            Log::info('Datos validados correctamente', $validated);

            // Buscar o crear solicitante sin usuario asociado
            $solicitante = Solicitante::firstOrCreate(
                ['rfc' => $validated['rfc']],
                [
                    'tipo_persona' => $validated['tipo_persona'],
                ]
            );

            Log::info('Solicitante procesado:', ['solicitante_id' => $solicitante->id]);

            // Obtener o crear el trámite
            $tramite = null;
            Log::info('Procesando trámite...', ['tramite_id' => $request->tramite_id, 'tipo_tramite' => $request->tipo_tramite]);
            
            if ($request->tramite_id) {
                Log::info('Buscando trámite existente...');
                $tramite = Tramite::find($request->tramite_id);
                if ($tramite) {
                    $tramite->update(['solicitante_id' => $solicitante->id]);
                    Log::info('Trámite encontrado y actualizado');
                } else {
                    Log::warning('Trámite no encontrado con ID:', ['tramite_id' => $request->tramite_id]);
                }
            } 
            
            if (!$tramite && $request->tipo_tramite) {
                Log::info('Creando nuevo trámite...');
                $tramite = Tramite::create([
                    'solicitante_id' => $solicitante->id,
                    'tipo_tramite' => ucfirst($validated['tipo_tramite']),
                    'estado' => 'Pendiente',
                    'progreso_tramite' => 0, // Inicia en 0 y aumenta al completar secciones
                ]);
                Log::info('Nuevo trámite creado:', ['tramite_id' => $tramite->id]);
            }

            if (!$tramite) {
                throw new \Exception('No se pudo crear o encontrar el trámite');
            }

            // Preparar datos para redirección al formulario principal
            $redirectUrl = route('tramites.create', [
                'tipo_tramite' => $validated['tipo_tramite'],
                'tramite' => $tramite->id
            ]);
            
            Log::info('URL de redirección generada:', ['url' => $redirectUrl]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trámite iniciado correctamente',
                    'redirect' => $redirectUrl
                ]);
            }

            return redirect($redirectUrl);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            $errorMessages = collect($e->errors())->flatten()->implode(', ');
            
            return $request->expectsJson()
                ? response()->json(['error' => $errorMessages], 422)
                : back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Error al procesar el trámite:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            $errorMessage = 'Error al procesar el trámite: ' . $e->getMessage();
            
            return $request->expectsJson()
                ? response()->json(['error' => $errorMessage], 500)
                : back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Create a new tramite record for registration
     *
     * @param \App\Models\Solicitante $solicitante
     * @param array $data
     * @return Tramite
     */
    public function createForRegistration(\App\Models\Solicitante $solicitante, array $data): \App\Models\Tramite
    {
        try {
            $tramite = \App\Models\Tramite::create([
                'solicitante_id' => $solicitante->id,
                'tipo_tramite' => $data['tipo_tramite'] ?? 'Inscripcion',
                'estado' => $data['estado'] ?? 'Pendiente',
                'progreso_tramite' => $data['progreso_tramite'] ?? 0,
                'fecha_inicio' => $data['fecha_inicio'] ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\Log::info('Trámite creado exitosamente', [
                'tramite_id' => $tramite->id,
                'solicitante_id' => $solicitante->id,
                'tipo_tramite' => $tramite->tipo_tramite
            ]);

            return $tramite;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al crear trámite', [
                'error' => $e->getMessage(),
                'solicitante_id' => $solicitante->id,
                'data' => $data
            ]);
            throw new \Exception('Error al crear el trámite: ' . $e->getMessage());
        }
    }

    /**
     * Guarda los datos generales del solicitante y del contacto
     */
    public function guardarDatosGenerales(Request $request)
    {
        try {
            Log::info('=== INICIO guardarDatosGenerales ===');
            Log::info('Request completo:', ['data' => $request->all()]);
            Log::info('Headers:', ['headers' => $request->headers->all()]);
            Log::info('Method:', ['method' => $request->method()]);
            
            // Validar los datos del formulario
            $validated = $request->validate([
                'tipo_persona' => 'required|in:Física,Moral',
                'rfc' => 'required|string|max:13',
                'curp' => 'nullable|string|max:18',
                'razon_social' => 'required|string|max:100',
                'objeto_social' => 'required|string|max:500',
                'sector_id' => 'nullable|integer',
                'actividades_seleccionadas' => 'nullable|string',
                'contacto_nombre' => 'required|string|max:40',
                'contacto_cargo' => 'required|string|max:50',
                'contacto_correo' => 'required|email|max:255',
                'contacto_telefono' => 'required|string|max:10',
                'tramite_id' => 'nullable|integer',
                'tipo_tramite' => 'nullable|in:inscripcion,renovacion,actualizacion',
            ]);

            Log::info('Datos validados para guardar datos generales:', ['validated' => $validated]);

            DB::beginTransaction();

            // Buscar o crear el solicitante
            Log::info('Buscando solicitante con RFC:', ['rfc' => $validated['rfc']]);
            $solicitante = Solicitante::where('rfc', $validated['rfc'])->first();
            
            if ($solicitante) {
                Log::info('Solicitante encontrado, actualizando...');
                // Actualizar solicitante existente
                $solicitante->update([
                    'tipo_persona' => $validated['tipo_persona'],
                    'curp' => $validated['tipo_persona'] === 'Física' ? $validated['curp'] : null,
                    'objeto_social' => $validated['objeto_social'],
                ]);
                Log::info('Solicitante actualizado:', ['solicitante_id' => $solicitante->id]);
            } else {
                Log::info('Solicitante no encontrado, creando nuevo...');
                // Crear nuevo solicitante
                $solicitante = Solicitante::create([
                    'rfc' => $validated['rfc'],
                    'tipo_persona' => $validated['tipo_persona'],
                    'curp' => $validated['tipo_persona'] === 'Física' ? $validated['curp'] : null,
                    'objeto_social' => $validated['objeto_social'],
                ]);
                Log::info('Nuevo solicitante creado:', ['solicitante_id' => $solicitante->id]);
            }

            // Obtener o crear el trámite
            $tramite = null;
            Log::info('Procesando trámite...', ['tramite_id' => $request->tramite_id, 'tipo_tramite' => $request->tipo_tramite]);
            
            if ($request->tramite_id) {
                Log::info('Buscando trámite existente...');
                $tramite = Tramite::find($request->tramite_id);
                if ($tramite) {
                    $tramite->update(['solicitante_id' => $solicitante->id]);
                    Log::info('Trámite encontrado y actualizado');
                } else {
                    Log::warning('Trámite no encontrado con ID:', ['tramite_id' => $request->tramite_id]);
                }
            } 
            
            if (!$tramite && $request->tipo_tramite) {
                Log::info('Creando nuevo trámite...');
                $tramite = Tramite::create([
                    'solicitante_id' => $solicitante->id,
                    'tipo_tramite' => ucfirst($validated['tipo_tramite']),
                    'estado' => 'Pendiente',
                    'progreso_tramite' => 2, // Marca como completada la primera sección
                ]);
                Log::info('Nuevo trámite creado:', ['tramite_id' => $tramite->id]);
            }

            if (!$tramite) {
                throw new \Exception('No se pudo crear o encontrar el trámite');
            }

            // Crear o actualizar el contacto
            $contacto = ContactoSolicitante::create([
                'nombre' => $validated['contacto_nombre'],
                'puesto' => $validated['contacto_cargo'],
                'telefono' => $validated['contacto_telefono'],
                'email' => $validated['contacto_correo'],
            ]);

            Log::info('Contacto creado:', ['contacto_id' => $contacto->id]);

            // Actualizar detalles del trámite
            $detalleTramite = \App\Models\DetalleTramite::updateOrCreate(
                ['tramite_id' => $tramite->id],
                [
                    'razon_social' => $validated['razon_social'],
                    'email' => $validated['contacto_correo'],
                    'telefono' => $validated['contacto_telefono'],
                    'contacto_id' => $contacto->id,
                ]
            );

            // Procesar actividades seleccionadas
            $actividades = [];
            if (!empty($validated['actividades_seleccionadas'])) {
                $actividades = json_decode($validated['actividades_seleccionadas'], true);
                if (!empty($actividades)) {
                    // Eliminar actividades previas del trámite
                    ActividadSolicitante::where('tramite_id', $tramite->id)->delete();
                    
                    // Crear nuevas relaciones de actividades
                    foreach ($actividades as $actividadId) {
                        ActividadSolicitante::create([
                            'tramite_id' => $tramite->id,
                            'actividad_id' => $actividadId,
                        ]);
                    }
                    Log::info('Actividades asociadas:', ['tramite_id' => $tramite->id, 'actividades' => $actividades]);
                }
            }

            // Actualizar el progreso del trámite siempre que se guarden datos generales
            $tramite->update(['progreso_tramite' => 2]);
            Log::info('Progreso del trámite actualizado a 2');

            DB::commit();

            Log::info('Datos generales guardados exitosamente:', [
                'solicitante_id' => $solicitante->id,
                'tramite_id' => $tramite->id
            ]);

            // Redirigir al formulario principal con paso 2 activo (domicilio)
            return redirect()->route('tramites.create', [
                'tipo_tramite' => strtolower($tramite->tipo_tramite),
                'tramite' => $tramite->id,
                'step' => 2
            ])->with('success', 'Datos generales guardados correctamente. Continúe con el domicilio.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en datos generales:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos generales:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Error al guardar los datos: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra el formulario de datos generales para un trámite
     * REDIRIGE al formulario principal para mantener el diseño hermoso
     */
    public function mostrarDatosGenerales(Request $request)
    {
        try {
            $tramiteId = $request->get('tramite_id');
            $tipoTramite = $request->get('tipo_tramite', 'inscripcion');

            if (!$tramiteId) {
                return redirect()->route('tramites.index')->with('error', 'No se encontró el trámite.');
            }

            // Redirigir al formulario principal con el diseño hermoso
            return redirect()->route('tramites.create', [
                'tipo_tramite' => $tipoTramite,
                'tramite' => $tramiteId
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de datos generales:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de domicilio para un trámite
     */
    public function mostrarDomicilio(Request $request)
    {
        try {
            $tramiteId = $request->get('tramite_id');
            $tipoTramite = $request->get('tipo_tramite', 'inscripcion');

            if (!$tramiteId) {
                return redirect()->route('tramites.index')->with('error', 'No se encontró el trámite.');
            }

            // Buscar el trámite con las relaciones necesarias
            $tramite = Tramite::with(['solicitante', 'detalleTramite.direccion.asentamiento'])
                ->find($tramiteId);

            if (!$tramite) {
                return redirect()->route('tramites.index')->with('error', 'Trámite no encontrado.');
            }

            // Inicializar datos del domicilio
            $datosDomicilio = [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => $tipoTramite,
            ];

            // Si ya existe dirección, cargar los datos
            if ($tramite->detalleTramite && $tramite->detalleTramite->direccion) {
                $direccion = $tramite->detalleTramite->direccion;
                $datosDomicilio = array_merge($datosDomicilio, [
                    'codigo_postal' => $direccion->codigo_postal,
                    'calle' => $direccion->calle,
                    'numero_exterior' => $direccion->numero_exterior,
                    'numero_interior' => $direccion->numero_interior,
                    'entre_calle_1' => $direccion->entre_calle_1,
                    'entre_calle_2' => $direccion->entre_calle_2,
                    'asentamiento_id' => $direccion->asentamiento_id,
                ]);
            }

    

            return view('tramites.domicilio', compact('datosDomicilio', 'tramite'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de domicilio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guarda los datos de domicilio del trámite
     */
    public function guardarDomicilio(Request $request)
    {
        try {
    
            Log::info('Request completo:', ['data' => $request->all()]);

            // Validar los datos del formulario
            $validated = $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id',
                'codigo_postal' => 'required|integer|digits:5',
                'colonia' => 'required|integer', // asentamiento_id
                'calle' => 'required|string|max:100',
                'numero_exterior' => 'required|string|max:10',
                'numero_interior' => 'nullable|string|max:10',
                'entre_calle_1' => 'required|string|max:100',
                'entre_calle_2' => 'required|string|max:100',
            ]);

            Log::info('Datos validados para guardar domicilio:', ['validated' => $validated]);

            DB::beginTransaction();

            // Buscar el trámite
            $tramite = Tramite::with('detalleTramite')->find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Crear o actualizar la dirección
            $direccionData = [
                'codigo_postal' => $validated['codigo_postal'],
                'asentamiento_id' => $validated['colonia'],
                'calle' => $validated['calle'],
                'numero_exterior' => $validated['numero_exterior'],
                'numero_interior' => $validated['numero_interior'],
                'entre_calle_1' => $validated['entre_calle_1'],
                'entre_calle_2' => $validated['entre_calle_2'],
            ];

            $direccion = null;
            
            // Si ya existe una dirección en el detalle del trámite, actualizarla
            if ($tramite->detalleTramite && $tramite->detalleTramite->direccion_id) {
                $direccion = Direccion::find($tramite->detalleTramite->direccion_id);
                if ($direccion) {
                    $direccion->update($direccionData);
                    Log::info('Dirección actualizada:', ['direccion_id' => $direccion->id]);
                }
            }

            // Si no existe dirección, crear una nueva
            if (!$direccion) {
                $direccion = Direccion::create($direccionData);
                Log::info('Nueva dirección creada:', ['direccion_id' => $direccion->id]);

                // Actualizar el detalle del trámite con la nueva dirección
                if ($tramite->detalleTramite) {
                    $tramite->detalleTramite->update(['direccion_id' => $direccion->id]);
                }
            }

            // Actualizar el progreso del trámite
            $tramite->update(['progreso_tramite' => 3]);
            Log::info('Progreso del trámite actualizado a 3');

            DB::commit();

            Log::info('Datos de domicilio guardados exitosamente:', [
                'tramite_id' => $tramite->id,
                'direccion_id' => $direccion->id
            ]);

            // Redirigir al siguiente formulario (o al mismo con mensaje de éxito)
            return redirect()->route('tramites.domicilio', [
                'tramite_id' => $tramite->id,
                'tipo_tramite' => strtolower($tramite->tipo_tramite)
            ])->with('success', 'Datos de domicilio guardados correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en domicilio:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos de domicilio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Error al guardar los datos: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Guarda los datos de domicilio desde el formulario principal
     */
    public function guardarDomicilioFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarDomicilioFormulario ===');
            Log::info('Request completo:', ['data' => $request->all()]);

            // Validar los datos del formulario
            $validated = $request->validate([
                'tramite_id' => 'required|integer|exists:tramite,id',
                'codigo_postal' => 'required|integer|digits:5',
                'colonia' => 'required|integer', // asentamiento_id
                'calle' => 'required|string|max:100',
                'numero_exterior' => 'required|string|max:10',
                'numero_interior' => 'nullable|string|max:10',
                'entre_calle_1' => 'required|string|max:100',
                'entre_calle_2' => 'required|string|max:100',
            ]);

            Log::info('Datos validados para guardar domicilio:', ['validated' => $validated]);

            DB::beginTransaction();

            // Buscar el trámite
            $tramite = Tramite::with('detalleTramite')->find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Crear o actualizar la dirección
            $direccionData = [
                'codigo_postal' => $validated['codigo_postal'],
                'asentamiento_id' => $validated['colonia'],
                'calle' => $validated['calle'],
                'numero_exterior' => $validated['numero_exterior'],
                'numero_interior' => $validated['numero_interior'],
                'entre_calle_1' => $validated['entre_calle_1'],
                'entre_calle_2' => $validated['entre_calle_2'],
            ];

            $direccion = null;
            
            // Si ya existe una dirección en el detalle del trámite, actualizarla
            if ($tramite->detalleTramite && $tramite->detalleTramite->direccion_id) {
                $direccion = Direccion::find($tramite->detalleTramite->direccion_id);
                if ($direccion) {
                    $direccion->update($direccionData);
                    Log::info('Dirección actualizada:', ['direccion_id' => $direccion->id]);
                }
            }

            // Si no existe dirección, crear una nueva
            if (!$direccion) {
                $direccion = Direccion::create($direccionData);
                Log::info('Nueva dirección creada:', ['direccion_id' => $direccion->id]);

                // Actualizar el detalle del trámite con la nueva dirección
                if ($tramite->detalleTramite) {
                    $tramite->detalleTramite->update(['direccion_id' => $direccion->id]);
                }
            }

            // Actualizar el progreso del trámite
            $tramite->update(['progreso_tramite' => 3]);
            Log::info('Progreso del trámite actualizado a 3');

            DB::commit();

            Log::info('Datos de domicilio guardados exitosamente:', [
                'tramite_id' => $tramite->id,
                'direccion_id' => $direccion->id
            ]);

            // Devolver respuesta JSON para el formulario
            return response()->json([
                'success' => true,
                'message' => 'Datos de domicilio guardados correctamente.',
                'step' => 3
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en domicilio:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos de domicilio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los datos: ' . $e->getMessage()
            ], 500);
        }
    }
} 