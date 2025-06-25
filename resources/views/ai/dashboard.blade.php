@extends('layouts.app')

@section('title', 'Dashboard de IA - Validación de Documentos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            🤖 Módulo de Inteligencia Artificial
        </h1>
        
        <div class="flex space-x-3">
            <a href="{{ route('ai.training.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                📊 Entrenar Modelo
            </a>
            <a href="{{ route('ai.validation.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200">
                ✅ Revisar Validaciones
            </a>
        </div>
    </div>

    <!-- Estado del modelo activo -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">🎯 Modelo Activo</h2>
        
        @if($stats['active_model'])
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-medium text-green-800">{{ $stats['active_model']->name }}</h3>
                    <p class="text-sm text-green-600">Versión: {{ $stats['active_model']->version }}</p>
                    <div class="mt-2">
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                            Precisión: {{ number_format($stats['active_model']->accuracy * 100, 1) }}%
                        </span>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800">Tipos Soportados</h4>
                    <ul class="text-sm text-blue-600 mt-1">
                        @foreach($stats['active_model']->supported_document_types as $type)
                            <li>• {{ $type }}</li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h4 class="font-medium text-purple-800">Estadísticas</h4>
                    <p class="text-sm text-purple-600">Muestras de entrenamiento: {{ $stats['active_model']->training_samples_count }}</p>
                    <p class="text-sm text-purple-600">Entrenado: {{ $stats['active_model']->training_completed_at->format('d/m/Y') }}</p>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">No hay modelo activo</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Necesitas entrenar y activar un modelo para comenzar a validar documentos automáticamente.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('ai.training.index') }}" class="text-sm bg-yellow-100 text-yellow-800 px-3 py-1 rounded-md hover:bg-yellow-200 transition duration-200">
                                Entrenar Primer Modelo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total de modelos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <span class="text-blue-600 font-bold">🧠</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Modelos Creados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_models'] }}</p>
                </div>
            </div>
        </div>

        <!-- Datos de entrenamiento -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <span class="text-green-600 font-bold">📊</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Datos de Entrenamiento</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['training_data']['total_samples'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['training_data']['validated_samples'] ?? 0 }} validados</p>
                </div>
            </div>
        </div>

        <!-- Validaciones realizadas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <span class="text-purple-600 font-bold">✅</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Validaciones IA</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['validation_results']['total_validations'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Confianza promedio -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        <span class="text-yellow-600 font-bold">📈</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Confianza Promedio</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format(($stats['validation_results']['average_confidence'] ?? 0) * 100, 1) }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis por tipos de documento -->
    @if(isset($stats['training_data']['samples_by_type']) && count($stats['training_data']['samples_by_type']) > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Datos de Entrenamiento por Tipo</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($stats['training_data']['samples_by_type'] as $type => $count)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-800">{{ $type }}</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $count }}</p>
                    <p class="text-sm text-gray-500">documentos</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Acciones rápidas -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">🚀 Acciones Rápidas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('ai.training.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition duration-200">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📚</span>
                    <div>
                        <h3 class="font-medium text-gray-800">Gestionar Entrenamiento</h3>
                        <p class="text-sm text-gray-600">Subir datos y entrenar modelos</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('ai.models.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition duration-200">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🤖</span>
                    <div>
                        <h3 class="font-medium text-gray-800">Ver Modelos</h3>
                        <p class="text-sm text-gray-600">Administrar modelos de IA</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('ai.validation.index') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition duration-200">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🔍</span>
                    <div>
                        <h3 class="font-medium text-gray-800">Revisar Validaciones</h3>
                        <p class="text-sm text-gray-600">Supervisar resultados de IA</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
// Actualizar estadísticas automáticamente cada 30 segundos
setInterval(function() {
    fetch('{{ route("ai.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Estadísticas actualizadas:', data.stats);
                // Aquí podrías actualizar elementos específicos de la página
            }
        })
        .catch(error => console.log('Error al actualizar estadísticas:', error));
}, 30000);
</script>
@endsection 