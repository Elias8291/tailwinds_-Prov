<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MembretesController extends Controller
{
    public function index()
    {
        return view('membretes.index');
    }

    public function ejemploInscripcion()
    {
        $pdf = Pdf::loadView('membretes.inscripcion.documento');
        return $pdf->stream('ejemplo-inscripcion.pdf');
    }

    public function ejemploRenovacion()
    {
        $pdf = Pdf::loadView('membretes.renovacion.documento');
        return $pdf->stream('ejemplo-renovacion.pdf');
    }

    public function ejemploActualizacion()
    {
        $pdf = Pdf::loadView('membretes.actualizacion.documento');
        return $pdf->stream('ejemplo-actualizacion.pdf');
    }

    public function generarCita($tramiteId)
    {
        $tramite = Tramite::with([
            'solicitante', 
            'cita', 
            'documentosSolicitante.documento',
            'detalleTramite.representanteLegal'
        ])->findOrFail($tramiteId);

        $tipoPersona = $tramite->solicitante->tipo_persona ?? 'Física';
        
        // Obtener documentos requeridos
        $documentosRequeridos = \App\Models\Documento::where(function($query) use ($tipoPersona) {
            $query->where('tipo_persona', $tipoPersona)
                  ->orWhere('tipo_persona', 'Ambas');
        })
        ->where('es_visible', true)
        ->get();

        // Obtener nombre del representante legal si es persona moral
        $nombreRepresentante = null;
        if ($tipoPersona === 'Moral' && $tramite->detalleTramite && $tramite->detalleTramite->representanteLegal) {
            $nombreRepresentante = $tramite->detalleTramite->representanteLegal->nombre;
        }

        $pdf = Pdf::loadView('membretes.citas.documento', [
            'tramite' => $tramite,
            'documentosRequeridos' => $documentosRequeridos,
            'nombreRepresentante' => $nombreRepresentante
        ]);

        // Configurar el PDF para mejor calidad
        $pdf->setPaper('letter');
        $pdf->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        return $pdf->stream('cita-cotejo-' . $tramite->id . '.pdf');
    }
} 