@extends('layouts.app')

@section('title', 'Entrenamiento de IA - Dashboard')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🤖 Entrenamiento de IA</h1>
                <p class="text-gray-600 mt-2">Gestiona el entrenamiento de modelos para reconocimiento de documentos</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('ai.training.upload') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Subir Documentos
                </a>
                <a href="{{ route('ai.training.review') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Revisar Documentos
                </a>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Documentos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $trainingStats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Validados</p>
                        <p class="text-2xl font-bold text-green-600">{{ $trainingStats['validated'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendientes</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $trainingStats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rechazados</p>
                        <p class="text-2xl font-bold text-red-600">{{ $trainingStats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progreso por Tipo de Documento -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">📊 Progreso por Tipo de Documento</h3>
                <div class="space-y-6">
                    @foreach($documentTypeStats as $stat)
                        <div class="border-b border-gray-100 pb-4 last:border-b-0">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium text-gray-900">{{ $stat['nombre'] }}</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">{{ $stat['count'] }}/30</span>
                                    @if($stat['status'] === 'complete')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Completo</span>
                                    @elseif($stat['status'] === 'partial')
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">Parcial</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Insuficiente</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300 
                                    {{ $stat['status'] === 'complete' ? 'bg-green-500' : ($stat['status'] === 'partial' ? 'bg-yellow-500' : 'bg-red-500') }}"
                                     style="width: {{ min(100, ($stat['count'] / 30) * 100) }}%"></div>
                            </div>
                            @if($stat['needed'] > 0)
                                <p class="text-xs text-gray-500 mt-1">Necesita {{ $stat['needed'] }} documentos más</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modelos de IA -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">🧠 Modelos de IA</h3>
                    <button onclick="openTrainingModal()" 
                            class="inline-flex items-center px-3 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Entrenar Modelo
                    </button>
                </div>

                @if($models && $models->count() > 0)
                    <div class="space-y-4">
                        @foreach($models as $model)
                            <div class="border border-gray-200 rounded-lg p-4 {{ $model->is_default ? 'ring-2 ring-primary bg-primary-50' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <h4 class="font-semibold text-gray-900">{{ $model->name }}</h4>
                                            @if($model->is_default)
                                                <span class="ml-2 px-2 py-1 text-xs bg-primary text-white rounded-full">Activo</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $model->description }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs text-gray-500">
                                                Precisión: <strong>{{ $model->accuracy_percentage }}</strong>
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Documentos: <strong>{{ $model->training_documents_count }}</strong>
                                            </span>
                                            @if($model->trained_at)
                                                <span class="text-xs text-gray-500">
                                                    Entrenado: <strong>{{ $model->trained_at->format('d/m/Y') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $model->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($model->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No hay modelos entrenados</h4>
                        <p class="text-gray-600 mb-4">Entrena tu primer modelo con los documentos validados</p>
                        <button onclick="openTrainingModal()" 
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                            Entrenar Primer Modelo
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Estado Inicial / Actividad Reciente -->
        <div class="bg-white rounded-xl shadow-md p-6">
            @if($trainingStats['total'] == 0)
                <div class="text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 bg-primary-100 rounded-full mb-6">
                        <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">¡Comienza el Entrenamiento de IA! 🚀</h3>
                    <p class="text-gray-600 mb-6">
                        Aún no has subido documentos para entrenar el modelo.<br>
                        Sube documentos de ejemplo para que la IA aprenda a reconocer diferentes tipos.
                    </p>
                    <div class="space-y-4">
                        <a href="{{ route('ai.training.upload') }}" 
                           class="inline-flex items-center px-6 py-3 bg-primary text-white text-lg rounded-lg hover:bg-primary-dark transition-colors shadow-lg">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Subir Primeros Documentos
                        </a>
                        <div class="text-sm text-gray-500">
                            <p>💡 <strong>Tip:</strong> Sube al menos 10-20 documentos por tipo para mejores resultados</p>
                        </div>
                    </div>
                </div>
            @else
                <h3 class="text-xl font-bold text-gray-900 mb-6">⚡ Actividad Reciente</h3>
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-600">Aquí se mostrará la actividad reciente del sistema</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Entrenamiento -->
<div id="trainingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Entrenar Nuevo Modelo</h3>
                    <button onclick="closeTrainingModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form id="trainingForm" onsubmit="startTraining(event)">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Modelo *</label>
                        <input type="text" id="modelName" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Ej: Modelo_Documentos_v1.0">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="modelDescription" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Descripción del modelo..."></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipos de Documento</label>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            @foreach($documentTypes as $docType)
                                <label class="flex items-center">
                                    <input type="checkbox" name="document_types[]" value="{{ $docType->id }}"
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-700">{{ $docType->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeTrainingModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" id="trainBtn"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50">
                            <div id="trainSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            <span id="trainText">Iniciar Entrenamiento</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openTrainingModal() {
    document.getElementById('trainingModal').classList.remove('hidden');
}

function closeTrainingModal() {
    document.getElementById('trainingModal').classList.add('hidden');
    document.getElementById('trainingForm').reset();
}

async function startTraining(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const documentTypes = Array.from(document.querySelectorAll('input[name="document_types[]"]:checked'))
        .map(input => input.value);
    
    if (documentTypes.length === 0) {
        showAlert('Debe seleccionar al menos un tipo de documento', 'error');
        return;
    }
    
    const btn = document.getElementById('trainBtn');
    const spinner = document.getElementById('trainSpinner');
    const text = document.getElementById('trainText');
    const originalText = text.textContent;
    
    // Mostrar carga
    btn.disabled = true;
    spinner.classList.remove('hidden');
    text.textContent = 'Entrenando modelo...';
    
    try {
        const response = await fetch('{{ route("ai.training.train") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                model_name: document.getElementById('modelName').value,
                description: document.getElementById('modelDescription').value,
                document_types: documentTypes
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Mostrar éxito
            spinner.classList.add('hidden');
            text.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>¡Entrenado!';
            btn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg';
            
            setTimeout(() => {
                showAlert(result.message, 'success');
                closeTrainingModal();
                if (result.redirect) {
                    window.location.reload();
                }
            }, 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al iniciar el entrenamiento', 'error');
    } finally {
        setTimeout(() => {
            btn.disabled = false;
            spinner.classList.add('hidden');
            text.textContent = originalText;
            btn.className = 'px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50';
        }, result?.success ? 1000 : 0);
    }
}

// Función de alerta reutilizable
function showAlert(message, type) {
    const alertColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500'
    };
    
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 ${alertColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.id === 'trainingModal') closeTrainingModal();
});
</script>
@endpush 