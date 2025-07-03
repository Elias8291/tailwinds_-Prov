<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use App\Models\Asentamiento;
use App\Http\Controllers\DetalleTramiteController;

class DomicilioController extends Controller
{
    /**
     * Guarda los datos de domicilio del trámite
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return bool Retorna true si la operación fue exitosa
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        Log::info('=== INICIO guardar domicilio ===', [
            'tramite_id' => $tramite->id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        try {
            DB::beginTransaction();

            $direccion = $this->saveDireccion($request, $tramite);
            $this->updateDetalleTramite($tramite, $direccion);

            DB::commit();

            // Actualizar progreso DESPUÉS de confirmar la transacción
            $this->updateProgreso($tramite);

            Log::info('✅ Domicilio guardado exitosamente', [
                'tramite_id' => $tramite->id,
                'direccion_id' => $direccion->id,
                'codigo_postal' => $direccion->codigo_postal
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error al guardar domicilio:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Guarda los datos de domicilio desde AJAX
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarFormulario domicilio ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar los datos del formulario
            $validated = $this->validateDomicilioData($request);

            // Buscar el trámite
            $tramite = Tramite::with('detalleTramite')->find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Guardar los datos usando el método de la clase
            $this->guardar($request, $tramite);

            // Determinar el siguiente paso según el tipo de persona
            $tipoPersona = $tramite->solicitante?->tipo_persona ?? null;
            $nextStep = ($tipoPersona === 'Física') ? 3 : 3; // Para física salta a documentos (3), para moral sigue a constitución (3)

            return response()->json([
                'success' => true,
                'message' => 'Datos de domicilio guardados correctamente.',
                'step' => $nextStep,
                'tipo_persona' => $tipoPersona,
                'progreso_actualizado' => 3
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Error de validación en domicilio:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en el formulario de domicilio.',
                'errors' => $e->errors(),
                'debug_info' => [
                    'seccion' => 'domicilio',
                    'timestamp' => now()->toISOString(),
                    'total_errores' => count($e->errors())
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al guardar datos de domicilio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar los datos de domicilio. Por favor, intente nuevamente.',
                'debug_info' => [
                    'seccion' => 'domicilio',
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Obtiene los datos de domicilio del trámite para mostrar en el formulario
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return array Los datos de domicilio formateados
     */
    public function obtenerDatos(Tramite $tramite)
    {
        try {
            Log::info('=== INICIO obtenerDatos domicilio ===', [
                'tramite_id' => $tramite->id,
                'user_id' => Auth::id()
            ]);

            // Usar el DetalleTramiteController para obtener datos completos
            $detalleTramiteController = new DetalleTramiteController();
            $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramite->id);
            
            if ($datosDomicilio && $datosDomicilio['codigo_postal']) {
                Log::info('✅ Datos de domicilio encontrados:', [
                    'tramite_id' => $tramite->id,
                    'codigo_postal' => $datosDomicilio['codigo_postal'],
                    'direccion_id' => $datosDomicilio['direccion_id'] ?? null
                ]);

                return $datosDomicilio;
            }
            
            // Si no hay datos, retornar estructura básica
            Log::info('⚠️ No se encontraron datos de domicilio, retornando estructura básica', [
                'tramite_id' => $tramite->id
            ]);
            
            return [
                'tramite_id' => $tramite->id,
                'codigo_postal' => null,
                'estado' => null,
                'municipio' => null,
                'colonia' => null,
                'asentamiento_id' => null,
                'calle' => null,
                'numero_exterior' => null,
                'numero_interior' => null,
                'entre_calle_1' => null,
                'entre_calle_2' => null,
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Error al obtener datos de domicilio:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'tramite_id' => $tramite->id,
                'codigo_postal' => null,
                'estado' => null,
                'municipio' => null,
                'colonia' => null,
                'asentamiento_id' => null,
                'calle' => null,
                'numero_exterior' => null,
                'numero_interior' => null,
                'entre_calle_1' => null,
                'entre_calle_2' => null,
            ];
        }
    }

    /**
     * Guarda o actualiza los datos de la dirección
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return Direccion El modelo de dirección actualizado
     */
    private function saveDireccion(Request $request, Tramite $tramite)
    {
        $direccionData = [
            'codigo_postal' => str_pad($request->input('codigo_postal'), 5, '0', STR_PAD_LEFT), // Asegurar 5 dígitos con 0 inicial
            'asentamiento_id' => $request->input('colonia'),
            'calle' => $request->input('calle'),
            'numero_exterior' => $request->input('numero_exterior'),
            'numero_interior' => $request->input('numero_interior'),
            'entre_calle_1' => $request->input('entre_calle_1'),
            'entre_calle_2' => $request->input('entre_calle_2'),
        ];

        Log::info('📍 Preparando datos de dirección:', $direccionData);

        // Buscar si ya existe una dirección asociada al trámite
        $direccion = null;
        if ($tramite->detalleTramite && $tramite->detalleTramite->direccion_id) {
            $direccion = Direccion::find($tramite->detalleTramite->direccion_id);
            Log::info('🔄 Actualizando dirección existente:', ['direccion_id' => $direccion->id]);
        }

        // Si existe la dirección, actualizarla; si no, crear una nueva
        if ($direccion) {
            $direccion->update($direccionData);
            Log::info('✅ Dirección actualizada exitosamente');
        } else {
            $direccion = Direccion::create($direccionData);
            Log::info('✅ Nueva dirección creada:', ['direccion_id' => $direccion->id]);
        }

        return $direccion;
    }

    /**
     * Actualiza o crea el detalle del trámite con la dirección
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @param Direccion $direccion El modelo de dirección asociado
     * @return void
     */
    private function updateDetalleTramite(Tramite $tramite, Direccion $direccion)
    {
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
        $detalle->direccion_id = $direccion->id;
        $detalle->save();

        Log::info('✅ DetalleTramite actualizado:', [
            'detalle_tramite_id' => $detalle->id,
            'direccion_id' => $direccion->id
        ]);
    }

    /**
     * Actualiza el progreso del trámite
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return void
     */
    private function updateProgreso(Tramite $tramite)
    {
        // Obtener el tipo de persona del solicitante
        $tipoPersona = $tramite->solicitante?->tipo_persona ?? null;
        
        if ($tipoPersona === 'Física') {
            // Para persona física: saltar directamente a sección 3 (documentos)
            // porque no necesita constitución, accionistas ni apoderado legal
            $tramite->actualizarProgresoSeccion(3);
            Log::info('✅ Progreso actualizado para Persona Física - Saltando a sección 3:', [
                'tramite_id' => $tramite->id,
                'tipo_persona' => $tipoPersona,
                'progreso_actual' => $tramite->progreso_tramite
            ]);
        } else {
            // Para persona moral: avanzar a sección 3 (constitución)
            $tramite->actualizarProgresoSeccion(3);
            Log::info('✅ Progreso actualizado para Persona Moral - Sección 3 (Constitución):', [
            'tramite_id' => $tramite->id,
                'tipo_persona' => $tipoPersona,
            'progreso_actual' => $tramite->progreso_tramite
        ]);
        }
    }

    /**
     * Obtiene la información completa de ubicación por código postal
     *
     * @param string $codigoPostal El código postal
     * @return array Los datos de ubicación
     */
    public function obtenerDatosPorCP($codigoPostal)
    {
        try {
            Log::info('🔍 Buscando datos por código postal:', ['codigo_postal' => $codigoPostal]);

            // Asegurar que el código postal tenga 5 dígitos con 0 inicial
            $codigoPostalFormateado = str_pad($codigoPostal, 5, '0', STR_PAD_LEFT);

            $asentamientos = Asentamiento::with(['localidad.municipio.estado'])
                ->where('codigo_postal', $codigoPostalFormateado)
                ->get();

            if ($asentamientos->isEmpty()) {
                Log::warning('⚠️ No se encontraron asentamientos para el código postal:', [
                    'codigo_postal' => $codigoPostalFormateado
                ]);

                return [
                    'success' => false,
                    'message' => 'No se encontraron datos para este código postal'
                ];
            }

            $primerAsentamiento = $asentamientos->first();
            $estado = $primerAsentamiento->localidad?->municipio?->estado?->nombre ?? 'No disponible';
            $municipio = $primerAsentamiento->localidad?->municipio?->nombre ?? 'No disponible';

            Log::info('✅ Datos encontrados:', [
                'codigo_postal' => $codigoPostalFormateado,
                'estado' => $estado,
                'municipio' => $municipio,
                'total_asentamientos' => $asentamientos->count()
            ]);

            return [
                'success' => true,
                'estado' => $estado,
                'municipio' => $municipio,
                'asentamientos' => $asentamientos->map(function ($asentamiento) {
                    return [
                        'id' => $asentamiento->id,
                        'nombre' => $asentamiento->nombre,
                        'tipo' => $asentamiento->tipoAsentamiento?->nombre ?? 'No disponible'
                    ];
                })
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error al obtener datos por código postal:', [
                'codigo_postal' => $codigoPostal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener los datos'
            ];
        }
    }

    /**
     * Obtiene los datos formateados de la dirección para un trámite (método legacy)
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return array Los datos de dirección formateados
     */
    public function getAddressData(Tramite $tramite)
    {
        return $this->obtenerDatos($tramite);
    }

    /**
     * Valida los datos del formulario de domicilio
     *
     * @param Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateDomicilioData(Request $request)
    {
        $rules = [
            'tramite_id' => [
                'required',
                'integer',
                'exists:tramite,id'
            ],
            'codigo_postal' => [
                'required',
                'string',
                'regex:/^[0-9]{4,5}$/',
                'min:4',
                'max:5'
            ],
            'colonia' => [
                'required',
                'integer',
                'exists:asentamiento,id'
            ],
            'calle' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.\-\,\#\/]+$/'
            ],
            'numero_exterior' => [
                'required',
                'string',
                'max:10',
                'regex:/^[a-zA-Z0-9\s\-\.\/SN]+$/i'
            ],
            'numero_interior' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[a-zA-Z0-9\s\-\.\/]*$/'
            ],
            'entre_calle_1' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.\-\,\#\/]*$/'
            ],
            'entre_calle_2' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.\-\,\#\/]*$/'
            ]
        ];

        $messages = [
            'tramite_id.required' => 'No se pudo identificar el trámite asociado',
            'tramite_id.integer' => 'El identificador del trámite debe ser un número válido',
            'tramite_id.exists' => 'El trámite especificado no existe o no es válido',
            
            'codigo_postal.required' => 'El código postal es obligatorio para ubicar la dirección',
            'codigo_postal.min' => 'El código postal debe tener al menos 4 dígitos',
            'codigo_postal.max' => 'El código postal no puede tener más de 5 dígitos',
            'codigo_postal.regex' => 'El código postal debe contener únicamente dígitos numéricos (ej: 12345)',
            
            'colonia.required' => 'Debe seleccionar un asentamiento de la lista disponible',
            'colonia.integer' => 'Debe seleccionar un asentamiento válido de la lista',
            'colonia.exists' => 'El asentamiento seleccionado no corresponde al código postal ingresado',
            
            'calle.required' => 'El nombre de la calle principal es obligatorio',
            'calle.min' => 'El nombre de la calle debe tener al menos 3 caracteres',
            'calle.max' => 'El nombre de la calle no puede exceder 100 caracteres',
            'calle.regex' => 'El nombre de la calle solo puede contener letras, números y caracteres básicos (. - , # /)',
            
            'numero_exterior.required' => 'El número exterior es obligatorio (puede usar "S/N" si no aplica)',
            'numero_exterior.max' => 'El número exterior no puede exceder 10 caracteres',
            'numero_exterior.regex' => 'El número exterior solo puede contener letras, números y caracteres básicos (ej: 123, S/N, 45-A)',
            
            'numero_interior.max' => 'El número interior no puede exceder 10 caracteres',
            'numero_interior.regex' => 'El número interior solo puede contener letras, números y caracteres básicos',
            
            'entre_calle_1.min' => 'Si especifica la primera calle de referencia, debe tener al menos 3 caracteres',
            'entre_calle_1.max' => 'La primera calle de referencia no puede exceder 100 caracteres',
            'entre_calle_1.regex' => 'La primera calle de referencia contiene caracteres no válidos',
            
            'entre_calle_2.min' => 'Si especifica la segunda calle de referencia, debe tener al menos 3 caracteres',
            'entre_calle_2.max' => 'La segunda calle de referencia no puede exceder 100 caracteres',
            'entre_calle_2.regex' => 'La segunda calle de referencia contiene caracteres no válidos'
        ];

        return $request->validate($rules, $messages);
    }
} 