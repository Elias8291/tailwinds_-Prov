<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with('user')
                    ->orderBy('fecha_hora', 'desc')
                    ->paginate(10);

        return view('citas.index', compact('citas'));
    }

    public function create()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Buscar el solicitante asociado al usuario
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        return view('citas.create', compact('solicitante'));
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