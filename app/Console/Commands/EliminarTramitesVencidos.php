<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tramite;
use App\Models\Notificacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EliminarTramitesVencidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tramites:eliminar-vencidos 
                            {--force : Forzar eliminación sin confirmación}
                            {--hours=48 : Número de horas para considerar un trámite como vencido}
                            {--dry-run : Solo mostrar qué trámites serían eliminados sin eliminarlos realmente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina automáticamente los trámites que han pasado 48 horas sin completarse';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        $this->info("🔍 Buscando trámites vencidos (>{$hours} horas)...");
        
        // Calcular fecha límite según las horas especificadas
        $fechaLimite = Carbon::now()->subHours($hours);
        
        // Buscar trámites que:
        // 1. Tengan fecha_inicio establecida
        // 2. NO tengan fecha_finalizacion (no completados)
        // 3. Su fecha_inicio sea anterior a las horas especificadas
        $tramitesVencidos = Tramite::where('fecha_inicio', '<=', $fechaLimite)
            ->whereNull('fecha_finalizacion')
            ->whereNotNull('fecha_inicio')
            ->with(['solicitante', 'detalleTramite', 'documentosSolicitante', 'actividades', 'accionistas', 'seccionesRevision', 'progresoSecciones'])
            ->get();

        if ($tramitesVencidos->isEmpty()) {
            $this->info('✅ No se encontraron trámites vencidos.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Se encontraron {$tramitesVencidos->count()} trámites vencidos:");

        foreach ($tramitesVencidos as $tramite) {
            $tiempoVencido = Carbon::now()->diffInHours($tramite->fecha_inicio);
            $this->line("   - Trámite ID: {$tramite->id} | Tipo: {$tramite->tipo_tramite} | Inicio: {$tramite->fecha_inicio->format('d/m/Y H:i')} | Vencido hace: {$tiempoVencido}h");
        }

        // Si es dry-run, solo mostrar información
        if ($dryRun) {
            $this->info('👀 Modo dry-run: No se eliminará nada. Estos trámites serían eliminados en una ejecución real.');
            return Command::SUCCESS;
        }

        // Solo pedir confirmación si no se usa --force
        if (!$force) {
            if (!$this->confirm('¿Desea eliminar estos trámites? Esta acción no se puede deshacer.')) {
                $this->info('❌ Operación cancelada.');
                return Command::SUCCESS;
            }
        } else {
            $this->info('🤖 Eliminando automáticamente (modo --force activado)...');
        }

        $eliminados = 0;
        $errores = 0;

        foreach ($tramitesVencidos as $tramite) {
            try {
                DB::beginTransaction();
                
                // Log de la eliminación
                Log::info("🗑️ Eliminando trámite vencido", [
                    'tramite_id' => $tramite->id,
                    'tipo_tramite' => $tramite->tipo_tramite,
                    'fecha_inicio' => $tramite->fecha_inicio,
                    'solicitante_id' => $tramite->solicitante_id,
                    'progreso' => $tramite->progreso_tramite
                ]);

                // Enviar notificación al usuario antes de eliminar
                $this->enviarNotificacionEliminacion($tramite);

                // Eliminar relaciones manualmente para asegurar eliminación en cascada
                
                // 1. Eliminar progreso de trámite
                $tramite->progresoSecciones()->delete();
                
                // 2. Eliminar secciones de revisión
                $tramite->seccionesRevision()->delete();
                
                // 3. Eliminar relaciones many-to-many
                $tramite->actividades()->detach();
                $tramite->accionistas()->detach();
                
                // 4. Eliminar documentos del solicitante
                $tramite->documentosSolicitante()->delete();
                
                // 5. Eliminar detalle del trámite
                if ($tramite->detalleTramite) {
                    $tramite->detalleTramite->delete();
                }
                
                // 6. Finalmente eliminar el trámite
                $tramite->delete();
                
                // 7. Si el solicitante no tiene más trámites, también eliminarlo
                if ($tramite->solicitante && $tramite->solicitante->tramites()->count() == 0) {
                    Log::info("🗑️ Eliminando solicitante sin trámites", [
                        'solicitante_id' => $tramite->solicitante->id,
                        'rfc' => $tramite->solicitante->rfc ?? 'N/A'
                    ]);
                    $tramite->solicitante->delete();
                }

                DB::commit();
                $eliminados++;
                
                $this->info("   ✅ Trámite {$tramite->id} eliminado correctamente");
                
            } catch (\Exception $e) {
                DB::rollBack();
                $errores++;
                
                Log::error("❌ Error al eliminar trámite vencido", [
                    'tramite_id' => $tramite->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $this->error("   ❌ Error al eliminar trámite {$tramite->id}: " . $e->getMessage());
            }
        }

        // Resumen final
        $this->info("\n📊 Resumen de la operación:");
        $this->line("   ✅ Trámites eliminados: {$eliminados}");
        if ($errores > 0) {
            $this->line("   ❌ Errores: {$errores}");
        }

        // Log final
        Log::info("🏁 Proceso de eliminación de trámites vencidos completado", [
            'eliminados' => $eliminados,
            'errores' => $errores,
            'horas_limite' => $hours,
            'fecha_limite' => $fechaLimite->format('Y-m-d H:i:s'),
            'modo_automatico' => $force,
            'total_encontrados' => $tramitesVencidos->count()
        ]);

        return Command::SUCCESS;
    }

    /**
     * Envía notificación al usuario informando sobre la eliminación de su trámite
     *
     * @param Tramite $tramite
     * @return void
     */
    private function enviarNotificacionEliminacion($tramite)
    {
        try {
            // Obtener el usuario del trámite
            $usuario = $tramite->solicitante->usuario ?? null;
            
            if (!$usuario) {
                Log::warning("No se pudo enviar notificación: usuario no encontrado", [
                    'tramite_id' => $tramite->id,
                    'solicitante_id' => $tramite->solicitante_id ?? 'N/A'
                ]);
                return;
            }

            // Calcular tiempo transcurrido desde el inicio
            $horasTranscurridas = Carbon::now()->diffInHours($tramite->fecha_inicio);
            $diasTranscurridos = floor($horasTranscurridas / 24);
            $horasRestantes = $horasTranscurridas % 24;
            
            // Crear mensaje personalizado según el tipo de trámite y progreso
            $tipoTramite = strtolower($tramite->tipo_tramite);
            $seccionActual = $tramite->getNombreSeccionActual();
            $nombreSolicitante = $tramite->solicitante->nombre_completo ?? 'Usuario';
            
            $titulo = "⚠️ Trámite Eliminado por Tiempo Vencido";
            
            $mensaje = "Su trámite de {$tramite->tipo_tramite} fue eliminado automáticamente por no completarse en 48 horas.\n\n" .
                      "📋 Detalles:\n" .
                      "• Tipo: {$tramite->tipo_tramite}\n" .
                      "• Inicio: {$tramite->fecha_inicio->format('d/m/Y H:i')}\n" .
                      "• Progreso: {$seccionActual}\n\n" .
                      "🔄 Puede iniciar un nuevo trámite cuando esté listo.\n" .
                      "⏰ Recuerde completarlo dentro de 48 horas.";

            // Crear la notificación
            Notificacion::crearParaUsuario(
                $titulo,
                $mensaje,
                'Advertencia', // Tipo de notificación
                $usuario->id
            );

            Log::info("✉️ Notificación de eliminación enviada", [
                'tramite_id' => $tramite->id,
                'usuario_id' => $usuario->id,
                'usuario_email' => $usuario->email ?? 'N/A',
                'tipo_tramite' => $tramite->tipo_tramite,
                'horas_transcurridas' => $horasTranscurridas
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error al enviar notificación de eliminación", [
                'tramite_id' => $tramite->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
