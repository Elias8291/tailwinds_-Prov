<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\ContactoSolicitante;
use App\Models\ActividadSolicitante;
use App\Models\Solicitante;
use Illuminate\Support\Facades\DB;

class DatosGeneralesController extends Controller
{
    /**
     * Guarda los datos generales del trámite y sus relaciones
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardar(Request $request)
    {
        try {
            Log::info('=== INICIO guardarDatosGenerales ===');
            Log::info('Request completo:', ['data' => $request->all()]);

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

            DB::beginTransaction();

            // Buscar o crear el solicitante
            $solicitante = Solicitante::where('rfc', $validated['rfc'])->first();
            
            if ($solicitante) {
                $solicitante->update([
                    'tipo_persona' => $validated['tipo_persona'],
                    'curp' => $validated['tipo_persona'] === 'Física' ? $validated['curp'] : null,
                    'objeto_social' => $validated['objeto_social'],
                ]);
            } else {
                $solicitante = Solicitante::create([
                    'rfc' => $validated['rfc'],
                    'tipo_persona' => $validated['tipo_persona'],
                    'curp' => $validated['tipo_persona'] === 'Física' ? $validated['curp'] : null,
                    'objeto_social' => $validated['objeto_social'],
                ]);
            }

            // Obtener o crear el trámite
            $tramite = null;
            if ($request->tramite_id) {
                $tramite = Tramite::find($request->tramite_id);
                if ($tramite) {
                    $tramite->update(['solicitante_id' => $solicitante->id]);
                }
            } 
            
            if (!$tramite && $request->tipo_tramite) {
                $tramite = Tramite::create([
                    'solicitante_id' => $solicitante->id,
                    'tipo_tramite' => ucfirst($validated['tipo_tramite']),
                    'estado' => 'Pendiente',
                    'progreso_tramite' => 2,
                ]);
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

            // Actualizar detalles del trámite
            $detalleTramite = DetalleTramite::updateOrCreate(
                ['tramite_id' => $tramite->id],
                [
                    'razon_social' => $validated['razon_social'],
                    'email' => $validated['contacto_correo'],
                    'telefono' => $validated['contacto_telefono'],
                    'contacto_id' => $contacto->id,
                ]
            );

            // Procesar actividades seleccionadas
            if (!empty($validated['actividades_seleccionadas'])) {
                $actividades = json_decode($validated['actividades_seleccionadas'], true);
                if (!empty($actividades)) {
                    ActividadSolicitante::where('tramite_id', $tramite->id)->delete();
                    foreach ($actividades as $actividadId) {
                        ActividadSolicitante::create([
                            'tramite_id' => $tramite->id,
                            'actividad_id' => $actividadId,
                        ]);
                    }
                }
            }

            // Actualizar el progreso del trámite
            $tramite->update(['progreso_tramite' => 2]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Datos generales guardados correctamente',
                'redirect' => route('tramites.create', [
                    'tipo_tramite' => strtolower($tramite->tipo_tramite),
                    'tramite' => $tramite->id,
                    'step' => 2
                ])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en datos generales:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar datos generales:', [
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
     * Obtiene los datos generales del trámite y el solicitante
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return array Los datos generales formateados
     */
    public function obtenerDatos(Tramite $tramite)
    {
        $solicitante = $tramite->solicitante;
        $detalleTramite = $tramite->detalleTramite;
        $contacto = $detalleTramite ? $detalleTramite->contacto : null;

        return [
            'rfc' => $solicitante->rfc ?? null,
            'tipo_persona' => $solicitante->tipo_persona ?? null,
            'curp' => $solicitante->curp ?? null,
            'razon_social' => $detalleTramite->razon_social ?? null,
            'objeto_social' => $solicitante->objeto_social ?? null,
            'sector_id' => $tramite->sector_id ?? null,
            'actividades' => $tramite->actividades()->pluck('id')->toArray(),
            'contacto_nombre' => $contacto->nombre ?? '',
            'contacto_cargo' => $contacto->puesto ?? '',
            'contacto_correo' => $contacto->email ?? '',
            'contacto_telefono' => $contacto->telefono ?? '',
        ];
    }
} 