<?php

namespace App\Http\Controllers;

use App\Models\DiaInhabil;
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
            'fecha' => 'required|date|unique:dia_inhabils,fecha',
            'descripcion' => 'required|string|max:255',
        ]);

        DiaInhabil::create($request->all());

        return redirect()->route('citas.index')
            ->with('success', 'Día inhábil registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DiaInhabil $diaInhabil)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiaInhabil $diaInhabil)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiaInhabil $diaInhabil)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiaInhabil $diasInhabile)
    {
        $diasInhabile->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Día inhábil eliminado correctamente.');
    }
}
