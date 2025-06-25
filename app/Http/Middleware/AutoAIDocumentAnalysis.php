<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\AI\DocumentAnalysisService;
use App\Models\DocumentoSolicitante;

class AutoAIDocumentAnalysis
{
    protected DocumentAnalysisService $analysisService;

    public function __construct(DocumentAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Handle an incoming request for automatic AI document analysis
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo procesar en rutas de subida de documentos con método POST
        if ($request->isMethod('post') && 
            $request->is('tramites-solicitante/upload-documento')) {
            
            // Verificar si la respuesta fue exitosa
            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getContent(), true);
                
                if (isset($responseData['success']) && $responseData['success'] && 
                    isset($responseData['docSolicitanteId'])) {
                    
                    try {
                        // Buscar el documento recién subido
                        $documentoSolicitante = DocumentoSolicitante::find($responseData['docSolicitanteId']);
                        
                        if ($documentoSolicitante) {
                            Log::info('🤖 Iniciando análisis automático IA', [
                                'documento_solicitante_id' => $documentoSolicitante->id,
                                'tipo_documento' => $documentoSolicitante->documento->nombre ?? 'Desconocido'
                            ]);
                            
                            // Realizar análisis en segundo plano (en este caso síncrono por simplicidad)
                            $this->analysisService->analyzeDocument($documentoSolicitante);
                            
                            Log::info('✅ Análisis IA completado automáticamente');
                        }
                    } catch (\Exception $e) {
                        // Log del error pero no afectar la respuesta principal
                        Log::warning('❌ Error en análisis automático IA', [
                            'error' => $e->getMessage(),
                            'documento_solicitante_id' => $responseData['docSolicitanteId'] ?? 'N/A'
                        ]);
                    }
                }
            }
        }

        return $response;
    }
} 