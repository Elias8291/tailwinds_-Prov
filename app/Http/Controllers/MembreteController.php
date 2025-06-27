<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MembreteController extends Controller
{
    /**
     * Muestra el índice de membretes disponibles
     */
    public function index()
    {
        return view('membretes.index');
    }

    /**
     * Genera un PDF de ejemplo de inscripción
     */
    public function ejemploInscripcion()
    {
        // Generar QR con información del documento usando SVG (no requiere imagick)
        $qrData = "Oficio: SA/DRM/DMRA/001/01/2025\nCédula: 12345\nTipo: Inscripción\nFecha: " . date('d/m/Y') . "\nValidar en: https://padron.oaxaca.gob.mx/verificar";
        $qrCode = QrCode::format('svg')->size(80)->generate($qrData);
        
        return view('membretes.inscripcion.documento', compact('qrCode'));
    }

    /**
     * Genera un PDF de ejemplo de renovación
     */
    public function ejemploRenovacion()
    {
        // Generar QR con información del documento usando SVG (no requiere imagick)
        $qrData = "Oficio: SA/DRM/DMRA/002/01/2025\nCédula: 67890\nTipo: Renovación\nFecha: " . date('d/m/Y') . "\nValidar en: https://padron.oaxaca.gob.mx/verificar";
        $qrCode = QrCode::format('svg')->size(80)->generate($qrData);
        
        return view('membretes.renovacion.documento', compact('qrCode'));
    }

    /**
     * Genera un PDF de ejemplo de actualización
     */
    public function ejemploActualizacion()
    {
        // Generar QR con información del documento usando SVG (no requiere imagick)
        $qrData = "Oficio: SA/DRM/DMRA/003/01/2025\nCédula: 54321\nTipo: Actualización\nFecha: " . date('d/m/Y') . "\nValidar en: https://padron.oaxaca.gob.mx/verificar";
        $qrCode = QrCode::format('svg')->size(80)->generate($qrData);
        
        return view('membretes.actualizacion.documento', compact('qrCode'));
    }

    private function generarPDF($vista, $datos)
    {
        $pdf = PDF::loadView($vista, $datos);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'enable_remote' => true,
            'chroot' => realpath(base_path()),
        ]);

        return $pdf;
    }

    private function obtenerFechaTexto()
    {
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $dia = date('d');
        $mes = $meses[(int)date('m')];
        $año = date('Y');
        
        return "{$dia} de {$mes} de {$año}";
    }
}