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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Gestión de Proveedores
                        </h2>
                        <p class="text-sm text-gray-500">Administra los proveedores registrados</p>
                    </div>
                </div>
                <a href="{{ route('proveedores.create') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transform hover:scale-105 transition-all duration-300 hover:shadow-lg w-full md:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Nuevo Proveedor
                </a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Barra de herramientas de la tabla -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col lg:flex-row gap-4 justify-between">
                    <!-- Formulario de búsqueda -->
                    <form method="GET" action="{{ route('proveedores.index') }}" 
                          class="flex flex-col md:flex-row gap-3 flex-grow">
                        <div class="flex flex-grow">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request()->get('search') }}"
                                   placeholder="Buscar por RFC..."
                                   class="w-full px-3 h-10 rounded-l border-2 border-[#B4325E] focus:outline-none focus:border-[#B4325E]">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-r px-2 md:px-3 py-0 md:py-1 hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                        <select name="estado" 
                                onchange="this.form.submit()"
                                class="w-full md:w-48 h-10 border-2 border-gray-300 focus:outline-none focus:border-gray-400 text-gray-700 rounded px-2 md:px-3 py-0 md:py-1 tracking-wider hover:border-gray-400 transition-colors duration-200">
                            <option value="" {{ !request()->get('estado') ? 'selected' : '' }}>Todos los estados</option>
                            <option value="Activo" {{ request()->get('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Inactivo" {{ request()->get('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            <option value="Pendiente Renovacion" {{ request()->get('estado') == 'Pendiente Renovacion' ? 'selected' : '' }}>Pendiente Renovación</option>
                        </select>
                    </form>
                </div>

                <!-- Selector de cantidad por página y contador -->
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <label for="perPage" class="text-sm text-gray-600">Mostrar:</label>
                        <select id="perPage" 
                                class="bg-white border-2 border-gray-300 text-gray-700 text-sm rounded px-2 py-1.5 focus:outline-none focus:border-gray-400 hover:border-gray-400 transition-colors duration-200"
                                onchange="window.location.href = this.value">
                            @foreach([10, 25, 50, 100] as $option)
                                <option value="{{ request()->fullUrlWithQuery(['perPage' => $option]) }}"
                                        {{ request()->get('perPage', 10) == $option ? 'selected' : '' }}>
                                    {{ $option }} elementos
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-sm text-gray-600">
                        @if(request()->has('search'))
                            Resultados para "{{ request()->get('search') }}"
                            @if(request()->has('estado'))
                                en estado "{{ request()->get('estado') }}"
                            @endif
                            : 
                        @endif
                        <span class="font-medium text-gray-900">{{ $proveedores->total() }}</span> elementos
                    </div>
                </div>
            </div>

            <!-- Vista móvil -->
            <div class="block md:hidden">
                @foreach($proveedores as $proveedor)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                {{ strtoupper(substr($proveedor->pv, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $proveedor->pv }}</div>
                                <div class="text-sm text-gray-500">RFC: {{ $proveedor->rfc }}</div>
                                <div class="text-sm text-gray-500">{{ $proveedor->tipo_persona }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('proveedores.edit', $proveedor->pv) }}" 
                               class="p-2 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>

                            <button type="button"
                                    @click="$dispatch('open-modal', 'confirm-proveedor-deletion-{{ $proveedor->pv }}')"
                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium 
                            @if($proveedor->estado === 'Activo') bg-green-50 text-green-700 border border-green-100
                            @elseif($proveedor->estado === 'Inactivo') bg-red-50 text-red-700 border border-red-100
                            @else bg-yellow-50 text-yellow-700 border border-yellow-100
                            @endif">
                            {{ $proveedor->estado }}
                        </span>
                    </div>
                </div>
                @endforeach

                <!-- Paginación móvil -->
                @if ($proveedores->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <!-- Botones Anterior/Siguiente -->
                            <div class="flex items-center gap-2">
                                @if ($proveedores->onFirstPage())
                                    <span class="inline-flex items-center px-4 py-2 text-sm bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                        Anterior
                                    </span>
                                @else
                                    <a href="{{ $proveedores->previousPageUrl() }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 border border-gray-200">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                        Anterior
                                    </a>
                                @endif

                                @if ($proveedores->hasMorePages())
                                    <a href="{{ $proveedores->nextPageUrl() }}" 
                                       class="inline-flex items-center px-4 py-2 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 border border-gray-200">
                                        Siguiente
                                        <svg class="w-5 h-5 ml-1 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 text-sm bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                        Siguiente
                                        <svg class="w-5 h-5 ml-1 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>

                            <!-- Indicador de página -->
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">{{ $proveedores->currentPage() }}</span>
                                de
                                <span class="font-medium">{{ $proveedores->lastPage() }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Vista desktop -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-[#B4325E] text-white">
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">PV</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">RFC</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Tipo</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha Registro</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha Vencimiento</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($proveedores as $proveedor)
                        <tr class="hover:bg-gray-50/50 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                        {{ strtoupper(substr($proveedor->pv, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $proveedor->pv }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $proveedor->rfc }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $proveedor->tipo_persona }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $proveedor->fecha_registro }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $proveedor->fecha_vencimiento }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium 
                                    @if($proveedor->estado === 'Activo') bg-green-50 text-green-700 border border-green-100
                                    @elseif($proveedor->estado === 'Inactivo') bg-red-50 text-red-700 border border-red-100
                                    @else bg-yellow-50 text-yellow-700 border border-yellow-100
                                    @endif">
                                    {{ $proveedor->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('proveedores.edit', $proveedor->pv) }}" 
                                       class="text-primary hover:text-primary-dark transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>

                                    <button type="button"
                                            @click="$dispatch('open-modal', 'confirm-proveedor-deletion-{{ $proveedor->pv }}')"
                                            class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                @if ($proveedores->hasPages())
                    <div class="px-6 py-4 bg-white border-t border-gray-100">
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
                            <div class="flex items-center gap-2">
                                {{-- Botón Anterior --}}
                                @if ($proveedores->onFirstPage())
                                    <span class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed px-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $proveedores->previousPageUrl() }}" 
                                       class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[#B4325E] transition-colors duration-200 px-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                    </a>
                                @endif

                                {{-- Números de Página con Boundaries --}}
                                <div class="flex items-center gap-2">
                                    @php
                                        $currentPage = $proveedores->currentPage();
                                        $lastPage = $proveedores->lastPage();
                                        $boundaries = 2;
                                        $start = max(1, min($currentPage - $boundaries, $lastPage - (2 * $boundaries)));
                                        $end = min($lastPage, max($currentPage + $boundaries, 1 + (2 * $boundaries)));
                                    @endphp

                                    @if($start > 1)
                                        <a href="{{ $proveedores->url(1) }}" 
                                           class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[#B4325E] transition-colors duration-200 px-4">
                                            1
                                        </a>
                                        @if($start > 2)
                                            <span class="text-gray-500 px-1">...</span>
                                        @endif
                                    @endif

                                    @for($i = $start; $i <= $end; $i++)
                                        @if($i == $currentPage)
                                            <span class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm font-semibold text-white bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-lg shadow-md px-4">
                                                {{ $i }}
                                            </span>
                                        @else
                                            <a href="{{ $proveedores->url($i) }}" 
                                               class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[#B4325E] transition-colors duration-200 px-4">
                                                {{ $i }}
                                            </a>
                                        @endif
                                    @endfor

                                    @if($end < $lastPage)
                                        @if($end < $lastPage - 1)
                                            <span class="text-gray-500 px-1">...</span>
                                        @endif
                                        <a href="{{ $proveedores->url($lastPage) }}" 
                                           class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[#B4325E] transition-colors duration-200 px-4">
                                            {{ $lastPage }}
                                        </a>
                                    @endif
                                </div>

                                {{-- Botón Siguiente --}}
                                @if ($proveedores->hasMorePages())
                                    <a href="{{ $proveedores->nextPageUrl() }}" 
                                       class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-white text-gray-700 rounded-lg hover:bg-[#B4325E]/10 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[#B4325E] transition-colors duration-200 px-3">
                                        <svg class="w-5 h-5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center justify-center min-w-[2.75rem] h-11 text-sm bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed px-3">
                                        <svg class="w-5 h-5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.5 19l-7-7 7-7"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modales de Confirmación de Eliminación -->
@foreach($proveedores as $proveedor)
    <x-modal name="confirm-proveedor-deletion-{{ $proveedor->pv }}" focusable maxWidth="md">
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
                        ¿Estás seguro de que deseas eliminar al proveedor "{{ $proveedor->pv }}"? Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-center space-x-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors duration-200">
                    Cancelar
                </button>

                <form method="POST" action="{{ route('proveedores.destroy', $proveedor->pv) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        Eliminar Proveedor
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
@endforeach
@endsection 