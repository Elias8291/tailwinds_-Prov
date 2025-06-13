<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Solicitante;

class RevisionController extends Controller
{
    /**
     * Mostrar la lista de trámites pendientes de revisión
     */
    public function index()
    {
        // Obtener trámites pendientes de revisión con información del solicitante
        $tramites = Tramite::with(['solicitante', 'revisor'])
            ->whereIn('estado', ['Pendiente', 'En Revision', 'Por Cotejar'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('revision.index', compact('tramites'));
    }

    /**
     * Mostrar la vista de revisión de un trámite específico
     */
    public function show(Tramite $tramite)
    {
        // Cargar relaciones necesarias
        $tramite->load(['solicitante', 'revisor', 'detalleTramite']);
        
        // Datos vacíos para los formularios (por ahora)
        $datosTramite = null;
        $datosDomicilio = null;
        $datosSAT = null;
        $documentos = [];
        $accionistas = [];
        $apoderados = [];
        $constitucion = null;
        $personal = [];
        $descripcion = null;

        return view('revision.show', compact(
            'tramite',
            'datosTramite',
            'datosDomicilio', 
            'datosSAT',
            'documentos',
            'accionistas',
            'apoderados',
            'constitucion',
            'personal',
            'descripcion'
        ));
    }
} 