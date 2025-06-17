@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-{{ $log->level_color }}-100 text-{{ $log->level_color }}-600 rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($log->level_icon == 'exclamation-triangle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            @elseif($log->level_icon == 'exclamation-circle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @elseif($log->level_icon == 'information-circle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Detalle del Log</h2>
                        <p class="text-sm text-gray-500">ID: {{ $log->id }}</p>
                    </div>
                </div>
                <a href="{{ route('logs.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
                    Volver
                </a>
            </div>
        </div>

        <!-- Información Principal -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nivel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-{{ $log->level_color }}-50 text-{{ $log->level_color }}-700 border border-{{ $log->level_color }}-100">
                        {{ ucfirst($log->level) }}
                    </span>
                </div>

                <!-- Canal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Canal</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-gray-50 text-gray-700 border border-gray-100">
                        {{ $log->channel }}
                    </span>
                </div>

                <!-- Fecha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora</label>
                    <p class="text-sm text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                    <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                </div>

                <!-- Usuario -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                    @if($log->user)
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $log->user->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $log->user->correo }}</p>
                    </div>
                    @else
                    <p class="text-sm text-gray-400">Sistema</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mensaje -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mensaje</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $log->message }}</p>
            </div>
        </div>

        @if($log->url || $log->method)
        <!-- Información de Request -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de Request</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($log->method)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Método HTTP</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $log->method }}
                    </span>
                </div>
                @endif

                @if($log->url)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                    <p class="text-sm text-gray-900 break-all">{{ $log->url }}</p>
                </div>
                @endif

                @if($log->ip_address)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección IP</label>
                    <p class="text-sm text-gray-900">{{ $log->ip_address }}</p>
                </div>
                @endif

                @if($log->user_agent)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">User Agent</label>
                    <p class="text-sm text-gray-900 break-all">{{ $log->user_agent }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($log->context)
        <!-- Contexto -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contexto</h3>
            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                <pre class="text-green-400 text-sm"><code>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
        @endif

        <!-- Acciones -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Acciones</h3>
                <div class="flex space-x-3">
                    @can('logs.destroy')
                    <button type="button"
                            @click="$dispatch('open-modal', 'confirm-log-deletion')"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                        Eliminar Log
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

@can('logs.destroy')
<!-- Modal de Confirmación de Eliminación -->
<x-modal name="confirm-log-deletion" focusable maxWidth="md">
    <div class="p-6">
        <div class="flex items-center justify-center space-x-4 mb-6">
            <div class="flex-shrink-0 h-12 w-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900">
                    Confirmar Eliminación
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    ¿Estás seguro de que deseas eliminar este log? Esta acción no se puede deshacer.
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-center space-x-3">
            <button type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                Cancelar
            </button>

            <form action="{{ route('logs.destroy', $log) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                    Eliminar Log
                </button>
            </form>
        </div>
    </div>
</x-modal>
@endcan

@endsection 