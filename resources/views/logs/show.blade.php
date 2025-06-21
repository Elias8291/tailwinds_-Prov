@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Encabezado con Gradiente -->
        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] rounded-xl shadow-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/10 p-2 rounded-lg">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Detalle del Log #{{ $log->id }}</h2>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('logs.index') }}" 
                       class="px-3 py-1.5 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors duration-200 flex items-center text-sm">
                        <i class="fas fa-arrow-left mr-1.5"></i>
                        Volver
                    </a>
                    <form action="{{ route('logs.destroy', $log) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-3 py-1.5 bg-red-500/20 text-white rounded-lg hover:bg-red-500/30 transition-colors duration-200 flex items-center text-sm"
                                onclick="return confirm('¿Estás seguro de eliminar este log?')">
                            <i class="fas fa-trash-alt mr-1.5"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grid Principal -->
        <div class="space-y-4">
            <!-- Fila de Estado y Request -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tarjeta de Estado -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-info-circle text-[#9d2449] mr-1.5"></i>
                            Estado
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($log->level === 'error') bg-red-100 text-red-800
                            @elseif($log->level === 'warning') bg-yellow-100 text-yellow-800
                            @elseif($log->level === 'info') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($log->level) }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">Canal</span>
                            <span class="text-xs font-medium text-gray-800">{{ $log->channel }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">Fecha</span>
                            <span class="text-xs font-medium text-gray-800">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">Usuario</span>
                            <span class="text-xs font-medium text-gray-800">{{ $log->user ? $log->user->name : 'Sistema' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Request -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800 flex items-center mb-3">
                        <i class="fas fa-exchange-alt text-[#9d2449] mr-1.5"></i>
                        Request
                    </h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">Método</span>
                            <span class="text-xs font-medium text-gray-800">{{ $log->request_method }}</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">IP</span>
                            <span class="text-xs font-medium text-gray-800">{{ $log->request_ip }}</span>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600 block mb-0.5">URL</span>
                            <span class="text-xs font-medium text-gray-800 break-all">{{ $log->request_url }}</span>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600 block mb-0.5">User Agent</span>
                            <span class="text-xs font-medium text-gray-800 break-all">{{ $log->request_user_agent }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Mensaje -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <h3 class="text-base font-semibold text-gray-800 flex items-center mb-3">
                    <i class="fas fa-envelope text-[#9d2449] mr-1.5"></i>
                    Mensaje
                </h3>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $log->message }}</p>
                </div>
            </div>

            <!-- Tarjeta de Contexto -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <h3 class="text-base font-semibold text-gray-800 flex items-center mb-3">
                    <i class="fas fa-code text-[#9d2449] mr-1.5"></i>
                    Contexto
                </h3>
                <div class="bg-gray-900 rounded-lg p-3 overflow-x-auto">
                    <pre class="text-green-400 text-xs"><code>{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 