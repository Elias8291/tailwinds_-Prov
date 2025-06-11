<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\SeccionTramite;
use App\Models\ProgresoTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgresoTramiteController extends Controller
{
    public function index(Tramite $tramite)
    {
        $secciones = SeccionTramite::orderBy('orden')->get();
        $progreso = $tramite->progresoSecciones;

        return view('tramites.progreso.index', compact('tramite', 'secciones', 'progreso'));
    }

    public function iniciarSeccion(Request $request, Tramite $tramite, SeccionTramite $seccion)
    {
        $progreso = $tramite->iniciarSeccion($seccion->id);
        
        return response()->json([
            'success' => true,
            'progreso' => $progreso,
            'porcentaje_total' => $tramite->actualizarProgreso()
        ]);
    }

    public function completarSeccion(Request $request, Tramite $tramite, SeccionTramite $seccion)
    {
        $request->validate([
            'observaciones' => 'nullable|string'
        ]);

        $progreso = $tramite->completarSeccion(
            $seccion->id, 
            $request->observaciones
        );

        return response()->json([
            'success' => true,
            'progreso' => $progreso,
            'porcentaje_total' => $tramite->actualizarProgreso()
        ]);
    }

    public function actualizarEstado(Request $request, Tramite $tramite, SeccionTramite $seccion)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completado,revision,aprobado,rechazado',
            'observaciones' => 'nullable|string'
        ]);

        $progreso = $tramite->progresoSecciones()->updateOrCreate(
            ['seccion_id' => $seccion->id],
            [
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
                'fecha_completado' => in_array($request->estado, ['completado', 'aprobado']) ? now() : null
            ]
        );

        $tramite->actualizarProgreso();

        return response()->json([
            'success' => true,
            'progreso' => $progreso,
            'porcentaje_total' => $tramite->progreso_tramite
        ]);
    }
} 