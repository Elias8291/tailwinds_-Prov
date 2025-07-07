<?php

namespace App\Services;

use App\Models\Oficio;
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
    public function generarOficio(Tramite $tramite, string $tipoOficio): ?Oficio
    {
        try {
            // Generar número de oficio
            $numeroOficio = Oficio::generarNumeroOficio();

            // Generar el PDF
            $pdf = $this->generarPDF($tramite, $tipoOficio, $numeroOficio);

            // Guardar el archivo
            $rutaArchivo = $this->guardarArchivo($pdf, $tramite, $numeroOficio);

            // Generar hash del archivo
            $hashArchivo = Oficio::generarHashArchivo($pdf->output());

            // Crear registro en la base de datos
            $oficio = Oficio::create([
                'tramite_id' => $tramite->id,
                'numero_oficio' => $numeroOficio,
                'tipo_oficio' => $tipoOficio,
                'ruta_archivo' => $rutaArchivo,
                'hash_archivo' => $hashArchivo,
                'estado' => 'generado',
                'generado_por' => Auth::id()
            ]);

            Log::info('Oficio generado exitosamente', [
                'oficio_id' => $oficio->id,
                'tramite_id' => $tramite->id,
                'tipo' => $tipoOficio,
                'numero' => $numeroOficio
            ]);

            return $oficio;

        } catch (\Exception $e) {
            Log::error('Error al generar oficio:', [
                'tramite_id' => $tramite->id,
                'tipo' => $tipoOficio,
                'error' => $e->getMessage()
            ]);

            return null;
        }
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
     * Guardar el archivo PDF
     */
    protected function guardarArchivo($pdf, Tramite $tramite, string $numeroOficio): string
    {
        $nombreArchivo = str_replace(['/', '\\', ' '], '_', $numeroOficio) . '.pdf';
        $rutaArchivo = 'oficios/' . $tramite->id . '/' . $nombreArchivo;

        Storage::put($rutaArchivo, $pdf->output());

        return $rutaArchivo;
    }

    /**
     * Verificar la integridad de un oficio
     */
    public function verificarIntegridad(Oficio $oficio): bool
    {
        return $oficio->verificarHash();
    }

    /**
     * Actualizar el estado de un oficio
     */
    public function actualizarEstado(Oficio $oficio, string $nuevoEstado, ?string $observaciones = null): bool
    {
        try {
            $oficio->update([
                'estado' => $nuevoEstado,
                'observaciones' => $observaciones
            ]);

            Log::info('Estado de oficio actualizado', [
                'oficio_id' => $oficio->id,
                'estado_anterior' => $oficio->getOriginal('estado'),
                'nuevo_estado' => $nuevoEstado
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de oficio:', [
                'oficio_id' => $oficio->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Obtener la ruta de descarga de un oficio
     */
    public function obtenerRutaDescarga(Oficio $oficio): ?string
    {
        if (!Storage::exists($oficio->ruta_archivo)) {
            return null;
        }

        return $oficio->getRutaCompleta();
    }
} 