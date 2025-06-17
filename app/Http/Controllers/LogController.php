<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Log::with('user');

        // Filtros
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener niveles únicos para el filtro
        $levels = Log::select('level')->distinct()->pluck('level');
        
        // Obtener canales únicos para el filtro
        $channels = Log::select('channel')->distinct()->pluck('channel');

        return view('logs.index', compact('logs', 'levels', 'channels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Log $log)
    {
        return view('logs.show', compact('log'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Log $log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Log $log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Log $log)
    {
        $log->delete();
        return redirect()->route('logs.index')->with('success', 'Log eliminado correctamente.');
    }
}
