<?php

namespace App\Services\AI;

use App\Models\AI\AiDocumentModel;
use App\Models\AI\AiValidationResult;
use App\Models\DocumentoSolicitante;
use App\Services\AI\PdfTextExtractor;
use App\Services\AI\DocumentFeatureExtractor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DocumentAnalysisService
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
     * Analyze a document and predict its type
     */
    public function analyzeDocument(DocumentoSolicitante $documentoSolicitante): AiValidationResult
    {
        $startTime = microtime(true);
        
        // Get the default AI model
        $aiModel = AiDocumentModel::getDefault();
        if (!$aiModel) {
            throw new \Exception('No hay un modelo de IA activo disponible');
        }

        // Decrypt and get file path
        $encryptedPath = $documentoSolicitante->ruta_archivo;
        $filePath = decrypt($encryptedPath);
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('El archivo del documento no se encuentra');
        }

        try {
            // Extract text from document
            $extractedText = $this->pdfExtractor->extractText($fullPath);
            
            // Extract features
            $features = $this->featureExtractor->extractFeatures($fullPath, $extractedText);
            
            // Perform classification
            $classificationResults = $this->classifyDocument($extractedText, $features, $aiModel);
            
            // Get the best prediction
            $bestPrediction = collect($classificationResults)->sortByDesc('confidence')->first();
            
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds

            // Create validation result
            $validationResult = AiValidationResult::create([
                'documento_solicitante_id' => $documentoSolicitante->id,
                'ai_model_id' => $aiModel->id,
                'predicted_document_type' => $bestPrediction['document_type'],
                'confidence_score' => $bestPrediction['confidence'],
                'classification_results' => $classificationResults,
                'extracted_features' => $features,
                'extracted_text_summary' => $this->generateTextSummary($extractedText),
                'processed_at' => now(),
                'processing_time_ms' => $processingTime,
                'validation_status' => $this->determineValidationStatus($bestPrediction['confidence'])
            ]);

            // Update model usage
            $aiModel->recordUsage();

            Log::info('Documento analizado exitosamente', [
                'documento_solicitante_id' => $documentoSolicitante->id,
                'predicted_type' => $bestPrediction['document_type'],
                'confidence' => $bestPrediction['confidence'],
                'processing_time_ms' => $processingTime
            ]);

            return $validationResult;

        } catch (\Exception $e) {
            Log::error('Error al analizar documento', [
                'documento_solicitante_id' => $documentoSolicitante->id,
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);
            
            throw new \Exception('Error al analizar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Classify document based on extracted text and features
     */
    protected function classifyDocument(string $text, array $features, AiDocumentModel $model): array
    {
        // This is a rule-based classifier. In a real AI implementation,
        // this would use a trained machine learning model
        $documentTypes = [
            'Constancia de Situación Fiscal',
            'Acta de Nacimiento',
            'Credencial de Elector',
            'Comprobante de Domicilio',
            'CURP',
            'RFC'
        ];

        $results = [];
        $text = strtolower($text);

        foreach ($documentTypes as $type) {
            $confidence = $this->calculateConfidenceForType($text, $features, $type);
            $results[] = [
                'document_type' => $type,
                'confidence' => $confidence
            ];
        }

        return $results;
    }

    /**
     * Calculate confidence score for a specific document type
     */
    protected function calculateConfidenceForType(string $text, array $features, string $documentType): float
    {
        $confidence = 0.0;
        $keywords = $this->getKeywordsForDocumentType($documentType);

        // Keyword matching (40% weight)
        $keywordMatches = 0;
        foreach ($keywords as $keyword) {
            if (strpos($text, strtolower($keyword)) !== false) {
                $keywordMatches++;
            }
        }
        $keywordScore = $keywordMatches / count($keywords);
        $confidence += $keywordScore * 0.4;

        // Document structure analysis (30% weight)
        $structureScore = $this->analyzeDocumentStructure($text, $features, $documentType);
        $confidence += $structureScore * 0.3;

        // File characteristics (20% weight)
        $fileScore = $this->analyzeFileCharacteristics($features, $documentType);
        $confidence += $fileScore * 0.2;

        // Length and format analysis (10% weight)
        $formatScore = $this->analyzeFormatCharacteristics($text, $documentType);
        $confidence += $formatScore * 0.1;

        return min(1.0, max(0.0, $confidence));
    }

    /**
     * Get keywords for document type
     */
    protected function getKeywordsForDocumentType(string $documentType): array
    {
        $keywords = [
            'Constancia de Situación Fiscal' => [
                'constancia', 'situación fiscal', 'sat', 'rfc', 'hacienda', 'contribuyente'
            ],
            'Acta de Nacimiento' => [
                'acta', 'nacimiento', 'registro civil', 'nació', 'registro', 'civil'
            ],
            'Credencial de Elector' => [
                'credencial', 'elector', 'ine', 'instituto nacional electoral', 'vigencia'
            ],
            'Comprobante de Domicilio' => [
                'domicilio', 'recibo', 'factura', 'servicio', 'agua', 'luz', 'gas', 'teléfono'
            ],
            'CURP' => [
                'curp', 'clave única', 'población', 'registro', 'identidad'
            ],
            'RFC' => [
                'rfc', 'registro federal', 'contribuyente', 'hacienda', 'clave'
            ]
        ];

        return $keywords[$documentType] ?? [];
    }

    /**
     * Extract text from uploaded file (for training purposes)
     */
    public function extractText($file): string
    {
        try {
            if (is_string($file)) {
                // File path provided
                return $this->pdfExtractor->extractText($file);
            } else {
                // Uploaded file object
                $tempPath = $file->getRealPath();
                return $this->pdfExtractor->extractText($tempPath);
            }
        } catch (\Exception $e) {
            Log::warning('Error extracting text from file: ' . $e->getMessage());
            return 'Error al extraer texto del documento';
        }
    }

    /**
     * Extract features from uploaded file (for training purposes)
     */
    public function extractFeatures($file, string $extractedText = null): array
    {
        try {
            if (is_string($file)) {
                // File path provided
                return $this->featureExtractor->extractFeatures($file, $extractedText);
            } else {
                // Uploaded file object
                $tempPath = $file->getRealPath();
                return $this->featureExtractor->extractFeatures($tempPath, $extractedText);
            }
        } catch (\Exception $e) {
            Log::warning('Error extracting features from file: ' . $e->getMessage());
            return [
                'file_size' => $file->getSize() ?? 0,
                'file_type' => $file->getClientMimeType() ?? 'unknown',
                'text_length' => strlen($extractedText ?? ''),
                'error' => 'Error al extraer características'
            ];
        }
    }

    /**
     * Analyze document structure
     */
    protected function analyzeDocumentStructure(string $text, array $features, string $documentType): float
    {
        // Analyze text patterns specific to document types
        $score = 0.0;
        
        switch ($documentType) {
            case 'Constancia de Situación Fiscal':
                if (preg_match('/\b[A-Z]{3,4}\d{6}[A-Z0-9]{3}\b/', $text)) $score += 0.5; // RFC pattern
                if (strpos($text, 'servicio de administración tributaria') !== false) $score += 0.3;
                break;
                
            case 'Acta de Nacimiento':
                if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $text)) $score += 0.4; // Date pattern
                if (strpos($text, 'registro civil') !== false) $score += 0.4;
                break;
                
            case 'Credencial de Elector':
                if (preg_match('/\b[A-Z]{6}\d{8}[HM][A-Z]{5}\d{2}\b/', $text)) $score += 0.6; // CURP pattern
                if (strpos($text, 'instituto nacional electoral') !== false) $score += 0.3;
                break;
        }

        return min(1.0, $score);
    }

    /**
     * Analyze file characteristics
     */
    protected function analyzeFileCharacteristics(array $features, string $documentType): float
    {
        $score = 0.0;
        
        // PDF files are preferred for official documents
        if (isset($features['file_type']) && $features['file_type'] === 'pdf') {
            $score += 0.5;
        }

        // File size analysis
        if (isset($features['file_size'])) {
            $sizeKB = $features['file_size'] / 1024;
            if ($sizeKB > 50 && $sizeKB < 5000) { // Reasonable size for documents
                $score += 0.3;
            }
        }

        return $score;
    }

    /**
     * Analyze format characteristics
     */
    protected function analyzeFormatCharacteristics(string $text, string $documentType): float
    {
        $score = 0.0;
        $textLength = strlen($text);

        // Different document types have different typical lengths
        $expectedLengths = [
            'Constancia de Situación Fiscal' => [500, 2000],
            'Acta de Nacimiento' => [200, 1000],
            'Credencial de Elector' => [100, 500],
            'Comprobante de Domicilio' => [200, 800],
            'CURP' => [50, 300],
            'RFC' => [50, 200]
        ];

        if (isset($expectedLengths[$documentType])) {
            [$min, $max] = $expectedLengths[$documentType];
            if ($textLength >= $min && $textLength <= $max) {
                $score = 1.0;
            } else {
                // Partial score based on how close it is
                $score = max(0, 1 - abs($textLength - ($min + $max) / 2) / $max);
            }
        }

        return $score;
    }

    /**
     * Generate a summary of extracted text
     */
    protected function generateTextSummary(string $text): string
    {
        $summary = substr($text, 0, 500);
        if (strlen($text) > 500) {
            $summary .= '...';
        }
        return $summary;
    }

    /**
     * Determine validation status based on confidence
     */
    protected function determineValidationStatus(float $confidence): string
    {
        if ($confidence >= 0.95) {
            return 'auto_approved';
        } elseif ($confidence >= 0.8) {
            return 'pending_review';
        } else {
            return 'pending_review';
        }
    }

    /**
     * Batch analyze multiple documents
     */
    public function batchAnalyze(array $documentoSolicitanteIds): array
    {
        $results = [];
        $errors = [];

        foreach ($documentoSolicitanteIds as $id) {
            try {
                $documento = DocumentoSolicitante::findOrFail($id);
                $result = $this->analyzeDocument($documento);
                $results[] = $result;
            } catch (\Exception $e) {
                $errors[] = [
                    'documento_id' => $id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'successful' => $results,
            'errors' => $errors
        ];
    }
} 