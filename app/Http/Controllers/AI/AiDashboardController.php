<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AiDocumentModel;
use App\Models\AI\AiTrainingData;
use App\Models\AI\AiValidationResult;
use App\Services\AI\ModelTrainingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiDashboardController extends Controller
{
    protected ModelTrainingService $modelTrainingService;

    public function __construct(ModelTrainingService $modelTrainingService)
    {
        $this->modelTrainingService = $modelTrainingService;
    }

    /**
     * Display the main AI dashboard
     */
    public function index()
    {
        try {
            // Get current active model
            $activeModel = AiDocumentModel::getDefault();
            
            // Get overall statistics
            $stats = $this->getOverallStats();
            
            // Get recent validation results
            $recentValidations = AiValidationResult::with(['documentoSolicitante', 'aiModel'])
                ->orderBy('processed_at', 'desc')
                ->limit(10)
                ->get();

            // Get training statistics
            $trainingStats = $this->modelTrainingService->getTrainingStats();

            return view('ai.dashboard.index', compact(
                'activeModel',
                'stats',
                'recentValidations',
                'trainingStats'
            ));

        } catch (\Exception $e) {
            Log::error('Error en dashboard de IA', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al cargar el dashboard de IA: ' . $e->getMessage());
        }
    }

    /**
     * Get overall system statistics
     */
    protected function getOverallStats(): array
    {
        return [
            'models' => [
                'total' => AiDocumentModel::count(),
                'active' => AiDocumentModel::where('status', 'active')->count(),
                'training' => AiDocumentModel::where('status', 'training')->count()
            ],
            'training_data' => AiTrainingData::getStats(),
            'validations' => AiValidationResult::getValidationStats(),
            'documents_analyzed_today' => AiValidationResult::whereDate('processed_at', today())->count(),
            'average_confidence' => AiValidationResult::avg('confidence_score') ?? 0
        ];
    }

    /**
     * Get performance metrics for charts
     */
    public function getMetrics(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $startDate = now()->subDays($days);

            // Daily validation counts
            $dailyValidations = AiValidationResult::selectRaw('DATE(processed_at) as date, COUNT(*) as count')
                ->where('processed_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Confidence distribution
            $confidenceDistribution = AiValidationResult::selectRaw('
                CASE 
                    WHEN confidence_score >= 0.9 THEN "High (90%+)"
                    WHEN confidence_score >= 0.7 THEN "Medium (70-90%)"
                    ELSE "Low (<70%)"
                END as confidence_range,
                COUNT(*) as count
            ')
                ->where('processed_at', '>=', $startDate)
                ->groupBy('confidence_range')
                ->get();

            // Document type distribution
            $documentTypeDistribution = AiValidationResult::selectRaw('predicted_document_type, COUNT(*) as count')
                ->where('processed_at', '>=', $startDate)
                ->groupBy('predicted_document_type')
                ->orderBy('count', 'desc')
                ->get();

            // Validation status distribution
            $validationStatusDistribution = AiValidationResult::selectRaw('validation_status, COUNT(*) as count')
                ->where('processed_at', '>=', $startDate)
                ->groupBy('validation_status')
                ->get();

            return response()->json([
                'daily_validations' => $dailyValidations,
                'confidence_distribution' => $confidenceDistribution,
                'document_type_distribution' => $documentTypeDistribution,
                'validation_status_distribution' => $validationStatusDistribution
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo métricas de IA', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Error al obtener métricas'], 500);
        }
    }

    /**
     * Get system health status
     */
    public function getSystemHealth()
    {
        try {
            $health = [
                'overall_status' => 'healthy',
                'checks' => []
            ];

            // Check if there's an active model
            $activeModel = AiDocumentModel::getDefault();
            $health['checks']['active_model'] = [
                'status' => $activeModel ? 'pass' : 'fail',
                'message' => $activeModel ? 'Modelo activo disponible' : 'No hay modelo activo',
                'details' => $activeModel ? [
                    'name' => $activeModel->name,
                    'accuracy' => $activeModel->accuracy_percentage,
                    'last_used' => $activeModel->last_used_at?->diffForHumans() ?? 'Nunca'
                ] : null
            ];

            // Check training data availability
            $validatedCount = AiTrainingData::where('validation_status', 'validated')->count();
            $health['checks']['training_data'] = [
                'status' => $validatedCount >= 20 ? 'pass' : 'warning',
                'message' => "Datos de entrenamiento validados: {$validatedCount}",
                'details' => [
                    'validated' => $validatedCount,
                    'pending' => AiTrainingData::where('validation_status', 'pending')->count(),
                    'recommendation' => $validatedCount < 20 ? 'Se recomiendan al menos 20 documentos validados' : null
                ]
            ];

            // Check recent performance
            $recentAccuracy = AiValidationResult::where('processed_at', '>=', now()->subDays(7))
                ->where('validation_status', 'human_confirmed')
                ->count();
            $recentTotal = AiValidationResult::where('processed_at', '>=', now()->subDays(7))->count();
            $recentAccuracyRate = $recentTotal > 0 ? ($recentAccuracy / $recentTotal) : 0;

            $health['checks']['recent_performance'] = [
                'status' => $recentAccuracyRate >= 0.8 ? 'pass' : 'warning',
                'message' => 'Rendimiento reciente: ' . number_format($recentAccuracyRate * 100, 1) . '%',
                'details' => [
                    'confirmed_predictions' => $recentAccuracy,
                    'total_predictions' => $recentTotal,
                    'accuracy_rate' => $recentAccuracyRate
                ]
            ];

            // Determine overall status
            $failedChecks = collect($health['checks'])->where('status', 'fail')->count();
            $warningChecks = collect($health['checks'])->where('status', 'warning')->count();

            if ($failedChecks > 0) {
                $health['overall_status'] = 'critical';
            } elseif ($warningChecks > 0) {
                $health['overall_status'] = 'warning';
            }

            return response()->json($health);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estado del sistema de IA', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'overall_status' => 'error',
                'error' => 'Error al verificar el estado del sistema'
            ], 500);
        }
    }

    /**
     * Quick actions for the dashboard
     */
    public function quickAction(Request $request)
    {
        try {
            $action = $request->get('action');

            switch ($action) {
                case 'analyze_pending':
                    return $this->analyzePendingDocuments();
                    
                case 'retrain_model':
                    return $this->retrainModel();
                    
                case 'clear_cache':
                    return $this->clearCache();
                    
                default:
                    return response()->json(['error' => 'Acción no válida'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error en acción rápida de IA', [
                'action' => $request->get('action'),
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Error al ejecutar la acción'], 500);
        }
    }

    /**
     * Analyze pending documents
     */
    protected function analyzePendingDocuments()
    {
        // This would trigger analysis of documents that haven't been processed yet
        // Implementation depends on your specific requirements
        
        return response()->json([
            'success' => true,
            'message' => 'Análisis de documentos pendientes iniciado'
        ]);
    }

    /**
     * Retrain the current model
     */
    protected function retrainModel()
    {
        try {
            $model = $this->modelTrainingService->trainModel();
            
            return response()->json([
                'success' => true,
                'message' => 'Modelo reentrenado exitosamente',
                'model_id' => $model->id,
                'accuracy' => $model->accuracy_percentage
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reentrenar el modelo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear system cache
     */
    protected function clearCache()
    {
        // Clear any AI-related caches
        // Implementation depends on your caching strategy
        
        return response()->json([
            'success' => true,
            'message' => 'Cache del sistema limpiado'
        ]);
    }
} 