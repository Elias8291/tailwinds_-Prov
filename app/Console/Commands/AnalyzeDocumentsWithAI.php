<?php

namespace App\Console\Commands;

use App\Models\DocumentoSolicitante;
use App\Services\AI\DocumentAnalysisService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnalyzeDocumentsWithAI extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:analyze-documents 
                            {--batch-size=10 : Número de documentos a procesar por lote}
                            {--status=pendiente : Estado de documentos a analizar}
                            {--force : Forzar análisis incluso si ya fue analizado}';

    /**
     * The console command description.
     */
    protected $description = 'Analizar documentos subidos usando inteligencia artificial';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Iniciando análisis de documentos con IA...');
        
        $batchSize = (int) $this->option('batch-size');
        $status = $this->option('status');
        $force = $this->option('force');
        
        $analysisService = new DocumentAnalysisService();
        
        // Obtener documentos para analizar
        $query = DocumentoSolicitante::whereNotNull('ruta_archivo');
        
        if (!$force) {
            // Solo analizar documentos que no han sido analizados
            $query->whereDoesntHave('aiValidationResults');
        }
        
        if ($status !== 'all') {
            $query->where('estado', $status);
        }
        
        $documentos = $query->limit($batchSize)->get();
        
        if ($documentos->isEmpty()) {
            $this->info('✅ No hay documentos para analizar.');
            return Command::SUCCESS;
        }
        
        $this->info("📊 Analizando {$documentos->count()} documentos...");
        
        $progressBar = $this->output->createProgressBar($documentos->count());
        $progressBar->start();
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($documentos as $documento) {
            try {
                $result = $analysisService->analyzeDocument($documento);
                
                if ($result) {
                    $successCount++;
                    $this->logAnalysisResult($documento, $result);
                } else {
                    $errorCount++;
                    Log::warning('Error analizando documento', [
                        'documento_id' => $documento->id
                    ]);
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Excepción analizando documento', [
                    'documento_id' => $documento->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info("✅ Análisis completado:");
        $this->info("   • Documentos analizados exitosamente: {$successCount}");
        $this->info("   • Errores: {$errorCount}");
        
        // Mostrar estadísticas de confianza
        $this->showConfidenceStats();
        
        return Command::SUCCESS;
    }
    
    /**
     * Registrar resultado del análisis
     */
    private function logAnalysisResult(DocumentoSolicitante $documento, $result): void
    {
        $confidenceLevel = '';
        if ($result->confidence_score >= 0.9) {
            $confidenceLevel = '🟢 Alta';
        } elseif ($result->confidence_score >= 0.7) {
            $confidenceLevel = '🟡 Media';
        } else {
            $confidenceLevel = '🔴 Baja';
        }
        
        if ($this->getOutput()->isVerbose()) {
            $this->line(sprintf(
                "   📄 Documento %d: %s (Confianza: %s - %.1f%%)",
                $documento->id,
                $result->predicted_document_type,
                $confidenceLevel,
                $result->confidence_score * 100
            ));
        }
    }
    
    /**
     * Mostrar estadísticas de confianza
     */
    private function showConfidenceStats(): void
    {
        $stats = \App\Models\AI\AiValidationResult::selectRaw('
            COUNT(*) as total,
            AVG(confidence_score) as avg_confidence,
            SUM(CASE WHEN confidence_score >= 0.9 THEN 1 ELSE 0 END) as high_confidence,
            SUM(CASE WHEN confidence_score >= 0.7 AND confidence_score < 0.9 THEN 1 ELSE 0 END) as medium_confidence,
            SUM(CASE WHEN confidence_score < 0.7 THEN 1 ELSE 0 END) as low_confidence
        ')->first();
        
        if ($stats && $stats->total > 0) {
            $this->newLine();
            $this->info("📊 Estadísticas de confianza:");
            $this->info(sprintf("   • Confianza promedio: %.1f%%", $stats->avg_confidence * 100));
            $this->info(sprintf("   • Alta confianza (≥90%%): %d (%.1f%%)", 
                $stats->high_confidence, 
                ($stats->high_confidence / $stats->total) * 100
            ));
            $this->info(sprintf("   • Confianza media (70-89%%): %d (%.1f%%)", 
                $stats->medium_confidence,
                ($stats->medium_confidence / $stats->total) * 100
            ));
            $this->info(sprintf("   • Baja confianza (<70%%): %d (%.1f%%)", 
                $stats->low_confidence,
                ($stats->low_confidence / $stats->total) * 100
            ));
        }
    }
} 