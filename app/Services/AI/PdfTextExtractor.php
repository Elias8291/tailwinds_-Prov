<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class PdfTextExtractor
{
    protected Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extract text from a PDF file
     */
    public function extractText(string $filePath): string
    {
        try {
            // Check if file exists
            if (!file_exists($filePath)) {
                throw new \Exception("El archivo no existe: {$filePath}");
            }

            // Check if it's a PDF file
            $mimeType = mime_content_type($filePath);
            if ($mimeType !== 'application/pdf') {
                throw new \Exception("El archivo no es un PDF válido");
            }

            // Parse the PDF
            $pdf = $this->parser->parseFile($filePath);
            
            // Get details about the PDF
            $details = $pdf->getDetails();
            
            // Extract text from all pages
            $text = $pdf->getText();
            
            // Clean and normalize the text
            $cleanText = $this->cleanText($text);
            
            Log::info('Texto extraído exitosamente del PDF', [
                'file_path' => $filePath,
                'text_length' => strlen($cleanText),
                'pages' => $details['Pages'] ?? 'unknown'
            ]);

            return $cleanText;

        } catch (\Exception $e) {
            Log::error('Error al extraer texto del PDF', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            // Return empty string if extraction fails
            return '';
        }
    }

    /**
     * Clean and normalize extracted text
     */
    protected function cleanText(string $text): string
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove control characters except newlines and tabs
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Remove multiple consecutive newlines
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Trim whitespace
        $text = trim($text);
        
        return $text;
    }

    /**
     * Extract text from specific pages
     */
    public function extractTextFromPages(string $filePath, array $pageNumbers): string
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
            $pages = $pdf->getPages();
            $extractedText = '';

            foreach ($pageNumbers as $pageNumber) {
                if (isset($pages[$pageNumber - 1])) { // Pages are 0-indexed
                    $extractedText .= $pages[$pageNumber - 1]->getText() . "\n";
                }
            }

            return $this->cleanText($extractedText);

        } catch (\Exception $e) {
            Log::error('Error al extraer texto de páginas específicas', [
                'file_path' => $filePath,
                'pages' => $pageNumbers,
                'error' => $e->getMessage()
            ]);
            
            return '';
        }
    }

    /**
     * Get PDF metadata
     */
    public function getPdfMetadata(string $filePath): array
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
            $details = $pdf->getDetails();
            $pages = $pdf->getPages();

            return [
                'title' => $details['Title'] ?? null,
                'author' => $details['Author'] ?? null,
                'subject' => $details['Subject'] ?? null,
                'creator' => $details['Creator'] ?? null,
                'producer' => $details['Producer'] ?? null,
                'creation_date' => $details['CreationDate'] ?? null,
                'modification_date' => $details['ModDate'] ?? null,
                'page_count' => count($pages),
                'file_size' => filesize($filePath),
                'text_length' => strlen($pdf->getText())
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener metadatos del PDF', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'page_count' => 0,
                'file_size' => filesize($filePath),
                'text_length' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if PDF is readable/parseable
     */
    public function isPdfReadable(string $filePath): bool
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
            $text = $pdf->getText();
            
            // Consider PDF readable if we can extract any text
            return !empty(trim($text));
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get word count from PDF
     */
    public function getWordCount(string $filePath): int
    {
        $text = $this->extractText($filePath);
        $words = str_word_count($text);
        return $words;
    }

    /**
     * Search for specific text in PDF
     */
    public function searchText(string $filePath, string $searchTerm, bool $caseSensitive = false): array
    {
        $text = $this->extractText($filePath);
        
        if (!$caseSensitive) {
            $text = strtolower($text);
            $searchTerm = strtolower($searchTerm);
        }

        $matches = [];
        $offset = 0;
        
        while (($position = strpos($text, $searchTerm, $offset)) !== false) {
            // Get context around the match
            $contextStart = max(0, $position - 50);
            $contextEnd = min(strlen($text), $position + strlen($searchTerm) + 50);
            $context = substr($text, $contextStart, $contextEnd - $contextStart);
            
            $matches[] = [
                'position' => $position,
                'context' => trim($context),
                'line_number' => substr_count($text, "\n", 0, $position) + 1
            ];
            
            $offset = $position + 1;
        }

        return $matches;
    }

    /**
     * Extraer texto de múltiples archivos
     */
    public function extractTextFromMultipleFiles(array $filePaths): array
    {
        $results = [];
        
        foreach ($filePaths as $filePath) {
            $results[$filePath] = $this->extractText($filePath);
        }
        
        return $results;
    }

    /**
     * Obtener metadatos del PDF junto con el texto
     */
    public function extractTextWithMetadata(string $filePath): array
    {
        try {
            $fullPath = Storage::path($filePath);
            
            if (!file_exists($fullPath)) {
                throw new Exception('Archivo no encontrado: ' . $filePath);
            }

            $pdf = $this->parser->parseFile($fullPath);
            
            return [
                'text' => $this->cleanText($pdf->getText()),
                'page_count' => count($pdf->getPages()),
                'details' => $pdf->getDetails(),
                'metadata' => [
                    'file_size' => filesize($fullPath),
                    'creation_date' => filectime($fullPath),
                    'modification_date' => filemtime($fullPath),
                ]
            ];

        } catch (Exception $e) {
            Log::error('Error extrayendo texto con metadatos', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'text' => '',
                'page_count' => 0,
                'details' => [],
                'metadata' => []
            ];
        }
    }

    /**
     * Verificar si un archivo PDF contiene texto extraíble
     */
    public function hasExtractableText(string $filePath): bool
    {
        try {
            $text = $this->extractText($filePath);
            return !empty(trim($text)) && strlen(trim($text)) > 10;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Método alternativo para extraer texto si falla el principal
     */
    private function extractTextFallback(string $filePath): string
    {
        try {
            // Método básico usando herramientas del sistema (si están disponibles)
            $fullPath = Storage::path($filePath);
            
            // Verificar si pdftotext está disponible
            if ($this->isPdftotextAvailable()) {
                $command = "pdftotext \"$fullPath\" -";
                $output = shell_exec($command);
                if ($output !== null) {
                    return $this->cleanText($output);
                }
            }
            
            Log::debug('Métodos alternativos de extracción no disponibles');
            return '';
            
        } catch (Exception $e) {
            Log::debug('Error en método alternativo de extracción', [
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Verificar si pdftotext está disponible en el sistema
     */
    private function isPdftotextAvailable(): bool
    {
        $result = shell_exec('which pdftotext 2>/dev/null');
        return !empty($result);
    }

    /**
     * Obtener estadísticas del texto extraído
     */
    public function getTextStats(string $text): array
    {
        $text = $this->cleanText($text);
        
        return [
            'character_count' => strlen($text),
            'word_count' => str_word_count($text),
            'line_count' => substr_count($text, "\n") + 1,
            'paragraph_count' => substr_count($text, "\n\n") + 1,
            'has_content' => !empty(trim($text)),
            'language_detected' => $this->detectLanguage($text),
        ];
    }

    /**
     * Detectar idioma del texto (básico)
     */
    private function detectLanguage(string $text): string
    {
        $text = strtolower($text);
        
        // Palabras comunes en español
        $spanishWords = ['el', 'la', 'de', 'que', 'y', 'a', 'en', 'un', 'es', 'se', 'no', 'te', 'lo', 'le', 'da', 'su', 'por', 'son', 'con', 'para'];
        
        // Palabras comunes en inglés
        $englishWords = ['the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'i', 'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at'];
        
        $spanishCount = 0;
        $englishCount = 0;
        
        foreach ($spanishWords as $word) {
            $spanishCount += substr_count($text, ' ' . $word . ' ');
        }
        
        foreach ($englishWords as $word) {
            $englishCount += substr_count($text, ' ' . $word . ' ');
        }
        
        if ($spanishCount > $englishCount) {
            return 'es';
        } elseif ($englishCount > $spanishCount) {
            return 'en';
        } else {
            return 'unknown';
        }
    }
} 