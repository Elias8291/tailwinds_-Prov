<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AiDocumentModel;
use App\Models\AI\AiTrainingData;
use App\Models\AI\AiValidationResult;
use App\Models\Documento;
use App\Services\AI\DocumentAnalysisService;
use App\Services\AI\ModelTrainingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentAIController extends Controller
{
    private DocumentAnalysisService $analysisService;
    private ModelTrainingService $trainingService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->analysisService = new DocumentAnalysisService();
        $this->trainingService = new ModelTrainingService();
    }

    /**
     * Panel principal del módulo de IA
     */
    public function dashboard()
    {
        $stats = [
            'active_model' => AiDocumentModel::getActiveModel(),
            'total_models' => AiDocumentModel::count(),
            'training_data' => $this->trainingService->getTrainingStats(),
            'validation_results' => AiValidationResult::getValidationStats(),
        ];

        return view('ai.dashboard', compact('stats'));
    }

    /**
     * Lista de modelos de IA
     */
    public function models()
    {
        $models = AiDocumentModel::with('validationResults')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ai.models.index', compact('models'));
    }

    /**
     * Mostrar detalles de un modelo
     */
    public function showModel(AiDocumentModel $model)
    {
        $model->load('validationResults.documentoSolicitante');
        $stats = $model->getStats();

        return view('ai.models.show', compact('model', 'stats'));
    }

    /**
     * Activar un modelo específico
     */
    public function activateModel(AiDocumentModel $model)
    {
        try {
            if ($model->status !== 'ready') {
                return back()->with('error', 'Solo se pueden activar modelos listos.');
            }

            $model->activate();

            return back()->with('success', 'Modelo activado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error activando modelo', [
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al activar el modelo.');
        }
    }

    /**
     * Datos de entrenamiento
     */
    public function trainingData()
    {
        $trainingData = AiTrainingData::with('documento')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $documentTypes = Documento::distinct('tipo_persona')->pluck('tipo_persona');
        $stats = $this->trainingService->getTrainingStats();

        return view('ai.training.index', compact('trainingData', 'documentTypes', 'stats'));
    }

    /**
     * Subir datos de entrenamiento
     */
    public function uploadTrainingData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'documento_id' => 'required|exists:documento,id',
            'document_type' => 'required|string|max:255',
        ]);

        try {
            $trainingData = $this->trainingService->addTrainingData(
                $request->file('file'),
                $request->documento_id,
                $request->document_type
            );

            if ($trainingData) {
                return back()->with('success', 'Datos de entrenamiento agregados exitosamente.');
            } else {
                return back()->with('error', 'Error al agregar datos de entrenamiento.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Validar datos de entrenamiento
     */
    public function validateTrainingData(Request $request, AiTrainingData $trainingData)
    {
        $request->validate([
            'is_correct' => 'required|boolean',
            'correct_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $validationData = [
                'validated_by' => Auth::id(),
                'is_correct' => $request->is_correct,
                'correct_type' => $request->correct_type,
                'notes' => $request->notes,
                'validated_at' => now(),
            ];

            if ($this->trainingService->validateTrainingData($trainingData->id, $validationData)) {
                return back()->with('success', 'Datos validados exitosamente.');
            } else {
                return back()->with('error', 'Error al validar datos.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Entrenar nuevo modelo
     */
    public function trainModel(Request $request)
    {
        $request->validate([
            'model_name' => 'required|string|max:255|unique:ai_document_models,name',
            'document_types' => 'required|array|min:1',
            'document_types.*' => 'string|max:255',
            'algorithm' => 'nullable|string|max:100',
        ]);

        try {
            $trainingParameters = [];
            if ($request->algorithm) {
                $trainingParameters['algorithm'] = $request->algorithm;
            }

            $model = $this->trainingService->trainModel(
                $request->model_name,
                $request->document_types,
                $trainingParameters
            );

            if ($model) {
                return redirect()->route('ai.models.show', $model)
                    ->with('success', 'Modelo entrenado exitosamente.');
            } else {
                return back()->with('error', 'Error al entrenar el modelo.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Resultados de validación con IA
     */
    public function validationResults()
    {
        $results = AiValidationResult::with(['documentoSolicitante.documento', 'aiModel', 'humanValidator'])
            ->orderBy('processed_at', 'desc')
            ->paginate(20);

        $pendingCount = AiValidationResult::where('validation_status', 'needs_review')->count();

        return view('ai.validation.index', compact('results', 'pendingCount'));
    }

    /**
     * Revisar resultado de validación
     */
    public function reviewValidation(Request $request, AiValidationResult $result)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $user = Auth::user();
            $notes = $request->notes ?? '';

            if ($request->action === 'approve') {
                $result->approve($user, $notes);
                $message = 'Validación aprobada exitosamente.';
            } else {
                $result->reject($user, $notes);
                $message = 'Validación rechazada exitosamente.';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Procesar documentos existentes para entrenamiento
     */
    public function processExistingDocuments(Request $request)
    {
        $request->validate([
            'documento_solicitante_ids' => 'required|array|min:1',
            'documento_solicitante_ids.*' => 'integer|exists:documento_solicitante,id',
            'document_type' => 'required|string|max:255',
        ]);

        try {
            $processed = $this->trainingService->processExistingDocuments(
                $request->documento_solicitante_ids,
                $request->document_type
            );

            return back()->with('success', "Se procesaron $processed documentos para entrenamiento.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * API: Analizar documento específico
     */
    public function analyzeDocument(Request $request)
    {
        $request->validate([
            'documento_solicitante_id' => 'required|integer|exists:documento_solicitante,id',
        ]);

        try {
            $documentoSolicitante = \App\Models\DocumentoSolicitante::findOrFail(
                $request->documento_solicitante_id
            );

            $result = $this->analysisService->analyzeDocument($documentoSolicitante);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'result' => [
                        'predicted_type' => $result->predicted_document_type,
                        'confidence' => $result->confidence_score,
                        'status' => $result->validation_status,
                        'feedback' => $result->ai_feedback,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al analizar el documento'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener estadísticas del sistema
     */
    public function getStats()
    {
        try {
            $stats = [
                'analysis' => $this->analysisService->getAnalysisStats(),
                'training' => $this->trainingService->getTrainingStats(),
                'validation' => AiValidationResult::getValidationStats(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
} 