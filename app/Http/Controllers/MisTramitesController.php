<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MisTramitesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante) {
            return redirect()->route('tramites.solicitante.index')
                ->with('error', 'No se encontró información del solicitante');
        }

        // Construir la consulta base con todas las relaciones necesarias
        $query = Tramite::with([
            'solicitante',
            'revisor',
            'seccionesRevision',
            'cita' => function($query) {
                $query->whereIn('estado', ['pendiente', 'confirmada']);
            }
        ])
        ->where('solicitante_id', $solicitante->id);

        // Aplicar filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_tramite')) {
            $query->where('tipo_tramite', $request->tipo_tramite);
        }

        if ($request->filled('fecha')) {
            switch ($request->fecha) {
                case 'hoy':
                    $query->whereDate('fecha_inicio', today());
                    break;
                case 'semana':
                    $query->whereBetween('fecha_inicio', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'mes':
                    $query->whereMonth('fecha_inicio', now()->month)
                          ->whereYear('fecha_inicio', now()->year);
                    break;
            }
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('id', 'like', "%{$buscar}%")
                  ->orWhere('tipo_tramite', 'like', "%{$buscar}%")
                  ->orWhere('estado', 'like', "%{$buscar}%");
            });
        }

        // Obtener trámites paginados
        $tramites = $query->latest()->paginate(10);

        // Mantener parámetros de búsqueda en la paginación
        $tramites->appends($request->query());

        Log::info('Listado de mis trámites cargado:', [
            'user_id' => $user->id,
            'solicitante_id' => $solicitante->id,
            'total_tramites' => $tramites->total(),
            'filtros_aplicados' => $request->only(['estado', 'tipo_tramite', 'fecha', 'buscar'])
        ]);

        return view('mis-tramites.index', compact('tramites'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Tramite $tramite)
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
            return redirect()->route('mis-tramites.index')
                ->with('error', 'No tiene permisos para ver este trámite');
        }

        // Cargar todas las relaciones necesarias
        $tramite->load([
            'solicitante',
            'revisor',
            'seccionesRevision',
            'documentosSolicitante.documento',
            'cita' => function($query) {
                $query->whereIn('estado', ['pendiente', 'confirmada']);
            }
        ]);

        return redirect()->route('tramites.solicitante.estado', ['tramite' => $tramite->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tramite $tramite)
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
            return redirect()->route('mis-tramites.index')
                ->with('error', 'No tiene permisos para editar este trámite');
        }

        if (!$tramite->puedeSerEditado()) {
            return redirect()->route('mis-tramites.index')
                ->with('error', 'Este trámite no puede ser editado en su estado actual');
        }

        return redirect()->route('tramites.create.tipo', [
            'tipo_tramite' => strtolower($tramite->tipo_tramite),
            'tramite' => $tramite->id
        ]);
    }

    /**
     * Download the approved tramite certificate.
     */
    public function download(Tramite $tramite)
    {
        $user = Auth::user();
        $solicitante = Solicitante::where('usuario_id', $user->id)->first();
        
        if (!$solicitante || $tramite->solicitante_id !== $solicitante->id) {
            return redirect()->route('mis-tramites.index')
                ->with('error', 'No tiene permisos para descargar este documento');
        }

        if ($tramite->estado !== 'Aprobado') {
            return redirect()->route('mis-tramites.index')
                ->with('error', 'Solo puede descargar constancias de trámites aprobados');
        }

        // Aquí implementar la lógica de generación/descarga del PDF
        // Por ahora redireccionar con mensaje informativo
        return redirect()->route('mis-tramites.index')
            ->with('info', 'La funcionalidad de descarga estará disponible próximamente');
    }
} 