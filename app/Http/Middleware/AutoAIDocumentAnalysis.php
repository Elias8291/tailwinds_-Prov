<?php

namespace App\Http\Middleware;

use App\Models\DocumentoSolicitante;
use App\Services\AI\DocumentAnalysisService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AutoAIDocumentAnalysis
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo ejecutar para requests exitosos que involucren subida de documentos
        if ($response->getStatusCode() === 200 && $this->shouldAnalyzeDocument($request)) {
            $this->analyzeRecentDocuments();
        }

        return $response;
    }

    /**
     * Verificar si se debe analizar el documento
     */
    private function shouldAnalyzeDocument(Request $request): bool
    {
        // Verificar si es una subida de documento
        $isDocumentUpload = $request->hasFile('archivo') || 
                           $request->hasFile('documento') || 
                           str_contains($request->path(), 'documento') ||
                           str_contains($request->path(), 'upload');

        // Verificar si el análisis automático está habilitado
        $autoAnalysisEnabled = config('app.ai_auto_analysis', true);

        return $isDocumentUpload && $autoAnalysisEnabled;
    }

    /**
     * Analizar documentos recientes que no han sido analizados
     */
    private function analyzeRecentDocuments(): void
    {
        try {
            // Obtener documentos subidos en los últimos 5 minutos que no han sido analizados
            $recentDocuments = DocumentoSolicitante::whereNotNull('ruta_archivo')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->whereDoesntHave('aiValidationResults')
                ->limit(5)
                ->get();

            if ($recentDocuments->isEmpty()) {
                return;
            }

            $analysisService = new DocumentAnalysisService();

            foreach ($recentDocuments as $documento) {
                try {
                    $result = $analysisService->analyzeDocument($documento);
                    
                    if ($result) {
                        Log::info('Documento analizado automáticamente', [
                            'documento_id' => $documento->id,
                            'predicted_type' => $result->predicted_document_type,
                            'confidence' => $result->confidence_score
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    Log::warning('Error en análisis automático de documento', [
                        'documento_id' => $documento->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error en middleware de análisis automático', [
                'error' => $e->getMessage()
            ]);
        }
    }
} 