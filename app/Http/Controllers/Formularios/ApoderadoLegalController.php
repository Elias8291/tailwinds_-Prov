<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\RepresentanteLegal;
use App\Models\InstrumentoNotarial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Carbon\Carbon;

class ApoderadoLegalController extends Controller
{
    /**
     * Guarda los datos del apoderado legal para un trámite específico
     *
     * @param Request $request La solicitud con los datos del apoderado legal
     * @param Tramite $tramite El trámite asociado
     * @return bool Indica si la operación fue exitosa
     * @throws \Illuminate\Validation\ValidationException|\Exception
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $this->validateRequest($request);

        DB::transaction(function () use ($request, $tramite) {
            $detalleTramite = $this->getOrCreateDetalleTramite($tramite);
            $instrumentoNotarial = $this->guardarInstrumentoNotarial($request, $detalleTramite->representanteLegal?->instrumento_notarial_id);
            $representanteLegal = $this->guardarRepresentanteLegal($request, $instrumentoNotarial->id, $detalleTramite->representante_legal_id);

            $detalleTramite->representante_legal_id = $representanteLegal->id;
            $detalleTramite->save();
        });

        // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Sección 5: Apoderado Legal → 6
            $tramite->actualizarProgresoSeccion(6);

            return true;
    }

    /**
     * Guarda los datos de apoderado legal desde AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarFormulario apoderado legal ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar los datos del formulario usando validaciones en español
            $validated = $this->validateFormularioData($request);

            // Buscar el trámite
            $tramite = Tramite::find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Procesar y guardar los datos del apoderado legal
            DB::transaction(function () use ($validated, $tramite) {
                $detalleTramite = $this->getOrCreateDetalleTramite($tramite);
                
                // Guardar instrumento notarial
                $instrumentoNotarial = $this->guardarInstrumentoNotarialAjax($validated, $detalleTramite->representanteLegal?->instrumento_notarial_id);
                
                // Guardar representante legal
                $representanteLegal = $this->guardarRepresentanteLegalAjax($validated, $instrumentoNotarial->id, $detalleTramite->representante_legal_id);

                // Actualizar detalle tramite
                $detalleTramite->representante_legal_id = $representanteLegal->id;
                $detalleTramite->save();

                Log::info('Apoderado legal guardado:', [
                    'representante_legal_id' => $representanteLegal->id,
                    'instrumento_notarial_id' => $instrumentoNotarial->id,
                    'tramite_id' => $tramite->id
                ]);
            });

            // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Sección 5: Apoderado Legal → 6
            $tramite->actualizarProgresoSeccion(6);

            Log::info('✅ Apoderado legal guardado exitosamente para tramite_id: ' . $validated['tramite_id']);

            return response()->json([
                'success' => true,
                'message' => 'Datos del apoderado legal guardados correctamente',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación en apoderado legal:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en la información del apoderado legal.',
                'errors' => $e->errors(),
                'debug_info' => [
                    'seccion' => 'apoderado_legal',
                    'timestamp' => now()->toISOString(),
                    'total_errores' => count($e->errors())
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al guardar datos del apoderado legal:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar los datos del apoderado legal. Por favor, intente nuevamente.',
                'debug_info' => [
                    'seccion' => 'apoderado_legal',
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Valida los datos del formulario de apoderado legal usando validaciones robustas en español
     *
     * @param Request $request La solicitud a validar
     * @return array Los datos validados
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateFormularioData(Request $request)
    {
        $rules = [
            'tramite_id' => [
                'required',
                'integer',
                'exists:tramite,id'
            ],
            'nombre_apoderado' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ],
            'numero_escritura' => [
                'required',
                'string',
                'min:1',
                'max:20',
                'regex:/^[0-9\-\/A-Z]+$/'
            ],
            'nombre_notario' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ],
            'numero_notario' => [
                'required',
                'string',
                'min:1',
                'max:10',
                'regex:/^[0-9]+$/'
            ],
            'entidad_federativa' => [
                'required',
                'integer',
                'between:1,32',
                'exists:estado,id'
            ],
            'fecha_escritura' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:1900-01-01'
            ],
            'numero_registro' => [
                'required',
                'string',
                'min:1',
                'max:30',
                'regex:/^[0-9A-Z\-\/\s]+$/'
            ],
            'fecha_inscripcion' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:fecha_escritura'
            ]
        ];

        $messages = [
            'tramite_id.required' => 'No se pudo identificar el trámite asociado',
            'tramite_id.integer' => 'El identificador del trámite debe ser un número válido',
            'tramite_id.exists' => 'El trámite especificado no existe o no es válido',
            
            'nombre_apoderado.required' => 'El nombre del apoderado legal es obligatorio',
            'nombre_apoderado.min' => 'El nombre del apoderado debe tener al menos 2 caracteres',
            'nombre_apoderado.max' => 'El nombre del apoderado no puede exceder 100 caracteres',
            'nombre_apoderado.regex' => 'El nombre del apoderado solo puede contener letras, espacios, apostrofes y puntos',
            
            'numero_escritura.required' => 'El número de escritura pública es obligatorio',
            'numero_escritura.min' => 'El número de escritura es obligatorio',
            'numero_escritura.max' => 'El número de escritura no puede exceder 20 caracteres',
            'numero_escritura.regex' => 'El número de escritura solo puede contener números, letras mayúsculas, guiones y diagonales',
            
            'nombre_notario.required' => 'El nombre del notario público es obligatorio',
            'nombre_notario.min' => 'El nombre del notario debe tener al menos 2 caracteres',
            'nombre_notario.max' => 'El nombre del notario no puede exceder 100 caracteres',
            'nombre_notario.regex' => 'El nombre del notario solo puede contener letras, espacios, apostrofes y puntos',
            
            'numero_notario.required' => 'El número del notario público es obligatorio',
            'numero_notario.min' => 'El número del notario es obligatorio',
            'numero_notario.max' => 'El número del notario no puede exceder 10 dígitos',
            'numero_notario.regex' => 'El número del notario solo puede contener dígitos numéricos',
            
            'entidad_federativa.required' => 'Debe seleccionar una entidad federativa',
            'entidad_federativa.integer' => 'Debe seleccionar una entidad federativa válida',
            'entidad_federativa.between' => 'La entidad federativa seleccionada no es válida',
            'entidad_federativa.exists' => 'La entidad federativa seleccionada no existe',
            
            'fecha_escritura.required' => 'La fecha de la escritura pública es obligatoria',
            'fecha_escritura.date' => 'La fecha de escritura debe ser una fecha válida',
            'fecha_escritura.before_or_equal' => 'La fecha de escritura no puede ser posterior a hoy',
            'fecha_escritura.after' => 'La fecha de escritura debe ser posterior al año 1900',
            
            'numero_registro.required' => 'El número de registro público mercantil es obligatorio',
            'numero_registro.min' => 'El número de registro es obligatorio',
            'numero_registro.max' => 'El número de registro no puede exceder 30 caracteres',
            'numero_registro.regex' => 'El número de registro solo puede contener números, letras mayúsculas, guiones, diagonales y espacios',
            
            'fecha_inscripcion.required' => 'La fecha de inscripción en el registro público es obligatoria',
            'fecha_inscripcion.date' => 'La fecha de inscripción debe ser una fecha válida',
            'fecha_inscripcion.before_or_equal' => 'La fecha de inscripción no puede ser posterior a hoy',
            'fecha_inscripcion.after_or_equal' => 'La fecha de inscripción no puede ser anterior a la fecha de escritura'
        ];

        $validated = $request->validate($rules, $messages);

        // Validaciones adicionales personalizadas
        $this->validateFechasAdicionales($validated);

        return $validated;
    }

    /**
     * Validaciones adicionales para fechas y lógica de negocio
     *
     * @param array $validated
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateFechasAdicionales(array $validated)
    {
        $fechaEscritura = \Carbon\Carbon::parse($validated['fecha_escritura']);
        $fechaInscripcion = \Carbon\Carbon::parse($validated['fecha_inscripcion']);
        
        // Verificar que la fecha de inscripción no sea muy anterior a la fecha de escritura
        if ($fechaInscripcion->lt($fechaEscritura->subDays(365))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fecha_inscripcion' => 'La fecha de inscripción no puede ser más de un año anterior a la fecha de escritura'
            ]);
        }

        // Verificar que la fecha de inscripción no sea muy posterior a la fecha de escritura (más de 5 años)
        if ($fechaInscripcion->gt($fechaEscritura->addYears(5))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fecha_inscripcion' => 'La fecha de inscripción no puede ser más de 5 años posterior a la fecha de escritura'
            ]);
        }

        // Verificar que las fechas no sean futuras
        $hoy = \Carbon\Carbon::now();
        if ($fechaEscritura->isFuture()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fecha_escritura' => 'La fecha de escritura no puede ser una fecha futura'
            ]);
        }

        if ($fechaInscripcion->isFuture()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fecha_inscripcion' => 'La fecha de inscripción no puede ser una fecha futura'
            ]);
        }
    }

    /**
     * Método legacy para mantener compatibilidad con el método guardar principal
     *
     * @param Request $request La solicitud a validar
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request)
    {
        // Convertir campos con guiones a guiones bajos para compatibilidad
        $data = $request->all();
        $mappedData = [
            'tramite_id' => $data['tramite_id'] ?? 0,
            'nombre_apoderado' => $data['nombre-apoderado'] ?? '',
            'numero_escritura' => $data['numero-escritura'] ?? '',
            'nombre_notario' => $data['nombre-notario'] ?? '',
            'numero_notario' => $data['numero-notario'] ?? '',
            'entidad_federativa' => $data['entidad-federativa'] ?? 0,
            'fecha_escritura' => $data['fecha-escritura'] ?? '',
            'numero_registro' => $data['numero-registro'] ?? '',
            'fecha_inscripcion' => $data['fecha-inscripcion'] ?? '',
        ];
        
        $newRequest = new Request($mappedData);
        $this->validateFormularioData($newRequest);
    }

    /**
     * Obtiene o crea un registro de DetalleTramite para el trámite especificado
     *
     * @param Tramite $tramite El trámite asociado
     * @return DetalleTramite El registro de DetalleTramite
     */
    private function getOrCreateDetalleTramite(Tramite $tramite): DetalleTramite
    {
        return DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
    }

    /**
     * Crea o actualiza un registro de instrumento notarial con los datos proporcionados
     *
     * @param Request $request La solicitud con los datos del instrumento notarial
     * @param int|null $instrumentoId El ID del instrumento notarial existente, si aplica
     * @return InstrumentoNotarial El registro del instrumento notarial creado o actualizado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function guardarInstrumentoNotarial(Request $request, ?int $instrumentoId): InstrumentoNotarial
    {
        $instrumentoNotarial = $instrumentoId ? InstrumentoNotarial::findOrFail($instrumentoId) : new InstrumentoNotarial();

        $instrumentoNotarial->numero_escritura = $request->input('numero-escritura');
        $instrumentoNotarial->fecha = $request->input('fecha-escritura');
        $instrumentoNotarial->nombre_notario = $request->input('nombre-notario');
        $instrumentoNotarial->numero_notario = $request->input('numero-notario');
        $instrumentoNotarial->estado_id = $request->input('entidad-federativa');
        $instrumentoNotarial->registro_mercantil = $request->input('numero-registro');
        $instrumentoNotarial->fecha_registro = $request->input('fecha-inscripcion');
        $instrumentoNotarial->save();

        return $instrumentoNotarial;
    }

    /**
     * Crea o actualiza un registro de instrumento notarial desde AJAX
     */
    private function guardarInstrumentoNotarialAjax(array $validated, ?int $instrumentoId): InstrumentoNotarial
    {
        $instrumentoNotarial = $instrumentoId ? InstrumentoNotarial::findOrFail($instrumentoId) : new InstrumentoNotarial();

        $instrumentoNotarial->numero_escritura = $validated['numero_escritura'];
        $instrumentoNotarial->fecha = $validated['fecha_escritura'];
        $instrumentoNotarial->nombre_notario = $validated['nombre_notario'];
        $instrumentoNotarial->numero_notario = $validated['numero_notario'];
        $instrumentoNotarial->estado_id = $validated['entidad_federativa'];
        $instrumentoNotarial->registro_mercantil = $validated['numero_registro'];
        $instrumentoNotarial->fecha_registro = $validated['fecha_inscripcion'];
        $instrumentoNotarial->save();

        return $instrumentoNotarial;
    }

    /**
     * Crea o actualiza un registro de representante legal con los datos proporcionados
     *
     * @param Request $request La solicitud con los datos del representante legal
     * @param int $instrumentoNotarialId El ID del instrumento notarial asociado
     * @param int|null $representanteId El ID del representante legal existente, si aplica
     * @return RepresentanteLegal El registro del representante legal creado o actualizado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function guardarRepresentanteLegal(Request $request, int $instrumentoNotarialId, ?int $representanteId): RepresentanteLegal
    {
        $representanteLegal = $representanteId ? RepresentanteLegal::findOrFail($representanteId) : new RepresentanteLegal();

        $representanteLegal->nombre = $request->input('nombre-apoderado');
        $representanteLegal->instrumento_notarial_id = $instrumentoNotarialId;
        $representanteLegal->save();

        return $representanteLegal;
    }

    /**
     * Crea o actualiza un registro de representante legal desde AJAX
     */
    private function guardarRepresentanteLegalAjax(array $validated, int $instrumentoNotarialId, ?int $representanteId): RepresentanteLegal
    {
        $representanteLegal = $representanteId ? RepresentanteLegal::findOrFail($representanteId) : new RepresentanteLegal();

        $representanteLegal->nombre = $validated['nombre_apoderado'];
        $representanteLegal->instrumento_notarial_id = $instrumentoNotarialId;
        $representanteLegal->save();

        return $representanteLegal;
    }
    
    /**
     * Obtiene y formatea los datos del apoderado legal para un trámite
     *
     * @param Tramite $tramite El trámite del cual obtener los datos del apoderado legal
     * @return array Los datos del apoderado legal formateados
     */
   public function getDatosApoderadoLegal(Tramite $tramite): array
    {
        $legalRepresentativeData = [
            'nombre_apoderado' => '',
            'numero_escritura' => '',
            'fecha_escritura' => '',
            'nombre_notario' => '',
            'numero_notario' => '',
            'entidad_federativa' => '',
            'numero_registro' => '',
            'fecha_inscripcion' => '',
        ];

        if (!$tramite->detalleTramite || !$tramite->detalleTramite->representanteLegal) {
            return $legalRepresentativeData;
        }

        $representanteLegal = $tramite->detalleTramite->representanteLegal;
        $instrumentoNotarial = $representanteLegal->instrumentoNotarial;

        if (!$instrumentoNotarial) {
            return $legalRepresentativeData;
        }

        return [
            'nombre_apoderado' => $this->safeString($representanteLegal->nombre, ''),
            'numero_escritura' => $this->safeString($instrumentoNotarial->numero_escritura, ''),
            'fecha_escritura' => $instrumentoNotarial->fecha
                ? Carbon::parse($instrumentoNotarial->fecha)->format('Y-m-d')
                : '',
            'nombre_notario' => $this->safeString($instrumentoNotarial->nombre_notario, ''),
            'numero_notario' => $this->safeString($instrumentoNotarial->numero_notario, ''),
            'entidad_federativa' => $instrumentoNotarial->estado_id ? (string)$instrumentoNotarial->estado_id : '',
            'numero_registro' => $this->safeString($instrumentoNotarial->registro_mercantil, ''),
            'fecha_inscripcion' => $instrumentoNotarial->fecha_registro
                ? Carbon::parse($instrumentoNotarial->fecha_registro)->format('Y-m-d')
                : '',
        ];
    }
    
    /**
     * Convierte un valor a string seguro, manejando casos no válidos
     *
     * @param mixed $value El valor a convertir
     * @param string $default El valor por defecto si no es válido
     * @return string
     */
    private function safeString($value, string $default): string
    {
        if (is_string($value) || is_numeric($value)) {
            return (string)$value;
        }
        return is_array($value) ? json_encode($value) : $default;
    }
} 