<?php

namespace App\Services;

use App\Models\Tramite;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OficioService
{
    /**
     * Generar un nuevo oficio para un trámite
     */
    public function generarOficio(Tramite $tramite, string $tipoOficio): array
    {
        try {
            // Generar número de oficio
            $numeroOficio = $this->generarNumeroOficio();

            // Generar el PDF
            $pdf = $this->generarPDF($tramite, $tipoOficio, $numeroOficio);

            // Guardar el archivo temporalmente
            $rutaArchivo = $this->guardarArchivo($pdf, $tramite, $numeroOficio);

            // Generar hash del archivo
            $hashArchivo = hash('sha256', $pdf->output());

            Log::info('Oficio generado exitosamente', [
                'tramite_id' => $tramite->id,
                'tipo' => $tipoOficio,
                'numero' => $numeroOficio
            ]);

            return [
                'pdf' => $pdf,
                'numero_oficio' => $numeroOficio,
                'ruta_archivo' => $rutaArchivo,
                'hash_archivo' => $hashArchivo,
                'tipo_oficio' => $tipoOficio,
                'tramite_id' => $tramite->id,
                'generado_por' => Auth::id(),
                'fecha_generacion' => now()
            ];

        } catch (\Exception $e) {
            Log::error('Error al generar oficio:', [
                'tramite_id' => $tramite->id,
                'tipo' => $tipoOficio,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Generar número de oficio
     */
    protected function generarNumeroOficio(): string
    {
        $año = date('Y');
        $consecutivo = rand(1000, 9999); // Generar número aleatorio ya que no guardamos en BD
        
        return sprintf('OFICIO-PV-%s-%04d', $año, $consecutivo);
    }

    /**
     * Generar el PDF del oficio
     */
    protected function generarPDF(Tramite $tramite, string $tipoOficio, string $numeroOficio)
    {
        // Preparar datos para la vista
        $datos = [
            'tramite' => $tramite,
            'numero_oficio' => $numeroOficio,
            'fecha' => now()->format('d/m/Y'),
            'tipo_oficio' => $tipoOficio
        ];

        // Renderizar vista según el tipo de oficio
        $vista = View::make('membretes.oficio', $datos)->render();

        // Configurar PDF
        $pdf = PDF::loadHTML($vista);
        $pdf->setPaper('letter');
        $pdf->setOption('margin-top', 30);
        $pdf->setOption('margin-bottom', 30);
        $pdf->setOption('margin-left', 30);
        $pdf->setOption('margin-right', 30);

        return $pdf;
    }

    /**
     * Guardar el archivo PDF temporalmente
     */
    protected function guardarArchivo($pdf, Tramite $tramite, string $numeroOficio): string
    {
        $nombreArchivo = str_replace(['/', '\\', ' '], '_', $numeroOficio) . '.pdf';
        $rutaArchivo = 'oficios/' . $tramite->id . '/' . $nombreArchivo;

        Storage::put($rutaArchivo, $pdf->output());

        return $rutaArchivo;
    }

    /**
     * Descargar oficio directamente
     */
    public function descargarOficio(Tramite $tramite, string $tipoOficio): \Symfony\Component\HttpFoundation\Response
    {
        $oficioData = $this->generarOficio($tramite, $tipoOficio);
        
        if (empty($oficioData)) {
            abort(500, 'Error al generar el oficio');
        }

        $nombreArchivo = str_replace(['/', '\\', ' '], '_', $oficioData['numero_oficio']) . '.pdf';
        
        return $oficioData['pdf']->download($nombreArchivo);
    }

    /**
     * Mostrar oficio en el navegador
     */
    public function mostrarOficio(Tramite $tramite, string $tipoOficio): \Symfony\Component\HttpFoundation\Response
    {
        $oficioData = $this->generarOficio($tramite, $tipoOficio);
        
        if (empty($oficioData)) {
            abort(500, 'Error al generar el oficio');
        }

        return $oficioData['pdf']->stream();
    }
} 