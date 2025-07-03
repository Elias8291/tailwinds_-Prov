<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\ContactoSolicitante;
use App\Models\ActividadSolicitante;
use App\Models\Solicitante;
use App\Models\Sector;
use App\Models\Actividad;

class DatosGeneralesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Método de prueba para verificar que el controlador está accesible
     */
    public function test(Request $request)
    {
        Log::info('=== TEST DatosGeneralesController ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Controlador accesible',
            'timestamp' => now()->toISOString(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Guarda los datos generales del trámite con validaciones completas
     */
    public function guardar(Request $request)
    {
        try {
                        Log::info('=== INICIO guardar datos generales ===', [
            'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar los datos del formulario usando validaciones robustas
            $validated = $this->validateFormularioData($request);

            // Buscar el trámite
            $tramite = Tramite::find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Verificar permisos
            $user = Auth::user();
            $solicitante = $user->solicitante;
            
            if (!$solicitante || $tramite->solicitante_id != $solicitante->id) {
                throw new \Exception('No tiene permisos para modificar este trámite');
            }

            // Procesar y guardar los datos en transacción
            DB::transaction(function () use ($validated, $tramite, $solicitante) {
                // Actualizar datos del solicitante si es necesario
                if (isset($validated['nombre_completo'])) {
                    $solicitante->update(['nombre_completo' => $validated['nombre_completo']]);
                }
                
                if (isset($validated['razon_social'])) {
                    $solicitante->update(['razon_social' => $validated['razon_social']]);
                }

                // Crear o actualizar DetalleTramite
                $detalleTramite = DetalleTramite::updateOrCreate(
                    ['tramite_id' => $tramite->id],
                    [
                        'giro' => $validated['giro'],
                        'sitio_web' => $validated['pagina_web'] ?? null,
                    ]
                );

                // Obtener contacto existente si hay uno relacionado
                $contactoExistente = null;
                if ($detalleTramite->contacto_id) {
                    $contactoExistente = ContactoSolicitante::find($detalleTramite->contacto_id);
                }

                // Crear o actualizar contacto
                if ($contactoExistente) {
                    $contactoExistente->update([
                        'nombre' => $validated['contacto_nombre'],
                        'puesto' => $validated['contacto_cargo'],
                        'email' => $validated['contacto_correo'],
                        'telefono' => $validated['contacto_telefono'],
                    ]);
                    $contacto = $contactoExistente;
                } else {
                    $contacto = ContactoSolicitante::create([
                        'nombre' => $validated['contacto_nombre'],
                        'puesto' => $validated['contacto_cargo'],
                        'email' => $validated['contacto_correo'],
                        'telefono' => $validated['contacto_telefono'],
                    ]);
                }

                // Actualizar referencia al contacto en el detalle del trámite
                $detalleTramite->update(['contacto_id' => $contacto->id]);

                // Procesar actividades seleccionadas
                $this->procesarActividades($tramite, $validated['actividades_seleccionadas']);

                Log::info('Datos generales guardados exitosamente:', [
                    'tramite_id' => $tramite->id,
                    'detalle_id' => $detalleTramite->id,
                    'contacto_id' => $contacto->id
                ]);
            });

            // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Sección 1: Datos Generales → 2
            $tramite->actualizarProgresoSeccion(2);

            Log::info('✅ Datos generales guardados exitosamente para tramite_id: ' . $validated['tramite_id']);

            return response()->json([
                'success' => true,
                'message' => 'Datos generales guardados correctamente',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación en datos generales:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en la información de datos generales.',
                'errors' => $e->errors(),
                'debug_info' => [
                    'seccion' => 'datos_generales',
                    'timestamp' => now()->toISOString(),
                    'total_errores' => count($e->errors())
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al guardar datos generales:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar los datos generales. Por favor, intente nuevamente.',
                'debug_info' => [
                    'seccion' => 'datos_generales',
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Valida los datos del formulario de datos generales usando validaciones robustas en español
     *
     * @param Request $request La solicitud a validar
     * @return array Los datos validados
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateFormularioData(Request $request)
    {
                // Obtener tipo de persona del usuario actual
        $user = Auth::user();
        $solicitante = $user->solicitante ?? null;
        $tipoPersona = $solicitante?->tipo_persona ?? 'Física';

        $rules = [
            'tramite_id' => [
                'required',
                'integer',
                'exists:tramite,id'
            ],
            'giro' => [
                'required',
                'string',
                'min:10',
                'max:500',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.,;:\-\(\)\/]+$/'
            ],
            'actividades_seleccionadas' => [
                'required',
                'string',
                'min:3'
            ],
            'contacto_nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ],
            'contacto_cargo' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ],
            'contacto_correo' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'contacto_telefono' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/'
            ],
            'pagina_web' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
            ]
        ];

        // Validaciones específicas según tipo de persona
        if ($tipoPersona === 'Física') {
            $rules['nombre_completo'] = [
                'sometimes',
                'required',
                'string',
                'min:5',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ];
            $rules['curp'] = [
                'sometimes',
                'required',
                'string',
                'size:18',
                'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/'
            ];
        } else {
            $rules['razon_social'] = [
                'sometimes',
                'required',
                'string',
                'min:5',
                'max:150',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.,&\-\(\)]+$/'
            ];
        }

        $messages = [
            'tramite_id.required' => 'No se pudo identificar el trámite asociado',
            'tramite_id.integer' => 'El identificador del trámite debe ser un número válido',
            'tramite_id.exists' => 'El trámite especificado no existe o no es válido',
            
            'giro.required' => 'El giro de la empresa es obligatorio',
            'giro.min' => 'El giro debe tener al menos 10 caracteres',
            'giro.max' => 'El giro no puede exceder 500 caracteres',
            'giro.regex' => 'El giro solo puede contener letras, números, espacios y signos de puntuación básicos',
            
            'actividades_seleccionadas.required' => 'Debe seleccionar al menos una actividad económica',
            'actividades_seleccionadas.min' => 'Debe seleccionar actividades económicas válidas',
            
            'contacto_nombre.required' => 'El nombre de la persona de contacto es obligatorio',
            'contacto_nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'contacto_nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'contacto_nombre.regex' => 'El nombre solo puede contener letras, espacios y apostrofes',
            
            'contacto_cargo.required' => 'El cargo de la persona de contacto es obligatorio',
            'contacto_cargo.min' => 'El cargo debe tener al menos 3 caracteres',
            'contacto_cargo.max' => 'El cargo no puede exceder 50 caracteres',
            'contacto_cargo.regex' => 'El cargo solo puede contener letras, espacios y apostrofes',
            
            'contacto_correo.required' => 'El correo electrónico de contacto es obligatorio',
            'contacto_correo.email' => 'El formato del correo electrónico no es válido',
            'contacto_correo.max' => 'El correo electrónico no puede exceder 255 caracteres',
            'contacto_correo.regex' => 'El formato del correo electrónico no es válido',
            
            'contacto_telefono.required' => 'El teléfono de contacto es obligatorio',
            'contacto_telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos numéricos',
            
            'pagina_web.max' => 'La URL de la página web no puede exceder 255 caracteres',
            'pagina_web.regex' => 'El formato de la URL no es válido',
            
            'nombre_completo.required' => 'El nombre completo es obligatorio para persona física',
            'nombre_completo.min' => 'El nombre completo debe tener al menos 5 caracteres',
            'nombre_completo.max' => 'El nombre completo no puede exceder 100 caracteres',
            'nombre_completo.regex' => 'El nombre completo solo puede contener letras, espacios y apostrofes',
            
            'curp.required' => 'La CURP es obligatoria para persona física',
            'curp.size' => 'La CURP debe tener exactamente 18 caracteres',
            'curp.regex' => 'El formato de la CURP no es válido',
            
            'razon_social.required' => 'La razón social es obligatoria para persona moral',
            'razon_social.min' => 'La razón social debe tener al menos 5 caracteres',
            'razon_social.max' => 'La razón social no puede exceder 150 caracteres',
            'razon_social.regex' => 'La razón social solo puede contener letras, números, espacios y signos de puntuación básicos'
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Procesa y guarda las actividades seleccionadas
     */
    private function procesarActividades(Tramite $tramite, $actividadesJson)
    {
        try {
            // Decodificar JSON de actividades
            $actividades = json_decode($actividadesJson, true);
            
            if (!is_array($actividades) || empty($actividades)) {
                throw new \Exception('No se encontraron actividades válidas');
            }

            // Eliminar actividades anteriores
            ActividadSolicitante::where('tramite_id', $tramite->id)->delete();

            // Agregar nuevas actividades
            foreach ($actividades as $actividadData) {
                if (isset($actividadData['id'])) {
                    ActividadSolicitante::create([
                        'tramite_id' => $tramite->id,
                        'actividad_id' => $actividadData['id']
                    ]);
                }
            }

            Log::info('Actividades procesadas exitosamente:', [
                'tramite_id' => $tramite->id,
                'total_actividades' => count($actividades)
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar actividades:', [
                'tramite_id' => $tramite->id,
                'actividades_json' => $actividadesJson,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Error al procesar las actividades económicas');
        }
    }

    /**
     * Obtiene los datos del trámite para mostrar en el formulario
     */
    public function obtenerDatos(Tramite $tramite)
    {
        Log::info('Obteniendo datos del trámite', [
            'tramite_id' => $tramite->id,
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check()
        ]);

        $solicitante = $tramite->solicitante;
        $detalle = $tramite->detalle;
        $contacto = $detalle?->contacto;
        $actividades = $tramite->actividades()->pluck('actividad_id')->toArray();
        $sector = $tramite->actividades()->with('actividad.sector')->first()?->actividad?->sector;

        return [
            'tramite_id' => $tramite->id,
            'tipo_tramite' => $tramite->tipo_tramite,
            'rfc' => $solicitante->rfc ?? '',
            'tipo_persona' => $solicitante->tipo_persona ?? '',
            'nombre_completo' => $solicitante->nombre_completo ?? Auth::user()->name ?? '',
            'razon_social' => $solicitante->razon_social ?? '',
            'curp' => $solicitante->curp ?? '',
            'giro' => $detalle->giro ?? '',
            'sector_id' => $sector?->id,
            'actividades_seleccionadas' => json_encode($actividades),
            'contacto_nombre' => $contacto->nombre ?? '',
            'contacto_cargo' => $contacto->puesto ?? '',
            'contacto_correo' => $contacto->email ?? '',
            'contacto_telefono' => $contacto->telefono ?? '',
            'pagina_web' => $detalle->sitio_web ?? '',
        ];
    }

    /**
     * Guarda o actualiza los detalles del trámite
     */
    private function saveDetalleTramite(Request $request, Tramite $tramite)
    {
        $detalle = DetalleTramite::updateOrCreate(
            ['tramite_id' => $tramite->id],
            [
                'giro' => $request->input('giro'),
                'sitio_web' => $request->input('pagina_web'),
            ]
        );

        Log::info('DetalleTramite guardado/actualizado', [
            'detalle_id' => $detalle->id,
            'tramite_id' => $tramite->id
        ]);

        return $detalle;
    }

    /**
     * Guarda o actualiza el contacto del solicitante
     */
    private function saveContactoSolicitante(Request $request, DetalleTramite $detalle)
    {
        // Obtener contacto existente si hay uno relacionado
        $contactoExistente = null;
        if ($detalle->contacto_id) {
            $contactoExistente = ContactoSolicitante::find($detalle->contacto_id);
        }

        // Crear o actualizar contacto
        if ($contactoExistente) {
            $contactoExistente->update([
                'nombre' => $request->input('contacto_nombre'),
                'puesto' => $request->input('contacto_cargo'),
                'email' => $request->input('contacto_correo'),
                'telefono' => $request->input('contacto_telefono'),
            ]);
            $contacto = $contactoExistente;
        } else {
            $contacto = ContactoSolicitante::create([
                'nombre' => $request->input('contacto_nombre'),
                'puesto' => $request->input('contacto_cargo'),
                'email' => $request->input('contacto_correo'),
                'telefono' => $request->input('contacto_telefono'),
            ]);
        }

        // Asociar contacto con detalle del trámite
        $detalle->update(['contacto_id' => $contacto->id]);

        Log::info('ContactoSolicitante guardado/actualizado', [
            'contacto_id' => $contacto->id,
            'detalle_id' => $detalle->id
        ]);

        return $contacto;
    }

    /**
     * Obtiene actividades para AJAX
     */
    public function obtenerActividades()
    {
        try {
            $actividades = Actividad::with('sector:id,nombre')
                ->select('id', 'nombre', 'sector_id')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $actividades
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actividades'
            ], 500);
        }
    }

    /**
     * Muestra los datos generales de un trámite
     */
    public function mostrar(Tramite $tramite)
    {
        try {
            // Verificar permisos
            $user = Auth::user();
            $solicitante = $user->solicitante;
            
            if (!$solicitante || $tramite->solicitante_id != $solicitante->id) {
                abort(403, 'No tiene permisos para ver este trámite');
            }

            $sectores = Sector::all();
            $actividades = $tramite->actividades()->with('actividad')->get()->pluck('actividad');
            
            $datosTramite = $this->obtenerDatos($tramite);

            return view('revision.datos-generales', compact(
                'tramite',
                'datosTramite', 
                'sectores',
                'actividades'
            ));

        } catch (\Exception $e) {
            Log::error('Error al mostrar datos generales', [
                'tramite_id' => $tramite->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('revision.index')
                ->with('error', 'Error al cargar los datos del trámite');
        }
    }

    /**
     * Valida que se hayan seleccionado actividades válidas
     */
    private function validateActividadesSeleccionadas(Request $request)
    {
        $actividadesJson = $request->input('actividades_seleccionadas');
        
        if (empty($actividadesJson)) {
            throw new \Exception('Debe seleccionar al menos una actividad económica');
        }

        $actividades = json_decode($actividadesJson, true);
        
        if (!is_array($actividades) || empty($actividades)) {
            throw new \Exception('Las actividades seleccionadas no tienen un formato válido');
        }

        // Validar que cada actividad tenga un ID válido
        foreach ($actividades as $actividad) {
            if (!isset($actividad['id']) || !is_numeric($actividad['id'])) {
                throw new \Exception('Una o más actividades seleccionadas no son válidas');
            }
            
            // Verificar que la actividad existe en la base de datos
            if (!Actividad::find($actividad['id'])) {
                throw new \Exception('Una o más actividades seleccionadas no existen en el sistema');
            }
        }

        return true;
    }
}