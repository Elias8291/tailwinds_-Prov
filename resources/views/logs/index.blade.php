@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Logs del Sistema
                        </h2>
                        <p class="text-sm text-gray-500">Monitoreo y seguimiento de eventos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" action="{{ route('logs.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Filtro por nivel -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
                        <select name="level" id="level" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                            <option value="">Todos los niveles</option>
                            @foreach($levels as $level)
                            <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por canal -->
                    <div>
                        <label for="channel" class="block text-sm font-medium text-gray-700 mb-2">Canal</label>
                        <select name="channel" id="channel" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                            <option value="">Todos los canales</option>
                            @foreach($channels as $channel)
                            <option value="{{ $channel }}" {{ request('channel') == $channel ? 'selected' : '' }}>
                                {{ ucfirst($channel) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fecha desde -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                    </div>

                    <!-- Fecha hasta -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                    </div>
                </div>

                <!-- Búsqueda por mensaje -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar en mensajes</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Buscar en mensajes de logs..."
                               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring-[#B4325E]">
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-lg hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-300">
                            Filtrar
                        </button>
                        <a href="{{ route('logs.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Vista móvil -->
            <div class="block md:hidden">
                @forelse($logs as $log)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 bg-{{ $log->level_color }}-100 text-{{ $log->level_color }}-600 rounded-xl shadow-md flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-{{ $log->level_color }}-50 text-{{ $log->level_color }}-700 border border-{{ $log->level_color }}-100">
                                        {{ ucfirst($log->level) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $log->channel }}</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-1">{{ Str::limit($log->message, 100) }}</p>
                                <div class="text-xs text-gray-500">
                                    <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                    @if($log->user)
                                    <span class="mx-1">•</span>
                                    <span>{{ $log->user->nombre }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @can('logs.show')
                            <a href="{{ route('logs.show', $log) }}" 
                               class="p-2 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @endcan

                            @can('logs.destroy')
                            <button type="button"
                                    @click="$dispatch('open-modal', 'confirm-log-deletion-{{ $log->id }}')"
                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500">No se encontraron logs con los filtros aplicados.</p>
                </div>
                @endforelse
            </div>

            <!-- Vista desktop -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-[#B4325E] text-white">
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Nivel</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Mensaje</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Usuario</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Canal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-{{ $log->level_color }}-100 text-{{ $log->level_color }}-600 rounded-xl shadow-md flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-{{ $log->level_color }}-50 text-{{ $log->level_color }}-700 border border-{{ $log->level_color }}-100">
                                            {{ ucfirst($log->level) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($log->message, 80) }}</div>
                                @if($log->url)
                                <div class="text-xs text-gray-500">{{ $log->method }} {{ $log->url }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                <div class="text-sm text-gray-900">{{ $log->user->nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $log->user->correo }}</div>
                                @else
                                <span class="text-sm text-gray-400">Sistema</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border border-gray-100">
                                    {{ $log->channel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-3">
                                    @can('logs.show')
                                    <a href="{{ route('logs.show', $log) }}" 
                                       class="text-primary hover:text-primary-dark transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @endcan

                                    @can('logs.destroy')
                                    <button type="button"
                                            @click="$dispatch('open-modal', 'confirm-log-deletion-{{ $log->id }}')"
                                            class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500">No se encontraron logs con los filtros aplicados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($logs->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales de Confirmación de Eliminación -->
@foreach($logs as $log)
<x-modal name="confirm-log-deletion-{{ $log->id }}" focusable maxWidth="md">
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
@endforeach

@endsection 