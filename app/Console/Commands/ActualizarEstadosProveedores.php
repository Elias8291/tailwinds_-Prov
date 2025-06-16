<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Log;

class ActualizarEstadosProveedores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proveedores:actualizar-estados {--dry-run : Mostrar qué se actualizaría sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza automáticamente el estado de proveedores vencidos de Activo a Inactivo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando actualización de estados de proveedores...');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: Solo se mostrarán los cambios, sin aplicarlos');
            
            // En modo dry-run, solo mostramos qué proveedores se actualizarían
            $proveedoresVencidos = Proveedor::where('estado', 'Activo')
                                           ->where('fecha_vencimiento', '<', now()->startOfDay())
                                           ->get();
            
            if ($proveedoresVencidos->isEmpty()) {
                $this->info('✅ No hay proveedores activos que necesiten actualización');
                return 0;
            }
            
            $this->info("📋 Se encontraron {$proveedoresVencidos->count()} proveedores que serían actualizados:");
            
            foreach ($proveedoresVencidos as $proveedor) {
                $diasVencido = now()->startOfDay()->diffInDays($proveedor->fecha_vencimiento);
                $this->line("   • PV: {$proveedor->pv} | Vencido hace: {$diasVencido} días | Fecha: {$proveedor->fecha_vencimiento->format('d/m/Y')}");
            }
            
            return 0;
        }
        
        // Ejecutar actualización real
        $resultados = Proveedor::actualizarEstadosVencidos();
        
        if (empty($resultados)) {
            $this->info('✅ No hay proveedores que necesiten actualización');
            Log::info('ActualizarEstadosProveedores: No se encontraron proveedores para actualizar');
        } else {
            $this->info("✅ Se actualizaron {" . count($resultados) . "} proveedores:");
            
            foreach ($resultados as $resultado) {
                $this->line("   • PV: {$resultado['pv']} | {$resultado['estado_anterior']} → {$resultado['estado_nuevo']}");
            }
            
            // Log para registro
            Log::info('ActualizarEstadosProveedores: Actualizados ' . count($resultados) . ' proveedores', [
                'proveedores_actualizados' => $resultados
            ]);
        }
        
        $this->info('🎉 Proceso completado exitosamente');
        
        return 0;
    }
}
