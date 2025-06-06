<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TramiteController extends Controller
{
    public function index()
    {
        $tramites = [
            [
                'tipo' => 'inscripcion',
                'nombre' => 'Inscripción',
                'descripcion' => 'Registro inicial en el padrón de proveedores',
                'icono' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                'color' => 'blue'
            ],
            [
                'tipo' => 'renovacion',
                'nombre' => 'Renovación',
                'descripcion' => 'Renovación anual de registro en el padrón',
                'icono' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                'color' => 'green'
            ],
            [
                'tipo' => 'actualizacion',
                'nombre' => 'Actualización',
                'descripcion' => 'Actualización de datos o documentos',
                'icono' => 'M4 4v16l12-6-12-6zm12 6v10m4-16v12',
                'color' => 'purple'
            ]
        ];
        
        return view('tramites.index', compact('tramites'));
    }

    public function create()
    {
        $tipos_tramite = [
            'inscripcion' => 'Inscripción',
            'renovacion' => 'Renovación',
            'actualizacion' => 'Actualización'
        ];
        
        return view('tramites.create', compact('tipos_tramite'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_tramite' => 'required|in:inscripcion,renovacion,actualizacion',
            'nombre_solicitante' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'descripcion' => 'required|string',
            'documentos.*' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        // Aquí irá la lógica para guardar el trámite
        // Por ahora solo redireccionamos con un mensaje de éxito
        return redirect()->back()->with('success', '¡Trámite enviado exitosamente!');
    }
} 