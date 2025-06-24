<?php

namespace App\Http\Controllers;

use App\Models\DiasInhabiles;
use Illuminate\Http\Request;

class DiaInhabilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dias-inhabiles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date|unique:dias_inhabiles,fecha_inicio',
            'descripcion' => 'required|string|max:255',
        ]);

        DiasInhabiles::create([
            'fecha_inicio' => $request->fecha_inicio,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Día inhábil registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DiasInhabiles $diaInhabil)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiasInhabiles $diaInhabil)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiasInhabiles $diaInhabil)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiasInhabiles $diasInhabile)
    {
        $diasInhabile->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Día inhábil eliminado correctamente.');
    }
}
