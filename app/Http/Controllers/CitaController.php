<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DiaInhabil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DetalleTramiteController;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with('user')
                    ->orderBy('fecha_hora', 'desc')
                    ->paginate(10);
        
        $diasInhabiles = DiaInhabil::orderBy('fecha', 'desc')->get();

        return view('citas.index', compact('citas', 'diasInhabiles'));
    }

    public function create()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        // Obtener datos de domicilio si hay un trámite en progreso
        $datosDomicilio = [];
        $datosApoderado = [];
        if ($solicitante) {
            $tramiteEnProgreso = Tramite::where('solicitante_id', $solicitante->id)
                ->whereIn('estado', ['Pendiente', 'En Revision'])
                ->latest()
                ->first();
                
            if ($tramiteEnProgreso) {
                try {
                    $detalleTramiteController = new DetalleTramiteController();
                    $datosDomicilio = $detalleTramiteController->getDatosDomicilioByTramiteId($tramiteEnProgreso->id);
                    
                    if ($datosDomicilio) {
                        Log::info('📍 CITAS CREATE: Datos de domicilio obtenidos', [
                            'tramite_id' => $tramiteEnProgreso->id,
                            'codigo_postal' => $datosDomicilio['codigo_postal'],
                            'estado' => $datosDomicilio['estado'],
                            'municipio' => $datosDomicilio['municipio']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('📍 CITAS CREATE: Error al obtener datos de domicilio', [
                        'tramite_id' => $tramiteEnProgreso->id,
                        'error' => $e->getMessage()
                    ]);
                    $datosDomicilio = [];
                }
                
                // Obtener datos del apoderado legal si es persona moral
                if ($tramiteEnProgreso && $tramiteEnProgreso->solicitante->tipo_persona === 'Moral') {
                    $apoderadoController = new \App\Http\Controllers\Formularios\ApoderadoLegalController();
                    $datosApoderado = $apoderadoController->getDatosApoderadoLegal($tramiteEnProgreso);
                    $datosApoderado['tramite_id'] = $tramiteEnProgreso->id;
                }
            }
        }
        
        return view('citas.create', compact('solicitante', 'datosDomicilio', 'datosApoderado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'required|string|max:255',
            'notas' => 'nullable|string',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return redirect()->back()
                ->with('error', 'No se encontró información del solicitante. Por favor, complete su registro primero.');
        }

        $cita = Cita::create([
            'user_id' => $user->id,
            'solicitante_id' => $solicitante->id,
            'fecha_hora' => $request->fecha_hora,
            'motivo' => $request->motivo,
            'notas' => $request->notas,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita agendada correctamente.');
    }

    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        return view('citas.edit', compact('cita'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'fecha_hora' => 'required|date|after:now',
            'motivo' => 'required|string|max:255',
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
            'notas' => 'nullable|string',
        ]);

        $cita->update($request->all());

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada correctamente.');
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        $cita->update(['estado' => $request->estado]);

        return redirect()->back()
            ->with('success', 'Estado de la cita actualizado correctamente.');
    }
} 