<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AiDocumentModel;
use App\Services\AI\ModelTrainingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiModelController extends Controller
{
    protected ModelTrainingService $modelTrainingService;

    public function __construct(ModelTrainingService $modelTrainingService)
    {
        $this->modelTrainingService = $modelTrainingService;
    }

    /**
     * Display a listing of AI models
     */
    public function index()
    {
        $models = AiDocumentModel::orderBy('created_at', 'desc')->get();
        
        return view('ai.models.index', compact('models'));
    }

    /**
     * Show the form for creating a new model
     */
    public function create()
    {
        // Get available document types for training
        $documentTypes = [
            'Constancia de Situación Fiscal',
            'Acta de Nacimiento',
            'Credencial de Elector',
            'Comprobante de Domicilio',
            'CURP',
            'RFC'
        ];

        return view('ai.models.create', compact('documentTypes'));
    }

    /**
     * Store a newly created model
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ai_document_models,name',
            'description' => 'nullable|string|max:1000',
            'document_types' => 'required|array|min:1',
            'document_types.*' => 'string'
        ]);

        try {
            $model = $this->modelTrainingService->trainModel(
                $request->document_types,
                $request->name
            );

            if ($request->set_as_default) {
                $model->setAsDefault();
            }

            Log::info('Nuevo modelo de IA creado', [
                'model_id' => $model->id,
                'name' => $model->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('ai.models.show', $model)
                ->with('success', 'Modelo entrenado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error creando modelo de IA', [
                'name' => $request->name,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al entrenar el modelo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified model
     */
    public function show(AiDocumentModel $model)
    {
        $stats = $model->getPerformanceStats();
        
        return view('ai.models.show', compact('model', 'stats'));
    }

    /**
     * Show the form for editing the specified model
     */
    public function edit(AiDocumentModel $model)
    {
        return view('ai.models.edit', compact('model'));
    }

    /**
     * Update the specified model
     */
    public function update(Request $request, AiDocumentModel $model)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ai_document_models,name,' . $model->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,deprecated'
        ]);

        try {
            $model->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status
            ]);

            Log::info('Modelo de IA actualizado', [
                'model_id' => $model->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('ai.models.show', $model)
                ->with('success', 'Modelo actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error actualizando modelo de IA', [
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el modelo');
        }
    }

    /**
     * Set a model as default
     */
    public function setAsDefault(AiDocumentModel $model)
    {
        try {
            $model->setAsDefault();

            Log::info('Modelo establecido como predeterminado', [
                'model_id' => $model->id,
                'user_id' => auth()->id()
            ]);

            return back()->with('success', 'Modelo establecido como predeterminado');

        } catch (\Exception $e) {
            Log::error('Error estableciendo modelo como predeterminado', [
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al establecer el modelo como predeterminado');
        }
    }

    /**
     * Get model performance data
     */
    public function getPerformance(AiDocumentModel $model)
    {
        try {
            $stats = $model->getPerformanceStats();
            
            // Get recent validations for trend analysis
            $recentValidations = $model->validationResults()
                ->selectRaw('DATE(processed_at) as date, AVG(confidence_score) as avg_confidence, COUNT(*) as count')
                ->where('processed_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'stats' => $stats,
                'recent_validations' => $recentValidations
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo rendimiento del modelo', [
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Error al obtener datos de rendimiento'], 500);
        }
    }

    /**
     * Remove the specified model
     */
    public function destroy(AiDocumentModel $model)
    {
        try {
            // Prevent deletion of default model
            if ($model->is_default) {
                return back()->with('error', 'No se puede eliminar el modelo predeterminado');
            }

            // Prevent deletion if model has validation results
            if ($model->validationResults()->exists()) {
                return back()->with('error', 'No se puede eliminar un modelo que tiene resultados de validación');
            }

            $modelName = $model->name;
            $model->delete();

            Log::info('Modelo de IA eliminado', [
                'model_name' => $modelName,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('ai.models.index')
                ->with('success', 'Modelo eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error eliminando modelo de IA', [
                'model_id' => $model->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al eliminar el modelo');
        }
    }
} 