<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::all();
        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        return view('documentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_persona' => 'required|in:Física,Moral,Ambas',
            'descripcion' => 'nullable|string|max:1000',
            'es_visible' => 'boolean',
        ]);

        Documento::create($request->all());

        return redirect()->route('documentos.index')
            ->with('success', 'Documento creado exitosamente.');
    }

    public function edit(Documento $documento)
    {
        return view('documentos.edit', compact('documento'));
    }

    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_persona' => 'required|in:Física,Moral,Ambas',
            'descripcion' => 'nullable|string|max:1000',
            'es_visible' => 'boolean',
        ]);

        $documento->update($request->all());

        return redirect()->route('documentos.index')
            ->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Documento $documento)
    {
        $documento->delete();

        return redirect()->route('documentos.index')
            ->with('success', 'Documento eliminado exitosamente.');
    }
} 