<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentFeatureExtractor
{
    /**
     * Extract features from a document
     */
    public function extractFeatures(string $filePath, string $extractedText = ''): array
    {
        try {
            $features = [];

            // Basic file features
            $features = array_merge($features, $this->extractFileFeatures($filePath));

            // Text-based features
            if (!empty($extractedText)) {
                $features = array_merge($features, $this->extractTextFeatures($extractedText));
            }

            // Document structure features
            $features = array_merge($features, $this->extractStructureFeatures($extractedText));

            // Pattern-based features
            $features = array_merge($features, $this->extractPatternFeatures($extractedText));

            Log::info('Features extraídas exitosamente', [
                'file_path' => $filePath,
                'feature_count' => count($features)
            ]);

            return $features;

        } catch (\Exception $e) {
            Log::error('Error al extraer features del documento', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Extract basic file features
     */
    protected function extractFileFeatures(string $filePath): array
    {
        $features = [];

        // File size
        $features['file_size'] = filesize($filePath);
        
        // File type
        $features['file_type'] = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // MIME type
        $features['mime_type'] = mime_content_type($filePath);
        
        // File modification time
        $features['modification_time'] = filemtime($filePath);
        
        // File creation time (if available)
        if (function_exists('filectime')) {
            $features['creation_time'] = filectime($filePath);
        }

        return $features;
    }

    /**
     * Extract text-based features
     */
    protected function extractTextFeatures(string $text): array
    {
        $features = [];

        // Basic text metrics
        $features['text_length'] = strlen($text);
        $features['word_count'] = str_word_count($text);
        $features['line_count'] = substr_count($text, "\n") + 1;
        $features['paragraph_count'] = substr_count($text, "\n\n") + 1;

        // Character analysis
        $features['uppercase_ratio'] = $this->calculateUppercaseRatio($text);
        $features['digit_ratio'] = $this->calculateDigitRatio($text);
        $features['special_char_ratio'] = $this->calculateSpecialCharRatio($text);

        // Language features
        $features['average_word_length'] = $this->calculateAverageWordLength($text);
        $features['sentence_count'] = $this->countSentences($text);

        // Vocabulary richness
        $features['unique_word_ratio'] = $this->calculateUniqueWordRatio($text);

        return $features;
    }

    /**
     * Extract document structure features
     */
    protected function extractStructureFeatures(string $text): array
    {
        $features = [];

        // Header detection (lines that might be headers)
        $features['potential_headers'] = $this->countPotentialHeaders($text);
        
        // List detection
        $features['bullet_points'] = substr_count($text, '•') + substr_count($text, '*');
        $features['numbered_lists'] = $this->countNumberedLists($text);
        
        // Table-like structures
        $features['table_indicators'] = $this->countTableIndicators($text);
        
        // Form fields
        $features['form_fields'] = $this->countFormFields($text);
        
        // Signature lines
        $features['signature_lines'] = $this->countSignatureLines($text);

        return $features;
    }

    /**
     * Extract pattern-based features
     */
    protected function extractPatternFeatures(string $text): array
    {
        $features = [];

        // Date patterns
        $features['date_patterns'] = $this->countDatePatterns($text);
        
        // ID number patterns
        $features['rfc_patterns'] = preg_match_all('/\b[A-Z]{3,4}\d{6}[A-Z0-9]{3}\b/', $text);
        $features['curp_patterns'] = preg_match_all('/\b[A-Z]{6}\d{8}[HM][A-Z]{5}\d{2}\b/', $text);
        $features['phone_patterns'] = preg_match_all('/\b\d{10}\b|\b\d{3}[-.\s]\d{3}[-.\s]\d{4}\b/', $text);
        
        // Address patterns
        $features['address_indicators'] = $this->countAddressIndicators($text);
        
        // Amount/currency patterns
        $features['currency_patterns'] = preg_match_all('/\$[\d,]+\.?\d*/', $text);
        
        // Email patterns
        $features['email_patterns'] = preg_match_all('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $text);
        
        // URL patterns
        $features['url_patterns'] = preg_match_all('/https?:\/\/[^\s]+/', $text);

        return $features;
    }

    /**
     * Calculate uppercase ratio
     */
    protected function calculateUppercaseRatio(string $text): float
    {
        $totalChars = strlen(preg_replace('/[^A-Za-z]/', '', $text));
        if ($totalChars === 0) return 0;
        
        $uppercaseChars = strlen(preg_replace('/[^A-Z]/', '', $text));
        return $uppercaseChars / $totalChars;
    }

    /**
     * Calculate digit ratio
     */
    protected function calculateDigitRatio(string $text): float
    {
        $totalChars = strlen($text);
        if ($totalChars === 0) return 0;
        
        $digitChars = strlen(preg_replace('/[^0-9]/', '', $text));
        return $digitChars / $totalChars;
    }

    /**
     * Calculate special character ratio
     */
    protected function calculateSpecialCharRatio(string $text): float
    {
        $totalChars = strlen($text);
        if ($totalChars === 0) return 0;
        
        $specialChars = strlen(preg_replace('/[A-Za-z0-9\s]/', '', $text));
        return $specialChars / $totalChars;
    }

    /**
     * Calculate average word length
     */
    protected function calculateAverageWordLength(string $text): float
    {
        $words = str_word_count($text, 1);
        if (empty($words)) return 0;
        
        $totalLength = array_sum(array_map('strlen', $words));
        return $totalLength / count($words);
    }

    /**
     * Count sentences
     */
    protected function countSentences(string $text): int
    {
        return preg_match_all('/[.!?]+/', $text);
    }

    /**
     * Calculate unique word ratio
     */
    protected function calculateUniqueWordRatio(string $text): float
    {
        $words = str_word_count(strtolower($text), 1);
        if (empty($words)) return 0;
        
        $uniqueWords = array_unique($words);
        return count($uniqueWords) / count($words);
    }

    /**
     * Count potential headers
     */
    protected function countPotentialHeaders(string $text): int
    {
        $lines = explode("\n", $text);
        $headerCount = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) > 0 && strlen($line) < 100 && 
                (ctype_upper($line) || preg_match('/^[A-Z][^.]*$/', $line))) {
                $headerCount++;
            }
        }
        
        return $headerCount;
    }

    /**
     * Count numbered lists
     */
    protected function countNumberedLists(string $text): int
    {
        return preg_match_all('/^\s*\d+[\.\)]\s/m', $text);
    }

    /**
     * Count table indicators
     */
    protected function countTableIndicators(string $text): int
    {
        $indicators = 0;
        $indicators += substr_count($text, '|'); // Pipe characters
        $indicators += substr_count($text, "\t"); // Tab characters
        $indicators += preg_match_all('/\s{3,}/', $text); // Multiple spaces
        
        return $indicators;
    }

    /**
     * Count form fields
     */
    protected function countFormFields(string $text): int
    {
        $patterns = [
            '/_{3,}/', // Underscores for filling
            '/\[\s*\]/', // Checkboxes
            '/\(\s*\)/', // Radio buttons
            '/:.*_{2,}/', // Colon followed by underscores
        ];
        
        $count = 0;
        foreach ($patterns as $pattern) {
            $count += preg_match_all($pattern, $text);
        }
        
        return $count;
    }

    /**
     * Count signature lines
     */
    protected function countSignatureLines(string $text): int
    {
        $patterns = [
            '/firma/i',
            '/signature/i',
            '/nombre y firma/i',
            '/autoriza/i'
        ];
        
        $count = 0;
        foreach ($patterns as $pattern) {
            $count += preg_match_all($pattern, $text);
        }
        
        return $count;
    }

    /**
     * Count date patterns
     */
    protected function countDatePatterns(string $text): int
    {
        $patterns = [
            '/\b\d{1,2}\/\d{1,2}\/\d{4}\b/', // MM/DD/YYYY or DD/MM/YYYY
            '/\b\d{1,2}-\d{1,2}-\d{4}\b/', // MM-DD-YYYY or DD-MM-YYYY
            '/\b\d{4}-\d{1,2}-\d{1,2}\b/', // YYYY-MM-DD
            '/\b\d{1,2}\s+de\s+\w+\s+de\s+\d{4}\b/i', // Spanish date format
        ];
        
        $count = 0;
        foreach ($patterns as $pattern) {
            $count += preg_match_all($pattern, $text);
        }
        
        return $count;
    }

    /**
     * Count address indicators
     */
    protected function countAddressIndicators(string $text): int
    {
        $indicators = [
            'calle', 'avenida', 'boulevard', 'colonia', 'delegación',
            'municipio', 'estado', 'c.p.', 'código postal', 'número'
        ];
        
        $count = 0;
        foreach ($indicators as $indicator) {
            $count += preg_match_all('/\b' . preg_quote($indicator, '/') . '\b/i', $text);
        }
        
        return $count;
    }

    /**
     * Extraer características de diseño del documento
     */
    public function extractLayoutFeatures(string $filePath): array
    {
        try {
            $fullPath = Storage::path($filePath);
            
            if (!file_exists($fullPath)) {
                throw new Exception('Archivo no encontrado: ' . $filePath);
            }

            $features = [
                'page_count' => $this->getPageCount($fullPath),
                'file_size' => filesize($fullPath),
                'creation_date' => $this->getCreationDate($fullPath),
                'has_images' => $this->hasImages($fullPath),
                'text_density' => $this->calculateTextDensity($fullPath),
            ];

            return $features;

        } catch (Exception $e) {
            Log::warning('Error extrayendo características de diseño', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'page_count' => 1,
                'file_size' => 0,
                'creation_date' => null,
                'has_images' => false,
                'text_density' => 0,
            ];
        }
    }

    /**
     * Extraer metadatos del documento
     */
    public function extractMetadata(string $filePath): array
    {
        try {
            $fullPath = Storage::path($filePath);
            
            $metadata = [
                'mime_type' => mime_content_type($fullPath),
                'extension' => pathinfo($fullPath, PATHINFO_EXTENSION),
                'filename' => pathinfo($fullPath, PATHINFO_FILENAME),
                'size_mb' => round(filesize($fullPath) / 1024 / 1024, 2),
            ];

            // Si es PDF, extraer metadatos específicos
            if (strtolower($metadata['extension']) === 'pdf') {
                $metadata = array_merge($metadata, $this->extractPdfMetadata($fullPath));
            }

            return $metadata;

        } catch (Exception $e) {
            Log::warning('Error extrayendo metadatos', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'mime_type' => 'unknown',
                'extension' => 'unknown',
                'filename' => 'unknown',
                'size_mb' => 0,
            ];
        }
    }

    /**
     * Obtener número de páginas del PDF
     */
    private function getPageCount(string $fullPath): int
    {
        try {
            if (strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) !== 'pdf') {
                return 1;
            }

            $content = file_get_contents($fullPath);
            if ($content === false) {
                return 1;
            }

            $count = preg_match_all('/\/Count\s+(\d+)/', $content, $matches);
            if ($count > 0) {
                return (int) max($matches[1]);
            }

            // Método alternativo
            $count = preg_match_all('/\/Page\W/', $content, $matches);
            return max(1, $count);

        } catch (Exception $e) {
            return 1;
        }
    }

    /**
     * Obtener fecha de creación del archivo
     */
    private function getCreationDate(string $fullPath): ?string
    {
        try {
            $timestamp = filectime($fullPath);
            return date('Y-m-d H:i:s', $timestamp);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verificar si el documento tiene imágenes
     */
    private function hasImages(string $fullPath): bool
    {
        try {
            if (strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) !== 'pdf') {
                return false;
            }

            $content = file_get_contents($fullPath);
            if ($content === false) {
                return false;
            }

            // Buscar objetos de imagen en PDF
            return preg_match('/\/Subtype\s*\/Image/', $content) === 1;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Calcular densidad de texto
     */
    private function calculateTextDensity(string $fullPath): float
    {
        try {
            $textExtractor = new PdfTextExtractor();
            $text = $textExtractor->extractText(str_replace(Storage::path(''), '', $fullPath));
            $textLength = strlen($text);
            $fileSize = filesize($fullPath);
            
            if ($fileSize === 0) {
                return 0;
            }

            return round($textLength / $fileSize, 4);

        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Extraer metadatos específicos de PDF
     */
    private function extractPdfMetadata(string $fullPath): array
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            $details = $pdf->getDetails();

            return [
                'pdf_version' => $details['PDFVersion'] ?? 'unknown',
                'creator' => $details['Creator'] ?? 'unknown',
                'producer' => $details['Producer'] ?? 'unknown',
                'creation_date' => $details['CreationDate'] ?? null,
                'modification_date' => $details['ModDate'] ?? null,
                'pages' => count($pdf->getPages()),
            ];

        } catch (Exception $e) {
            Log::debug('Error extrayendo metadatos PDF', [
                'file' => $fullPath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'pdf_version' => 'unknown',
                'creator' => 'unknown',
                'producer' => 'unknown',
                'creation_date' => null,
                'modification_date' => null,
                'pages' => 1,
            ];
        }
    }
} 