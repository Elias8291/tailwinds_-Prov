<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Solicitante;
use App\Models\Tramite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

    public function create($tipoTramite, $tramiteId)
    {
        try {
            // Validar que el tipo de trámite sea válido
            $tipos_tramite = [
                'inscripcion' => 'Inscripción',
                'renovacion' => 'Renovación',
                'actualizacion' => 'Actualización'
            ];

            if (!array_key_exists($tipoTramite, $tipos_tramite)) {
                abort(404, 'Tipo de trámite no válido');
            }

            // Buscar el trámite
            $tramite = Tramite::findOrFail($tramiteId);
            
            // Verificar que el trámite corresponda al tipo
            if (strtolower($tramite->tipo_tramite) !== $tipoTramite) {
                abort(404, 'El tipo de trámite no corresponde');
            }

            // Obtener el solicitante y sus datos
            $solicitante = $tramite->solicitante;
            
            // Preparar datos para la vista
            $datosTramite = [
                'tipo_tramite' => $tipoTramite,
                'titulo' => $tipos_tramite[$tipoTramite],
                'rfc' => $solicitante->rfc,
                'tipo_persona' => $solicitante->tipo_persona,
                'nombre_completo' => $solicitante->tipo_persona === 'Física' ? 
                    $solicitante->nombre . ' ' . $solicitante->apellido_paterno . ' ' . $solicitante->apellido_materno :
                    $solicitante->razon_social,
                'mostrar_razon_social' => $tipoTramite !== 'inscripcion'
            ];

            return view("tramites.create", [
                'tramite' => $tramite,
                'solicitante' => $solicitante,
                'datosTramite' => $datosTramite
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de trámite:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
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

            // Crear trámite
            $tramite = Tramite::create([
                'solicitante_id' => $solicitante->id,
                'tipo_tramite' => ucfirst($validated['tipo_tramite']),
                'estado' => 'Pendiente',
                'progreso_tramite' => 0,
            ]);

            Log::info('Trámite creado:', ['tramite_id' => $tramite->id]);

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
} 