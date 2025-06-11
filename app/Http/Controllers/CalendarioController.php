<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\DiasInhabiles;
use App\Models\Solicitante;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarioController extends Controller
{
    public function index()
    {
        $citas = Cita::with(['solicitante', 'tramite'])->orderBy('fecha_cita')->get();
        $diasInhabiles = DiasInhabiles::orderBy('fecha_inicio')->get();
        $solicitantes = Solicitante::all();
        $tramites = Tramite::all();

        return view('admin.calendario.index', compact('citas', 'diasInhabiles', 'solicitantes', 'tramites'));
    }

    public function storeCita(Request $request)
    {
        $request->validate([
            'solicitante_id' => 'required|exists:solicitante,id',
            'tramite_id' => 'required|exists:tramite,id',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'observaciones' => 'nullable|string'
        ]);

        // Verificar si la fecha es día inhábil
        $fechaCita = $request->fecha_cita;
        $diaInhabil = DiasInhabiles::where(function($query) use ($fechaCita) {
            $query->where('fecha_inicio', '<=', $fechaCita)
                  ->where(function($q) use ($fechaCita) {
                      $q->where('fecha_fin', '>=', $fechaCita)
                        ->orWhereNull('fecha_fin');
                  });
        })->first();

        if ($diaInhabil) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha seleccionada corresponde a un día inhábil'
            ], 422);
        }

        // Verificar si ya existe una cita en la misma fecha y hora
        $citaExistente = Cita::where('fecha_cita', $fechaCita)
                            ->where('hora_cita', $request->hora_cita)
                            ->first();

        if ($citaExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una cita programada para esta fecha y hora'
            ], 422);
        }

        $cita = Cita::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cita creada exitosamente',
            'cita' => $cita
        ]);
    }

    public function storeDiaInhabil(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'descripcion' => 'required|string|max:255'
        ]);

        // Verificar si hay citas programadas en el rango de fechas
        $query = Cita::where('fecha_cita', '>=', $request->fecha_inicio);
        if ($request->fecha_fin) {
            $query->where('fecha_cita', '<=', $request->fecha_fin);
        } else {
            $query->where('fecha_cita', $request->fecha_inicio);
        }
        
        $citasAfectadas = $query->count();

        if ($citasAfectadas > 0) {
            return response()->json([
                'success' => false,
                'message' => "Hay {$citasAfectadas} cita(s) programada(s) en este rango de fechas"
            ], 422);
        }

        $diaInhabil = DiasInhabiles::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Día inhábil registrado exitosamente',
            'diaInhabil' => $diaInhabil
        ]);
    }

    public function updateCita(Request $request, Cita $cita)
    {
        $request->validate([
            'solicitante_id' => 'required|exists:solicitante,id',
            'tramite_id' => 'required|exists:tramite,id',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required',
            'estado' => 'required|in:Pendiente,Confirmada,Cancelada,Completada',
            'observaciones' => 'nullable|string'
        ]);

        $cita->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cita actualizada exitosamente',
            'cita' => $cita
        ]);
    }

    public function updateDiaInhabil(Request $request, DiasInhabiles $diaInhabil)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'descripcion' => 'required|string|max:255'
        ]);

        $diaInhabil->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Día inhábil actualizado exitosamente',
            'diaInhabil' => $diaInhabil
        ]);
    }

    public function destroyCita(Cita $cita)
    {
        $cita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cita eliminada exitosamente'
        ]);
    }

    public function destroyDiaInhabil(DiasInhabiles $diaInhabil)
    {
        $diaInhabil->delete();

        return response()->json([
            'success' => true,
            'message' => 'Día inhábil eliminado exitosamente'
        ]);
    }
} 