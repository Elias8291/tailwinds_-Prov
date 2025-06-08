<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $proveedores = Proveedor::paginate($perPage)->withQueryString();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pv' => 'required|string|max:10|unique:proveedor,pv',
            'fecha_registro' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_registro',
            'estado' => 'required|in:Activo,Inactivo,Pendiente Renovacion',
            'observaciones' => 'nullable|string',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor registrado exitosamente.');
    }

    public function edit($pv)
    {
        $proveedor = Proveedor::findOrFail($pv);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $pv)
    {
        $request->validate([
            'fecha_registro' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_registro',
            'estado' => 'required|in:Activo,Inactivo,Pendiente Renovacion',
            'observaciones' => 'nullable|string',
        ]);

        $proveedor = Proveedor::findOrFail($pv);
        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy($pv)
    {
        $proveedor = Proveedor::findOrFail($pv);
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
} 