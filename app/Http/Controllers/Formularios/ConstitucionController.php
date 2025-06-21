<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\DatosConstitutivo;
use App\Models\InstrumentoNotarial;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConstitucionController extends Controller
{
    /**
     * Obtiene los datos de constitución para un trámite, formateando fechas en español
     *
     * @param Tramite $tramite El trámite del cual obtener los datos de constitución
     * @return array Los datos de constitución formateados
     */
    public function getIncorporationData(Tramite $tramite): array
    {
        try {
            Log::info('Obteniendo datos de constitución para trámite:', [
                'tramite_id' => $tramite->id,
                'tipo_persona' => $tramite->solicitante->tipo_persona ?? 'No definido'
            ]);

            setlocale(LC_TIME, 'es_MX.UTF-8', 'es_ES.UTF-8', 'Spanish_Mexico');

            $incorporationData = $this->initializeIncorporationData();
            $detalleTramite = $this->getDetalleTramite($tramite);

            Log::info('Detalle de trámite encontrado:', [
                'tramite_id' => $tramite->id,
                'detalle_tramite_existe' => !is_null($detalleTramite),
                'dato_constitutivo_id' => $detalleTramite?->dato_constitutivo_id,
                'tiene_dato_constitutivo' => !is_null($detalleTramite?->datoConstitutivo),
                'tiene_instrumento_notarial' => !is_null($detalleTramite?->datoConstitutivo?->instrumentoNotarial)
            ]);

            if ($detalleTramite && $detalleTramite->datoConstitutivo && $detalleTramite->datoConstitutivo->instrumentoNotarial) {
                $this->fillIncorporationData($detalleTramite->datoConstitutivo->instrumentoNotarial, $incorporationData);
                
                Log::info('Datos de constitución llenados:', [
                    'tramite_id' => $tramite->id,
                    'datos' => $incorporationData
                ]);
            } else {
                Log::info('No se encontraron datos de constitución:', [
                    'tramite_id' => $tramite->id,
                    'razon' => 'No existe detalle_tramite, dato_constitutivo o instrumento_notarial'
                ]);
            }

            return $incorporationData;
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de constitución:', [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Devolver array vacío en caso de error
            return [];
        }
    }

    /**
     * Guarda los datos de constitución para un trámite específico
     *
     * @param Request $request La solicitud con los datos de constitución
     * @param Tramite $tramite El trámite asociado
     * @return bool Indica si la operación fue exitosa
     * @throws \Illuminate\Validation\ValidationException|\Exception
     */
    public function guardar(Request $request, Tramite $tramite): bool
    {
        $this->validateRequest($request);

        DB::transaction(function () use ($request, $tramite) {
            $detalleTramite = $this->getOrCreateDetalleTramite($tramite);
            $instrumentoNotarial = $this->guardarInstrumentoNotarial($request, $detalleTramite->datoConstitutivo?->instrumento_notarial_id);
            $datoConstitutivo = $this->guardarDatoConstitutivo($instrumentoNotarial->id, $detalleTramite->dato_constitutivo_id);

            $detalleTramite->dato_constitutivo_id = $datoConstitutivo->id;
            $detalleTramite->save();
        });

        // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Sección 3: Constitución → 4
            $tramite->actualizarProgresoSeccion(4);

            return true;
    }

    /**
     * Guarda los datos de constitución desde AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarFormulario constitución ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar los datos del formulario
            $validated = $request->validate([
                'tramite_id' => 'required|integer',
                'numero_escritura' => 'required|string|max:15',
                'nombre_notario' => 'required|string|max:100',
                'entidad_federativa' => 'required|integer|min:1|max:32',
                'fecha_constitucion' => 'required|date|before_or_equal:today',
                'numero_notario' => 'required|string|max:10',
                'numero_registro' => 'required|string|max:20',
                'fecha_inscripcion' => 'required|date|before_or_equal:today',
            ]);

            // Buscar el trámite
            $tramite = Tramite::with('detalleTramite')->find($validated['tramite_id']);
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Guardar los datos usando el método de la clase
            $this->guardar($request, $tramite);

            Log::info('✅ Constitución guardada exitosamente', [
                'tramite_id' => $tramite->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos de constitución guardados correctamente.',
                'step' => 4
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Error de validación en constitución:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al guardar datos de constitución:', [
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

    /**
     * Valida los datos recibidos en la solicitud para los campos de constitución
     *
     * @param Request $request La solicitud a validar
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request): void
    {
        $rules = [
            'numero_escritura' => 'required|string|max:15',
            'nombre_notario' => 'required|string|max:100',
            'entidad_federativa' => 'required|integer|min:1|max:32',
            'fecha_constitucion' => 'required|date|before_or_equal:today',
            'numero_notario' => 'required|string|max:10',
            'numero_registro' => 'required|string|max:20',
            'fecha_inscripcion' => 'required|date|before_or_equal:today',
        ];

        $messages = [
            'numero_escritura.required' => 'El número de escritura es obligatorio.',
            'numero_escritura.numeric' => 'El número de escritura debe ser numérico.',
            'nombre_notario.required' => 'El nombre del notario es obligatorio.',
            'nombre_notario.regex' => 'El nombre del notario debe contener solo letras, espacios y puntos.',
            'entidad_federativa.required' => 'La entidad federativa es obligatoria.',
            'entidad_federativa.exists' => 'La entidad federativa seleccionada no es válida.',
            'fecha_constitucion.required' => 'La fecha de constitución es obligatoria.',
            'fecha_constitucion.date' => 'La fecha de constitución debe ser una fecha válida.',
            'fecha_constitucion.before_or_equal' => 'La fecha de constitución no puede ser futura.',
            'numero_notario.required' => 'El número de notario es obligatorio.',
            'numero_notario.numeric' => 'El número de notario debe ser numérico.',
            'numero_registro.required' => 'El número de registro es obligatorio.',
            'fecha_inscripcion.required' => 'La fecha de inscripción es obligatoria.',
            'fecha_inscripcion.date' => 'La fecha de inscripción debe ser una fecha válida.',
            'fecha_inscripcion.after_or_equal' => 'La fecha de inscripción no puede ser anterior a la fecha de constitución.',
            'fecha_inscripción.before_or_equal' => 'La fecha de inscripción no puede ser futura.',
        ];

        $request->validate($rules, $messages);
    }

    /**
     * Inicializa el array de datos de constitución con valores por defecto
     *
     * @return array Los datos de constitución inicializados
     */
    private function initializeIncorporationData(): array
    {
        return [
            'numero_escritura' => null,
            'nombre_notario' => null,
            'entidad_federativa' => null,
            'fecha_constitucion' => null,
            'numero_notario' => null,
            'numero_registro' => null,
            'fecha_inscripcion' => null,
            'fecha_constitucion_formatted' => null,
            'fecha_inscripcion_formatted' => null,
            'entidad_federativa_nombre' => null,
        ];
    }

    /**
     * Obtiene el registro de DetalleTramite para el trámite especificado
     *
     * @param Tramite $tramite El trámite asociado
     * @return DetalleTramite|null El registro de DetalleTramite o null si no existe
     */
    private function getDetalleTramite(Tramite $tramite): ?DetalleTramite
    {
        return DetalleTramite::with(['datoConstitutivo.instrumentoNotarial.estado'])
            ->where('tramite_id', $tramite->id)
            ->first();
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
     * Llena el array de datos de constitución con información del instrumento notarial
     *
     * @param InstrumentoNotarial $instrumentoNotarial El registro del instrumento notarial
     * @param array &$incorporationData El array de datos a llenar
     * @return void
     */
    private function fillIncorporationData(InstrumentoNotarial $instrumentoNotarial, array &$incorporationData): void
    {
        $incorporationData['numero_escritura'] = $this->safeString($instrumentoNotarial->numero_escritura, null);
        $incorporationData['nombre_notario'] = $this->safeString($instrumentoNotarial->nombre_notario, null);
        $incorporationData['numero_notario'] = $this->safeString($instrumentoNotarial->numero_notario, null);
        
        // Para fechas, usar formato Y-m-d para inputs date y formato legible para mostrar
        $incorporationData['fecha_constitucion'] = $instrumentoNotarial->fecha
            ? $instrumentoNotarial->fecha->format('Y-m-d')
            : null;
        $incorporationData['fecha_constitucion_formatted'] = $instrumentoNotarial->fecha
            ? $instrumentoNotarial->fecha->format('d/m/Y')
            : null;
            
        $incorporationData['numero_registro'] = $this->safeString($instrumentoNotarial->registro_mercantil, null);
        
        $incorporationData['fecha_inscripcion'] = $instrumentoNotarial->fecha_registro
            ? $instrumentoNotarial->fecha_registro->format('Y-m-d')
            : null;
        $incorporationData['fecha_inscripcion_formatted'] = $instrumentoNotarial->fecha_registro
            ? $instrumentoNotarial->fecha_registro->format('d/m/Y')
            : null;
            
        // Para entidad federativa, devolver el ID del estado para mantener compatibilidad
        $incorporationData['entidad_federativa'] = $instrumentoNotarial->estado_id ?? null;
        $incorporationData['entidad_federativa_nombre'] = $instrumentoNotarial->estado?->nombre ?? null;
        
        Log::info('Datos llenados desde instrumento notarial:', [
            'numero_escritura' => $incorporationData['numero_escritura'],
            'nombre_notario' => $incorporationData['nombre_notario'],
            'entidad_federativa' => $incorporationData['entidad_federativa'],
            'instrumento_id' => $instrumentoNotarial->id
        ]);
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

        $instrumentoNotarial->numero_escritura = $request->input('numero_escritura');
        $instrumentoNotarial->fecha = $request->input('fecha_constitucion');
        $instrumentoNotarial->nombre_notario = $request->input('nombre_notario');
        $instrumentoNotarial->numero_notario = $request->input('numero_notario');
        $instrumentoNotarial->estado_id = $request->input('entidad_federativa');
        $instrumentoNotarial->registro_mercantil = $request->input('numero_registro');
        $instrumentoNotarial->fecha_registro = $request->input('fecha_inscripcion');
        $instrumentoNotarial->save();

        return $instrumentoNotarial;
    }

    /**
     * Crea o actualiza un registro de datos constitutivos con el objeto social del solicitante
     *
     * @param int $instrumentoNotarialId El ID del instrumento notarial asociado
     * @param int|null $datoConstitutivoId El ID del dato constitutivo existente, si aplica
     * @return DatosConstitutivo El registro de datos constitutivos creado o actualizado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function guardarDatoConstitutivo(int $instrumentoNotarialId, ?int $datoConstitutivoId): DatosConstitutivo
    {
        $datoConstitutivo = $datoConstitutivoId ? DatosConstitutivo::findOrFail($datoConstitutivoId) : new DatosConstitutivo();

        $datoConstitutivo->instrumento_notarial_id = $instrumentoNotarialId;
        $datoConstitutivo->objeto_social = $this->safeString(Auth::user()->solicitante->objeto_social, '');
        $datoConstitutivo->save();

        return $datoConstitutivo;
    }

    /**
     * Convierte un valor a string seguro, manejando casos no válidos
     *
     * @param mixed $value El valor a convertir
     * @param string|null $default El valor por defecto si no es válido
     * @return string|null
     */
    private function safeString($value, ?string $default): ?string
    {
        if (is_string($value) && !empty(trim($value))) {
            return $value;
        }
        return $default;
    }
} 