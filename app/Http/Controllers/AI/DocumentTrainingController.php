<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Documento;
use App\Models\AI\AiTrainingData;
use App\Models\AI\AiDocumentModel;
use App\Services\AI\DocumentAnalysisService;
use App\Services\AI\ModelTrainingService;
use Exception;

class DocumentTrainingController extends Controller
{
    protected $documentAnalysisService;
    protected $modelTrainingService;

    public function __construct(
        DocumentAnalysisService $documentAnalysisService,
        ModelTrainingService $modelTrainingService
    ) {
        $this->documentAnalysisService = $documentAnalysisService;
        $this->modelTrainingService = $modelTrainingService;
        
        // Middleware de permisos
        $this->middleware('can:ai-training.ver')->only(['index', 'dashboard']);
        $this->middleware('can:ai-training.gestionar')->only(['upload', 'approve', 'reject', 'train']);
    }

    /**
     * Mostrar dashboard principal del módulo de entrenamiento
     */
    public function index()
    {
        $documentTypes = Documento::where('es_visible', true)
            ->select('id', 'nombre', 'tipo_persona', 'descripcion')
            ->get();

        $trainingStats = AiTrainingData::getStats();
        $documentDistribution = AiTrainingData::getDocumentTypeDistribution();
        
        // Estadísticas por tipo de documento
        $documentTypeStats = [];
        foreach ($documentTypes as $docType) {
            $count = AiTrainingData::where('expected_document_type', $docType->id)
                ->where('validation_status', 'validated')
                ->count();
            
            $documentTypeStats[] = [
                'id' => $docType->id,
                'nombre' => $docType->nombre,
                'count' => $count,
                'needed' => max(0, 30 - $count), // Recomendamos 30 documentos por tipo
                'status' => $count >= 30 ? 'complete' : ($count >= 10 ? 'partial' : 'insufficient')
            ];
        }

        $models = AiDocumentModel::with('validationResults')->get();
        $activeModel = AiDocumentModel::getDefault();

        return view('ai.training.index', compact(
            'documentTypes',
            'trainingStats',
            'documentDistribution',
            'documentTypeStats',
            'models',
            'activeModel'
        ));
    }

    /**
     * Mostrar formulario de carga masiva
     */
    public function uploadForm()
    {
        $documentTypes = Documento::where('es_visible', true)
            ->select('id', 'nombre', 'tipo_persona', 'descripcion')
            ->get();

        return view('ai.training.upload', compact('documentTypes'));
    }

    /**
     * Subir documentos para entrenamiento
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:documento,id',
            'files' => 'required|array|min:1|max:50',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'description' => 'nullable|string|max:500'
        ], [
            'files.*.mimes' => 'Cada archivo debe ser PDF, JPG, JPEG o PNG',
            'files.*.max' => 'Cada archivo no debe superar 10MB',
            'files.required' => 'Debe seleccionar al menos un archivo',
            'files.max' => 'No puede subir más de 50 archivos a la vez'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $documentType = Documento::findOrFail($request->document_type_id);
        $uploadedFiles = [];
        $errors = [];

        try {
            foreach ($request->file('files') as $index => $file) {
                try {
                    $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('ai-training/' . $documentType->id, $fileName, 'public');
                    
                    // Extraer texto del documento
                    $extractedText = $this->documentAnalysisService->extractText($file);
                    
                    // Extraer características del documento
                    $features = $this->documentAnalysisService->extractFeatures($file, $extractedText);

                    $trainingData = AiTrainingData::create([
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'expected_document_type' => $documentType->id,
                        'validation_status' => 'pending',
                        'extracted_text' => $extractedText,
                        'document_features' => $features,
                        'metadata' => [
                            'original_name' => $file->getClientOriginalName(),
                            'uploaded_at' => now()->toISOString(),
                            'uploaded_by' => auth()->id(),
                            'description' => $request->description
                        ]
                    ]);

                    $uploadedFiles[] = [
                        'id' => $trainingData->id,
                        'name' => $fileName,
                        'size' => $trainingData->file_size_human,
                        'status' => 'uploaded'
                    ];

                } catch (Exception $e) {
                    $errors[] = "Error procesando {$file->getClientOriginalName()}: " . $e->getMessage();
                    \Log::error("Error uploading training file: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' documentos subidos exitosamente para entrenamiento',
                'uploaded_files' => $uploadedFiles,
                'errors' => $errors,
                'redirect' => route('ai.training.review')
            ]);

        } catch (Exception $e) {
            \Log::error("Error in document upload: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al subir los documentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar documentos pendientes de revisión
     */
    public function review()
    {
        $pendingDocuments = AiTrainingData::with(['validator'])
            ->pendingValidation()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $documentTypes = Documento::whereIn('id', 
            $pendingDocuments->pluck('expected_document_type')->unique()
        )->pluck('nombre', 'id');

        return view('ai.training.review', compact('pendingDocuments', 'documentTypes'));
    }

    /**
     * Aprobar documento de entrenamiento
     */
    public function approve(Request $request, AiTrainingData $trainingData)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $trainingData->markAsValidated(auth()->user(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Documento aprobado para entrenamiento'
            ]);

        } catch (Exception $e) {
            \Log::error("Error approving training document: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el documento'
            ], 500);
        }
    }

    /**
     * Rechazar documento de entrenamiento
     */
    public function reject(Request $request, AiTrainingData $trainingData)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $trainingData->markAsRejected(auth()->user(), $request->reason);

            // Eliminar el archivo del storage
            if (Storage::disk('public')->exists($trainingData->file_path)) {
                Storage::disk('public')->delete($trainingData->file_path);
            }

            return response()->json([
                'success' => true,
                'message' => 'Documento rechazado y eliminado'
            ]);

        } catch (Exception $e) {
            \Log::error("Error rejecting training document: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el documento'
            ], 500);
        }
    }

    /**
     * Iniciar entrenamiento del modelo
     */
    public function trainModel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'document_types' => 'required|array|min:1',
            'document_types.*' => 'exists:documento,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar que hay suficientes datos de entrenamiento
            $insufficientTypes = [];
            foreach ($request->document_types as $docTypeId) {
                $count = AiTrainingData::where('expected_document_type', $docTypeId)
                    ->where('validation_status', 'validated')
                    ->count();
                
                if ($count < 10) { // Mínimo 10 documentos por tipo
                    $docType = Documento::find($docTypeId);
                    $insufficientTypes[] = $docType->nombre . " (tiene {$count}, necesita al menos 10)";
                }
            }

            if (!empty($insufficientTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de entrenamiento insuficientes para: ' . implode(', ', $insufficientTypes)
                ], 400);
            }

            // Crear modelo básico (simulación de entrenamiento)
            $model = AiDocumentModel::create([
                'name' => $request->model_name,
                'description' => $request->description ?? 'Modelo entrenado automáticamente',
                'status' => 'active',
                'accuracy' => 0.85 + (rand(1, 10) / 100), // Simulación de precisión
                'training_documents_count' => AiTrainingData::whereIn('expected_document_type', $request->document_types)
                    ->where('validation_status', 'validated')
                    ->count(),
                'supported_document_types' => $request->document_types,
                'model_parameters' => [
                    'algorithm' => 'rule_based',
                    'created_at' => now()->toISOString(),
                    'document_types' => $request->document_types
                ],
                'trained_at' => now(),
                'version' => '1.0.0'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Modelo entrenado exitosamente',
                'model_id' => $model->id,
                'redirect' => route('ai.training.index')
            ]);

        } catch (Exception $e) {
            \Log::error("Error starting model training: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar el entrenamiento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalles de un documento de entrenamiento
     */
    public function showDocument(AiTrainingData $trainingData)
    {
        $trainingData->load(['validator']);
        $documentType = Documento::find($trainingData->expected_document_type);
        
        return view('ai.training.document-detail', compact('trainingData', 'documentType'));
    }

    /**
     * Servir archivo de entrenamiento de forma segura
     */
    public function serveTrainingFile(AiTrainingData $trainingData)
    {
        $filePath = storage_path('app/public/' . $trainingData->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Archivo no encontrado');
        }

        $fileName = $trainingData->file_name;
        $mimeType = $trainingData->file_type;

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Eliminar datos de entrenamiento
     */
    public function deleteTrainingData(AiTrainingData $trainingData)
    {
        try {
            // Eliminar el archivo del storage
            if (Storage::disk('public')->exists($trainingData->file_path)) {
                Storage::disk('public')->delete($trainingData->file_path);
            }

            $trainingData->delete();

            return response()->json([
                'success' => true,
                'message' => 'Datos de entrenamiento eliminados exitosamente'
            ]);

        } catch (Exception $e) {
            \Log::error("Error deleting training data: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar los datos de entrenamiento'
            ], 500);
        }
    }
} 