<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Tramite;
use App\Models\Accionista;
use App\Models\AccionistaSolicitante;
use Illuminate\Support\Facades\DB;

class AccionistasController extends Controller
{
    /**
     * Guarda los datos de los accionistas para un trámite específico
     *
     * @param Request $request La solicitud con los datos de los accionistas
     * @param Tramite $tramite El trámite asociado
     * @return bool Indica si la operación fue exitosa
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $this->validateFormularioData($request);

        DB::transaction(function () use ($request, $tramite) {
            $accionistasData = $this->parseAccionistasData($request);
            $this->deleteExistingAccionistas($tramite);
            $totalPorcentaje = $this->saveAccionistas($tramite, $accionistasData);
            $this->validateTotalPorcentaje($totalPorcentaje, $tramite);
        });

        // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Sección 4: Accionistas → 5
            $tramite->actualizarProgresoSeccion(5);

            return true;
    }

    /**
     * Guarda los datos de accionistas desde AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarFormulario accionistas ===', [
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

            // Procesar y guardar los accionistas
            DB::transaction(function () use ($validated, $tramite) {
                // Eliminar accionistas existentes
                $this->deleteExistingAccionistas($tramite);
                
                // Guardar nuevos accionistas
                $totalPorcentaje = 0;
                foreach ($validated['accionistas'] as $accionistaData) {
                    // Crear o encontrar accionista
                    $accionista = Accionista::firstOrCreate([
                        'nombre' => $accionistaData['nombre'],
                        'apellido_paterno' => $accionistaData['apellido_paterno'],
                        'apellido_materno' => $accionistaData['apellido_materno'] ?? '',
                    ]);

                    $porcentaje = floatval($accionistaData['porcentaje']);
                    $totalPorcentaje += $porcentaje;

                    // Crear relación con solicitante
                    AccionistaSolicitante::create([
                        'tramite_id' => $tramite->id,
                        'accionista_id' => $accionista->id,
                        'porcentaje_participacion' => $porcentaje,
                    ]);

                    Log::info('Accionista guardado:', [
                        'accionista_id' => $accionista->id,
                        'nombre' => $accionista->nombre,
                        'porcentaje' => $porcentaje
                    ]);
                }

                Log::info('Total porcentaje accionistas: ' . $totalPorcentaje . '%');
            });

            // Actualizar progreso del trámite DESPUÉS de confirmar la transacción - Sección 4: Accionistas → 5
            $tramite->actualizarProgresoSeccion(5);

            Log::info('✅ Accionistas guardados exitosamente para tramite_id: ' . $validated['tramite_id']);

            return response()->json([
                'success' => true,
                'message' => 'Accionistas guardados correctamente',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación en accionistas:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Por favor corrija los errores en la información de los accionistas.',
                'errors' => $e->errors(),
                'debug_info' => [
                    'seccion' => 'accionistas',
                    'timestamp' => now()->toISOString(),
                    'total_errores' => count($e->errors())
                ]
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al guardar datos de accionistas:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar los datos de accionistas. Por favor, intente nuevamente.',
                'debug_info' => [
                    'seccion' => 'accionistas',
                    'timestamp' => now()->toISOString(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Valida los datos del formulario de accionistas usando validaciones en español
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
            'accionistas' => [
                'required',
                'array',
                'min:1',
                'max:20' // Límite razonable de accionistas
            ],
            'accionistas.*.nombre' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ],
            'accionistas.*.apellido_paterno' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/'
            ],
            'accionistas.*.apellido_materno' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]*$/'
            ],
            'accionistas.*.porcentaje' => [
                'required',
                'numeric',
                'min:0.01',
                'max:100',
                'regex:/^\d{1,2}(\.\d{1,2})?$/' // Permite hasta 2 decimales
            ]
        ];

        // Usar los mensajes de validación en español del framework
        $messages = [
            'tramite_id.required' => 'No se pudo identificar el trámite asociado',
            'tramite_id.integer' => 'El identificador del trámite debe ser un número válido',
            'tramite_id.exists' => 'El trámite especificado no existe o no es válido',
            
            'accionistas.required' => 'Debe agregar al menos un accionista',
            'accionistas.array' => 'Los datos de accionistas no tienen el formato correcto',
            'accionistas.min' => 'Debe especificar al menos un accionista',
            'accionistas.max' => 'No puede especificar más de 20 accionistas',
            
            'accionistas.*.nombre.required' => 'El nombre del accionista es obligatorio',
            'accionistas.*.nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'accionistas.*.nombre.max' => 'El nombre no puede exceder 50 caracteres',
            'accionistas.*.nombre.regex' => 'El nombre solo puede contener letras, espacios y apostrofes',
            
            'accionistas.*.apellido_paterno.required' => 'El apellido paterno es obligatorio',
            'accionistas.*.apellido_paterno.min' => 'El apellido paterno debe tener al menos 2 caracteres',
            'accionistas.*.apellido_paterno.max' => 'El apellido paterno no puede exceder 50 caracteres',
            'accionistas.*.apellido_paterno.regex' => 'El apellido paterno solo puede contener letras, espacios y apostrofes',
            
            'accionistas.*.apellido_materno.min' => 'El apellido materno debe tener al menos 2 caracteres',
            'accionistas.*.apellido_materno.max' => 'El apellido materno no puede exceder 50 caracteres',
            'accionistas.*.apellido_materno.regex' => 'El apellido materno solo puede contener letras, espacios y apostrofes',
            
            'accionistas.*.porcentaje.required' => 'El porcentaje de participación es obligatorio',
            'accionistas.*.porcentaje.numeric' => 'El porcentaje debe ser un valor numérico válido',
            'accionistas.*.porcentaje.min' => 'El porcentaje debe ser mayor a 0%',
            'accionistas.*.porcentaje.max' => 'El porcentaje no puede exceder 100%',
            'accionistas.*.porcentaje.regex' => 'El porcentaje debe tener máximo 2 decimales (ej: 25.50)'
        ];

        return $request->validate($rules, $messages);
    }



    /**
     * Procesa y normaliza los datos de los accionistas recibidos
     *
     * @param Request $request La solicitud con los datos
     * @return array Los datos normalizados de los accionistas
     */
    private function parseAccionistasData(Request $request): array
    {
        $accionistasData = $request->input('accionistas');
        Log::info('Processing shareholders data:', $request->all());

        if (!is_array($accionistasData)) {
            $accionistasData = json_decode($accionistasData, true) ?: [];
        }

        return $accionistasData;
    }

    /**
     * Elimina los registros de accionistas existentes para el trámite
     *
     * @param Tramite $tramite El trámite asociado
     * @return void
     */
    private function deleteExistingAccionistas(Tramite $tramite)
    {
        AccionistaSolicitante::where('tramite_id', $tramite->id)->delete();
        Log::info('Previous shareholders removed for tramite: ' . $tramite->id);
    }

    /**
     * Guarda los datos de los accionistas y calcula el porcentaje total
     *
     * @param Tramite $tramite El trámite asociado
     * @param array $accionistasData Los datos de los accionistas
     * @return float El porcentaje total de participación
     */
    private function saveAccionistas(Tramite $tramite, array $accionistasData): float
    {
        $totalPorcentaje = 0;

        foreach ($accionistasData as $accionistaData) {
            if (
                empty($accionistaData['nombre']) ||
                empty($accionistaData['apellido_paterno']) ||
                !isset($accionistaData['porcentaje'])
            ) {
                continue;
            }

            $accionista = Accionista::firstOrCreate([
                'nombre' => $accionistaData['nombre'],
                'apellido_paterno' => $accionistaData['apellido_paterno'],
                'apellido_materno' => $accionistaData['apellido_materno'] ?? '',
            ]);

            $porcentaje = floatval($accionistaData['porcentaje']);
            $totalPorcentaje += $porcentaje;

            AccionistaSolicitante::create([
                'tramite_id' => $tramite->id,
                'accionista_id' => $accionista->id,
                'porcentaje_participacion' => $porcentaje,
            ]);

            Log::info('Shareholder added: ' . $accionista->id . ' with ' . $porcentaje . '% participation');
        }

        return $totalPorcentaje;
    }

    /**
     * Valida que el porcentaje total de participación sea aproximadamente 100%
     *
     * @param float $totalPorcentaje El porcentaje total calculado
     * @param Tramite $tramite El trámite asociado
     * @return void
     */
    private function validateTotalPorcentaje(float $totalPorcentaje, Tramite $tramite)
    {
        if (abs($totalPorcentaje - 100) > 0.1) {
            Log::warning('Total shareholder percentage is not 100% for tramite ' . $tramite->id . ': ' . $totalPorcentaje);
        } else {
            Log::info('Total shareholder percentage for tramite ' . $tramite->id . ': ' . $totalPorcentaje . '%');
        }
    }

    /**
     * Obtiene los datos de los accionistas asociados a un trámite
     *
     * @param Tramite $tramite El trámite del cual obtener los accionistas
     * @return array Los datos de los accionistas
     */
    public function getShareholdersData(Tramite $tramite)
    {
        try {
            Log::info('Fetching shareholders data for tramite: ' . $tramite->id);

            $accionistas = AccionistaSolicitante::where('tramite_id', $tramite->id)
                ->with('accionista')
                ->get()
                ->map(function ($accionistaSolicitante) {
                    return $this->formatAccionistaData($accionistaSolicitante);
                })
                ->toArray();

            Log::info('Shareholders data retrieved: ', $accionistas);

            return $accionistas;
        } catch (\Exception $e) {
            Log::error('Error fetching shareholders data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return [];
        }
    }

    /**
     * Formatea los datos de un accionista para la respuesta
     *
     * @param AccionistaSolicitante $accionistaSolicitante El registro del accionista
     * @return array Los datos formateados del accionista
     */
    private function formatAccionistaData(AccionistaSolicitante $accionistaSolicitante): array
    {
        return [
            'id' => $accionistaSolicitante->accionista->id ?? 0,
            'nombre' => $this->safeString($accionistaSolicitante->accionista->nombre, 'No disponible'),
            'apellido_paterno' => $this->safeString($accionistaSolicitante->accionista->apellido_paterno, 'No disponible'),
            'apellido_materno' => $this->safeString($accionistaSolicitante->accionista->apellido_materno, ''),
            'porcentaje_participacion' => $this->safeNumeric($accionistaSolicitante->porcentaje_participacion, 0),
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
        if (is_string($value)) {
            return $value;
        }
        return is_array($value) ? json_encode($value) : $default;
    }

    /**
     * Convierte un valor a numérico seguro, manejando casos no válidos
     *
     * @param mixed $value El valor a convertir
     * @param float $default El valor por defecto si no es válido
     * @return float
     */
    private function safeNumeric($value, float $default): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }
        return is_array($value) ? floatval(json_encode($value)) : $default;
    }
} 