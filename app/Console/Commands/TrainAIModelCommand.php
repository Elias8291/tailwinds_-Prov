<?php

namespace App\Console\Commands;

use App\Services\AI\ModelTrainingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TrainAIModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:train-model 
                            {name : Nombre del modelo}
                            {--types=* : Tipos de documentos a incluir en el entrenamiento}
                            {--algorithm=rule_based_enhanced : Algoritmo de entrenamiento}
                            {--min-samples=10 : Número mínimo de muestras por tipo}';

    /**
     * The console command description.
     */
    protected $description = 'Entrenar un nuevo modelo de IA para reconocimiento de documentos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('name');
        $documentTypes = $this->option('types');
        $algorithm = $this->option('algorithm');
        $minSamples = (int) $this->option('min-samples');
        
        $this->info("🤖 Iniciando entrenamiento del modelo: {$modelName}");
        
        // Si no se especificaron tipos, mostrar los disponibles
        if (empty($documentTypes)) {
            $availableTypes = $this->getAvailableDocumentTypes();
            
            if (empty($availableTypes)) {
                $this->error('❌ No hay datos de entrenamiento disponibles.');
                $this->info('💡 Primero sube algunos documentos de entrenamiento usando el panel web.');
                return Command::FAILURE;
            }
            
            $this->info('📋 Tipos de documentos disponibles:');
            foreach ($availableTypes as $type => $count) {
                $this->line("   • {$type}: {$count} documentos");
            }
            
            $documentTypes = $this->choice(
                'Selecciona los tipos de documentos para entrenar (separados por coma)',
                array_keys($availableTypes),
                null,
                null,
                true
            );
        }
        
        $this->info("📊 Tipos seleccionados: " . implode(', ', $documentTypes));
        
        // Verificar que hay suficientes datos
        $this->info('🔍 Verificando datos de entrenamiento...');
        $insufficientTypes = $this->checkTrainingDataSufficiency($documentTypes, $minSamples);
        
        if (!empty($insufficientTypes)) {
            $this->error('❌ Datos de entrenamiento insuficientes para:');
            foreach ($insufficientTypes as $type => $count) {
                $this->line("   • {$type}: {$count} documentos (mínimo: {$minSamples})");
            }
            
            if (!$this->confirm('¿Continuar con el entrenamiento de todos modos?')) {
                return Command::FAILURE;
            }
        }
        
        $this->info('⚙️ Configurando parámetros de entrenamiento...');
        $trainingParameters = [
            'algorithm' => $algorithm,
            'min_samples_per_type' => $minSamples,
            'trained_via' => 'artisan_command',
            'trained_at' => now()->toISOString(),
        ];
        
        $this->info('🚀 Iniciando entrenamiento...');
        
        $trainingService = new ModelTrainingService();
        
        try {
            $model = $trainingService->trainModel(
                $modelName,
                $documentTypes,
                $trainingParameters
            );
            
            if ($model) {
                $this->info('✅ Modelo entrenado exitosamente!');
                $this->newLine();
                $this->info("📋 Detalles del modelo:");
                $this->info("   • ID: {$model->id}");
                $this->info("   • Nombre: {$model->name}");
                $this->info("   • Versión: {$model->version}");
                $this->info("   • Estado: {$model->status}");
                $this->info("   • Precisión: " . number_format($model->accuracy * 100, 2) . "%");
                $this->info("   • Muestras de entrenamiento: {$model->training_samples_count}");
                $this->info("   • Tipos soportados: " . implode(', ', $model->supported_document_types));
                
                if ($this->confirm('¿Activar este modelo como el modelo principal?', true)) {
                    $model->activate();
                    $this->info('🎯 Modelo activado como modelo principal.');
                }
                
                $this->newLine();
                $this->info('💡 Puedes ver más detalles en el panel web: /ai/models/' . $model->id);
                
            } else {
                $this->error('❌ Error durante el entrenamiento del modelo.');
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('Error en comando de entrenamiento', [
                'model_name' => $modelName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Obtener tipos de documentos disponibles
     */
    private function getAvailableDocumentTypes(): array
    {
        return \App\Models\AI\AiTrainingData::selectRaw('document_type, COUNT(*) as count')
            ->where('is_validated', true)
            ->groupBy('document_type')
            ->pluck('count', 'document_type')
            ->toArray();
    }
    
    /**
     * Verificar suficiencia de datos de entrenamiento
     */
    private function checkTrainingDataSufficiency(array $documentTypes, int $minSamples): array
    {
        $insufficient = [];
        
        foreach ($documentTypes as $type) {
            $count = \App\Models\AI\AiTrainingData::where('document_type', $type)
                ->where('is_validated', true)
                ->where('is_used_for_training', true)
                ->count();
                
            if ($count < $minSamples) {
                $insufficient[$type] = $count;
            }
        }
        
        return $insufficient;
    }
} 