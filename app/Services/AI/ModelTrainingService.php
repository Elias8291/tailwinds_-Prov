<?php

namespace App\Services\AI;

use App\Models\AI\AiDocumentModel;
use App\Models\AI\AiTrainingData;
use App\Models\Documento;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Services\AI\DocumentAnalysisService;
use App\Services\AI\PdfTextExtractor;
use App\Services\AI\DocumentFeatureExtractor;

class ModelTrainingService
{
    protected PdfTextExtractor $pdfExtractor;
    protected DocumentFeatureExtractor $featureExtractor;

    public function __construct(
        PdfTextExtractor $pdfExtractor,
        DocumentFeatureExtractor $featureExtractor
    ) {
        $this->pdfExtractor = $pdfExtractor;
        $this->featureExtractor = $featureExtractor;
    }

    /**
     * Agregar datos de entrenamiento desde un archivo subido
     */
    public function addTrainingData(
        UploadedFile $file, 
        int $documentoId, 
        string $documentType
    ): ?AiTrainingData {
        try {
            // Verificar que el documento existe
            $documento = Documento::findOrFail($documentoId);
            
            // Generar hash del archivo
            $tempPath = $file->getRealPath();
            $fileHash = md5_file($tempPath);
            
            // Verificar si ya existe
            $existing = AiTrainingData::where('file_hash', $fileHash)->first();
            if ($existing) {
                throw new Exception('Este archivo ya existe en los datos de entrenamiento');
            }

            // Almacenar archivo
            $fileName = uniqid('training_') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('ai_training_data', $fileName, 'private');
            
            // Extraer características
            $features = $this->extractFeatures($filePath);
            
            // Crear registro de datos de entrenamiento
            $trainingData = AiTrainingData::create([
                'documento_id' => $documentoId,
                'file_path' => $filePath,
                'file_hash' => $fileHash,
                'document_type' => $documentType,
                'extracted_features' => $features,
                'is_validated' => false,
            ]);

            Log::info('Datos de entrenamiento agregados', [
                'training_data_id' => $trainingData->id,
                'document_type' => $documentType,
                'file_hash' => $fileHash
            ]);

            return $trainingData;

        } catch (Exception $e) {
            Log::error('Error agregando datos de entrenamiento', [
                'error' => $e->getMessage(),
                'documento_id' => $documentoId,
                'document_type' => $documentType
            ]);
            return null;
        }
    }

    /**
     * Entrenar un nuevo modelo
     */
    public function trainModel(array $documentTypes = [], string $modelName = null): AiDocumentModel
    {
        $modelName = $modelName ?: 'Model_' . now()->format('Y_m_d_H_i_s');
        
        Log::info('Iniciando entrenamiento de modelo', [
            'model_name' => $modelName,
            'document_types' => $documentTypes
        ]);

        // Get validated training data
        $trainingData = $this->getTrainingData($documentTypes);
        
        if ($trainingData->isEmpty()) {
            throw new \Exception('No hay suficientes datos de entrenamiento validados');
        }

        // Create new model
        $model = AiDocumentModel::create([
            'name' => $modelName,
            'description' => 'Modelo entrenado automáticamente en ' . now()->format('Y-m-d H:i:s'),
            'status' => 'training',
            'supported_document_types' => $this->getSupportedDocumentTypes($trainingData),
            'model_parameters' => $this->generateModelParameters($trainingData),
            'version' => '1.0.0'
        ]);

        try {
            // Simulate model training (in real implementation, this would use actual ML)
            $this->performTraining($model, $trainingData);
            
            // Calculate accuracy metrics
            $accuracy = $this->calculateModelAccuracy($model, $trainingData);
            
            // Update model with training results
            $model->update([
                'status' => 'active',
                'accuracy' => $accuracy,
                'training_documents_count' => $trainingData->count(),
                'trained_at' => now()
            ]);

            Log::info('Modelo entrenado exitosamente', [
                'model_id' => $model->id,
                'accuracy' => $accuracy,
                'training_samples' => $trainingData->count()
            ]);

            return $model;

        } catch (\Exception $e) {
            $model->update(['status' => 'deprecated']);
            
            Log::error('Error durante el entrenamiento del modelo', [
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Error durante el entrenamiento: ' . $e->getMessage());
        }
    }

    /**
     * Validar datos de entrenamiento manualmente
     */
    public function validateTrainingData(AiTrainingData $trainingData, bool $isValid, string $notes = ''): void
    {
        $status = $isValid ? 'validated' : 'rejected';
        
        $trainingData->update([
            'validation_status' => $status,
            'validated_by' => \Illuminate\Support\Facades\Auth::id(),
            'validated_at' => now(),
            'validation_notes' => $notes
        ]);

        Log::info('Datos de entrenamiento validados', [
            'training_data_id' => $trainingData->id,
            'is_valid' => $isValid,
            'validator' => \Illuminate\Support\Facades\Auth::id()
        ]);
    }

    /**
     * Obtener estadísticas de entrenamiento
     */
    public function getTrainingStats(): array
    {
        $stats = AiTrainingData::getStats();
        $distribution = AiTrainingData::getDocumentTypeDistribution();

        return [
            'training_data' => $stats,
            'document_type_distribution' => $distribution,
            'models' => [
                'total' => AiDocumentModel::count(),
                'active' => AiDocumentModel::where('status', 'active')->count(),
                'training' => AiDocumentModel::where('status', 'training')->count()
            ]
        ];
    }

    /**
     * Procesar documentos existentes para entrenamiento
     */
    public function processExistingDocuments(array $documentoSolicitanteIds, string $documentType): int
    {
        $processed = 0;
        
        foreach ($documentoSolicitanteIds as $id) {
            try {
                $documentoSolicitante = \App\Models\DocumentoSolicitante::findOrFail($id);
                
                // Obtener la ruta del archivo
                $filePath = $this->getDocumentFilePath($documentoSolicitante);
                
                if (!$filePath || !Storage::exists($filePath)) {
                    continue;
                }

                // Generar hash del archivo
                $fullPath = Storage::path($filePath);
                $fileHash = md5_file($fullPath);
                
                // Verificar si ya existe
                if (AiTrainingData::where('file_hash', $fileHash)->exists()) {
                    continue;
                }

                // Extraer características
                $features = $this->extractFeatures($filePath);
                
                // Crear datos de entrenamiento
                AiTrainingData::create([
                    'documento_id' => $documentoSolicitante->documento_id,
                    'file_path' => $filePath,
                    'file_hash' => $fileHash,
                    'document_type' => $documentType,
                    'extracted_features' => $features,
                    'is_validated' => false,
                ]);
                
                $processed++;

            } catch (Exception $e) {
                Log::warning('Error procesando documento para entrenamiento', [
                    'documento_solicitante_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $processed;
    }

    /**
     * Extraer características de un archivo para entrenamiento
     */
    private function extractFeatures(string $filePath): array
    {
        $text = $this->pdfExtractor->extractText($filePath);
        $features = $this->featureExtractor->extractFeatures($filePath, $text);

        // Agregar características derivadas
        $textFeatures = $this->featureExtractor->extractTextFeatures($features['text_content']);
        $features['text_features'] = $textFeatures;

        return $features;
    }

    /**
     * Obtener datos de entrenamiento validados para tipos específicos
     */
    private function getValidatedTrainingData(array $documentTypes): array
    {
        return AiTrainingData::whereIn('document_type', $documentTypes)
            ->where('is_validated', true)
            ->where('is_used_for_training', true)
            ->get()
            ->toArray();
    }

    /**
     * Simular el proceso de entrenamiento
     */
    private function performTraining(AiDocumentModel $model, \Illuminate\Database\Eloquent\Collection $trainingData): void
    {
        // In a real ML implementation, this would:
        // 1. Prepare feature vectors
        // 2. Split data into training/validation sets
        // 3. Train the actual ML model
        // 4. Validate the model performance
        // 5. Save the trained model

        // For our rule-based system, we'll analyze the training data patterns
        $patterns = $this->analyzeTrainingPatterns($trainingData);
        
        // Store the patterns in model parameters
        $currentParams = $model->model_parameters ?? [];
        $model->update([
            'model_parameters' => array_merge($currentParams, [
                'learned_patterns' => $patterns,
                'training_completed_at' => now()->toISOString()
            ])
        ]);

        Log::info('Patrones de entrenamiento analizados', [
            'model_id' => $model->id,
            'pattern_count' => count($patterns)
        ]);
    }

    /**
     * Calcular precisión del modelo
     */
    private function calculateModelAccuracy(AiDocumentModel $model, \Illuminate\Database\Eloquent\Collection $trainingData): float
    {
        // Simulate accuracy calculation
        // In real implementation, this would use proper cross-validation
        
        $totalSamples = $trainingData->count();
        if ($totalSamples === 0) return 0.0;

        // For simulation, calculate based on data quality and distribution
        $documentTypes = $trainingData->pluck('expected_document_type')->unique();
        $distribution = $trainingData->groupBy('expected_document_type')->map->count();
        
        // Base accuracy starts at 0.7
        $baseAccuracy = 0.7;
        
        // Bonus for having multiple document types
        if ($documentTypes->count() > 1) {
            $baseAccuracy += 0.1;
        }
        
        // Bonus for balanced distribution
        $maxSamples = $distribution->max();
        $minSamples = $distribution->min();
        if ($maxSamples > 0 && ($minSamples / $maxSamples) > 0.5) {
            $baseAccuracy += 0.1;
        }
        
        // Bonus for larger training set
        if ($totalSamples > 20) {
            $baseAccuracy += 0.05;
        }
        
        return min(0.99, $baseAccuracy);
    }

    /**
     * Guardar configuración del modelo entrenado
     */
    private function saveModelConfiguration(AiDocumentModel $model, array $trainingData): void
    {
        $config = [
            'model_id' => $model->id,
            'version' => $model->version,
            'training_date' => now()->toISOString(),
            'supported_types' => $model->supported_document_types,
            'training_data_count' => count($trainingData),
            'rules' => $this->generateRulesFromTrainingData($trainingData),
        ];

        Storage::put($model->model_path, json_encode($config, JSON_PRETTY_PRINT));
    }

    /**
     * Generar reglas desde datos de entrenamiento
     */
    private function generateRulesFromTrainingData(array $trainingData): array
    {
        $rules = [];
        
        foreach ($trainingData as $data) {
            $features = $data['extracted_features'] ?? [];
            $textContent = strtolower($features['text_content'] ?? '');
            $documentType = $data['document_type'];
            
            // Analizar palabras clave comunes para cada tipo
            $words = str_word_count($textContent, 1);
            $significantWords = array_filter($words, function($word) {
                return strlen($word) > 4 && !in_array(strtolower($word), [
                    'documento', 'archivo', 'página', 'número', 'fecha'
                ]);
            });
            
            if (!isset($rules[$documentType])) {
                $rules[$documentType] = [
                    'keywords' => [],
                    'patterns' => [],
                    'metadata_rules' => []
                ];
            }
            
            $rules[$documentType]['keywords'] = array_merge(
                $rules[$documentType]['keywords'], 
                array_slice($significantWords, 0, 5)
            );
        }
        
        return $rules;
    }

    /**
     * Obtener ruta del archivo del documento
     */
    private function getDocumentFilePath(\App\Models\DocumentoSolicitante $documentoSolicitante): ?string
    {
        try {
            $rutaArchivo = $documentoSolicitante->ruta_archivo;
            
            if (!$rutaArchivo) {
                return null;
            }

            // Intentar desencriptar si es necesario
            try {
                return \Illuminate\Support\Facades\Crypt::decryptString($rutaArchivo);
            } catch (Exception $e) {
                return $rutaArchivo;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get training data for model training
     */
    protected function getTrainingData(array $documentTypes = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = AiTrainingData::where('validation_status', 'validated');
        
        if (!empty($documentTypes)) {
            $query->whereIn('expected_document_type', $documentTypes);
        }
        
        return $query->get();
    }

    /**
     * Get supported document types from training data
     */
    protected function getSupportedDocumentTypes(\Illuminate\Database\Eloquent\Collection $trainingData): array
    {
        return $trainingData->pluck('expected_document_type')->unique()->values()->toArray();
    }

    /**
     * Generate model parameters based on training data
     */
    protected function generateModelParameters(\Illuminate\Database\Eloquent\Collection $trainingData): array
    {
        $distribution = $trainingData->groupBy('expected_document_type')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();

        return [
            'algorithm' => 'rule_based_classifier',
            'training_distribution' => $distribution,
            'total_samples' => $trainingData->count(),
            'feature_weights' => [
                'keywords' => 0.4,
                'structure' => 0.3,
                'file_characteristics' => 0.2,
                'format' => 0.1
            ],
            'confidence_thresholds' => [
                'auto_approve' => 0.95,
                'needs_review' => 0.7
            ],
            'created_at' => now()->toISOString()
        ];
    }

    /**
     * Analyze patterns in training data
     */
    protected function analyzeTrainingPatterns(\Illuminate\Database\Eloquent\Collection $trainingData): array
    {
        $patterns = [];

        foreach ($trainingData->groupBy('expected_document_type') as $documentType => $samples) {
            $patterns[$documentType] = [
                'sample_count' => $samples->count(),
                'common_keywords' => $this->extractCommonKeywords($samples),
                'average_length' => $samples->avg(function ($sample) {
                    return strlen($sample->extracted_text ?? '');
                }),
                'file_characteristics' => $this->analyzeFileCharacteristics($samples)
            ];
        }

        return $patterns;
    }

    /**
     * Extract common keywords from samples
     */
    protected function extractCommonKeywords(\Illuminate\Database\Eloquent\Collection $samples): array
    {
        $allText = $samples->pluck('extracted_text')->implode(' ');
        $words = str_word_count(strtolower($allText), 1);
        
        // Count word frequencies
        $wordCount = array_count_values($words);
        
        // Filter out common words and short words
        $commonWords = ['el', 'la', 'de', 'que', 'y', 'a', 'en', 'un', 'es', 'se', 'no', 'te', 'lo'];
        $filteredWords = array_filter($wordCount, function ($count, $word) use ($commonWords) {
            return strlen($word) > 3 && !in_array($word, $commonWords) && $count > 1;
        }, ARRAY_FILTER_USE_BOTH);
        
        // Sort by frequency and take top 10
        arsort($filteredWords);
        return array_slice(array_keys($filteredWords), 0, 10);
    }

    /**
     * Analyze file characteristics from samples
     */
    protected function analyzeFileCharacteristics(\Illuminate\Database\Eloquent\Collection $samples): array
    {
        return [
            'average_file_size' => $samples->avg('file_size'),
            'file_types' => $samples->pluck('file_type')->unique()->values()->toArray(),
            'common_features' => $this->getCommonFeatures($samples)
        ];
    }

    /**
     * Get common features across samples
     */
    protected function getCommonFeatures(\Illuminate\Database\Eloquent\Collection $samples): array
    {
        $allFeatures = [];
        
        foreach ($samples as $sample) {
            if ($sample->document_features) {
                foreach ($sample->document_features as $key => $value) {
                    if (!isset($allFeatures[$key])) {
                        $allFeatures[$key] = [];
                    }
                    $allFeatures[$key][] = $value;
                }
            }
        }

        // Calculate averages for numeric features
        $commonFeatures = [];
        foreach ($allFeatures as $feature => $values) {
            if (is_numeric($values[0] ?? null)) {
                $commonFeatures[$feature] = array_sum($values) / count($values);
            }
        }

        return $commonFeatures;
    }
} 