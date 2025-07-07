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
                 class="bg-white rounded-xl shadow-xl border-l-4 border-emerald-500 p-4">
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
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white rounded-xl shadow-xl border-l-4 border-red-500 p-4">
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
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-4 shadow-lg">
                        <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Logs del Sistema
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Monitoreo y seguimiento de eventos del sistema</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 mb-6">
            <div class="p-6">
                <!-- Header compacto -->
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                        Filtros de Búsqueda
                    </h3>
                </div>
                
                <form method="GET" action="{{ route('logs.index') }}" class="space-y-5">
                    <!-- Fila de filtros y botones -->
                    <div class="flex flex-col xl:flex-row xl:items-end xl:gap-4">
                        <!-- Filtros -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 flex-1">
                            <!-- Filtro por nivel -->
                            <div class="group">
                                <label for="level" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4v10a2 2 0 002 2h6a2 2 0 002-2V8M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h2m8-12h2a2 2 0 012 2v8a2 2 0 01-2 2h-2"/>
                                        </svg>
                                    </div>
                                    Nivel
                                </label>
                                <div class="relative">
                                    <select name="level" id="level" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                                        <option value="">Todos los niveles</option>
                                        @foreach($levels as $level)
                                        <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por canal -->
                            <div class="group">
                                <label for="channel" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                        </svg>
                                    </div>
                                    Canal
                                </label>
                                <div class="relative">
                                    <select name="channel" id="channel" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                        <option value="">Todos los canales</option>
                                        @foreach($channels as $channel)
                                        <option value="{{ $channel }}" {{ request('channel') == $channel ? 'selected' : '' }}>
                                            {{ ucfirst($channel) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Fecha desde -->
                            <div class="group">
                                <label for="date_from" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 8h6m-6 4h6M3 7h18a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                                        </svg>
                                    </div>
                                    Desde
                                </label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                       class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-200">
                            </div>
                            <!-- Fecha hasta -->
                            <div class="group">
                                <label for="date_to" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    Hasta
                                </label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                       class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-orange-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all duration-200">
                            </div>
                            <!-- Búsqueda por mensaje -->
                            <div class="group">
                                <label for="search" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-pink-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    Buscar
                                </label>
                                <div class="relative">
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                           placeholder="Buscar en mensajes..."
                                           class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 placeholder-gray-400 hover:border-pink-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-200">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Botones de acción -->
                        <div class="flex gap-3 mt-4 xl:mt-0 xl:ml-4 justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#7a1d37] text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B4325E]/30 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                                </svg>
                                Filtrar
                            </button>
                            <a href="{{ route('logs.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400/20 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Separador -->
                <div class="border-t border-gray-200 my-6"></div>

                <!-- Tabla de Logs -->
                <div class="overflow-hidden">
            <!-- Vista móvil -->
            <div class="block md:hidden">
                @forelse($logs as $log)
                <div class="p-6 border-b border-gray-100 hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-{{ $log->level_color }}-50 text-{{ $log->level_color }}-700 border border-{{ $log->level_color }}-100">
                                        {{ ucfirst($log->level) }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $log->channel }}</span>
                                </div>
                                <p class="text-base font-medium text-gray-900 mb-2">{{ Str::limit($log->message, 100) }}</p>
                                <div class="text-sm text-gray-500">
                                    <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                    @if($log->user)
                                    <span class="mx-2">•</span>
                                    <span>{{ $log->user->nombre }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('logs.show', $log) }}" 
                                   class="p-2 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
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
                        <tr class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white">
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nivel</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mensaje</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Usuario</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Canal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-{{ $log->level_color }}-50 text-{{ $log->level_color }}-700 border border-{{ $log->level_color }}-100">
                                    {{ ucfirst($log->level) }}
                                </span>
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
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-gray-50 text-gray-700 border border-gray-100">
                                    {{ $log->channel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('logs.show', $log) }}" 
                                       class="p-2 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
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

                    <!-- Paginación usando componente -->
                    <x-pagination :items="$logs" label="logs" />
                </div>
            </div>
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