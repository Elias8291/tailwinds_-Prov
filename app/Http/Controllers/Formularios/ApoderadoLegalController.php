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

        return DB::transaction(function () use ($request, $tramite) {
            $detalleTramite = $this->getOrCreateDetalleTramite($tramite);
            $instrumentoNotarial = $this->guardarInstrumentoNotarial($request, $detalleTramite->representanteLegal?->instrumento_notarial_id);
            $representanteLegal = $this->guardarRepresentanteLegal($request, $instrumentoNotarial->id, $detalleTramite->representante_legal_id);

            $detalleTramite->representante_legal_id = $representanteLegal->id;
            $detalleTramite->save();

            return true;
        });
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

            // Validar los datos del formulario con validaciones más flexibles para AJAX
            $validated = $request->validate([
                'tramite_id' => 'required|integer',
                'nombre_apoderado' => 'required|string|max:100',
                'numero_escritura' => 'required|string|max:15',
                'nombre_notario' => 'required|string|max:100',
                'numero_notario' => 'required|string|max:10',
                'entidad_federativa' => 'required|integer|min:1|max:32',
                'fecha_escritura' => 'required|date|before_or_equal:today',
                'numero_registro' => 'required|string|max:20',
                'fecha_inscripcion' => 'required|date|before_or_equal:today',
            ]);

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

            Log::info('✅ Apoderado legal guardado exitosamente para tramite_id: ' . $validated['tramite_id']);

            return response()->json([
                'success' => true,
                'message' => 'Datos del apoderado legal guardados correctamente',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Errores de validación en apoderado legal:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ EXCEPCIÓN en guardarFormulario apoderado legal:', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida los datos recibidos en la solicitud
     *
     * @param Request $request La solicitud a validar
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request)
    {
        $rules = [
            'nombre-apoderado' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
            'numero-escritura' => 'required|numeric|max:9999999999',
            'nombre-notario' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
            'numero-notario' => 'required|numeric|max:9999999999',
            'entidad-federativa' => 'required|exists:estado,id',
            'fecha-escritura' => 'required|date|before_or_equal:today',
            'numero-registro' => 'required|string|max:20',
            'fecha-inscripcion' => 'required|date|after_or_equal:fecha-escritura|before_or_equal:today',
        ];

        $messages = [
            'nombre-apoderado.required' => 'El nombre del apoderado legal es obligatorio.',
            'nombre-apoderado.regex' => 'El nombre del apoderado debe contener solo letras, espacios y puntos.',
            'numero-escritura.required' => 'El número de escritura es obligatorio.',
            'numero-escritura.numeric' => 'El número de escritura debe ser numérico.',
            'nombre-notario.required' => 'El nombre del notario es obligatorio.',
            'nombre-notario.regex' => 'El nombre del notario debe contener solo letras, espacios y puntos.',
            'entidad-federativa.required' => 'La entidad federativa es obligatoria.',
            'entidad-federativa.exists' => 'La entidad federativa seleccionada no es válida.',
            'fecha-escritura.required' => 'La fecha de escritura es obligatoria.',
            'fecha-escritura.date' => 'La fecha de escritura debe ser una fecha válida.',
            'fecha-escritura.before_or_equal' => 'La fecha de escritura no puede ser futura.',
            'numero-notario.required' => 'El número de notario es obligatorio.',
            'numero-notario.numeric' => 'El número de notario debe ser numérico.',
            'numero-registro.required' => 'El número de registro es obligatorio.',
            'fecha-inscripcion.required' => 'La fecha de inscripción es obligatoria.',
            'fecha-inscripcion.date' => 'La fecha de inscripción debe ser una fecha válida.',
            'fecha-inscripcion.after_or_equal' => 'La fecha de inscripción no puede ser anterior a la fecha de escritura.',
            'fecha-inscripcion.before_or_equal' => 'La fecha de inscripción no puede ser futura.',
        ];

        $request->validate($rules, $messages);
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