@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-white/10 text-white shadow-md">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            Mis Trámites
                        </h1>
                        <p class="text-sm text-white/80 mt-1">Gestión de tus trámites y solicitudes</p>
                    </div>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="flex items-center space-x-6">
                    <div class="text-center bg-white/10 rounded-lg p-3">
                        <div class="text-2xl font-bold text-white">{{ $tramites->total() }}</div>
                        <div class="text-xs text-white/80">Total</div>
                    </div>
                    <div class="text-center bg-white/10 rounded-lg p-3">
                        <div class="text-2xl font-bold text-amber-300">
                            {{ $tramites->where('estado', 'Pendiente')->count() }}
                        </div>
                        <div class="text-xs text-white/80">Pendientes</div>
                    </div>
                    <div class="text-center bg-white/10 rounded-lg p-3">
                        <div class="text-2xl font-bold text-blue-300">
                            {{ $tramites->where('estado', 'En Revision')->count() }}
                        </div>
                        <div class="text-xs text-white/80">En Revisión</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('mis-tramites.index') }}" class="space-y-4">
                <!-- Grid de filtros -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Filtro por Estado -->
                    <div class="bg-gray-50 rounded-lg p-3">
                        <label for="estado" class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-tasks mr-1 text-[#9d2449]"></i>
                            Estado
                        </label>
                        <select name="estado" id="estado" class="w-full text-sm rounded-lg border-gray-200 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En Revision" {{ request('estado') == 'En Revision' ? 'selected' : '' }}>En Revisión</option>
                            <option value="Aprobado" {{ request('estado') == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="Rechazado" {{ request('estado') == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                        </select>
                    </div>

                    <!-- Filtro por Tipo de Trámite -->
                    <div class="bg-gray-50 rounded-lg p-3">
                        <label for="tipo_tramite" class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-file-alt mr-1 text-[#9d2449]"></i>
                            Tipo
                        </label>
                        <select name="tipo_tramite" id="tipo_tramite" class="w-full text-sm rounded-lg border-gray-200 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20">
                            <option value="">Todos</option>
                            <option value="Inscripcion" {{ request('tipo_tramite') == 'Inscripcion' ? 'selected' : '' }}>Inscripción</option>
                            <option value="Renovacion" {{ request('tipo_tramite') == 'Renovacion' ? 'selected' : '' }}>Renovación</option>
                            <option value="Actualizacion" {{ request('tipo_tramite') == 'Actualizacion' ? 'selected' : '' }}>Actualización</option>
                        </select>
                    </div>

                    <!-- Filtro por Fecha -->
                    <div class="bg-gray-50 rounded-lg p-3">
                        <label for="fecha" class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-calendar mr-1 text-[#9d2449]"></i>
                            Fecha
                        </label>
                        <select name="fecha" id="fecha" class="w-full text-sm rounded-lg border-gray-200 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20">
                            <option value="">Todas</option>
                            <option value="hoy" {{ request('fecha') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                            <option value="semana" {{ request('fecha') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                            <option value="mes" {{ request('fecha') == 'mes' ? 'selected' : '' }}>Este mes</option>
                        </select>
                    </div>

                    <!-- Búsqueda -->
                    <div class="bg-gray-50 rounded-lg p-3">
                        <label for="buscar" class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-search mr-1 text-[#9d2449]"></i>
                            Buscar
                        </label>
                        <div class="relative">
                            <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                                   class="w-full text-sm rounded-lg border-gray-200 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 pr-10"
                                   placeholder="Buscar trámite...">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-search text-gray-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('mis-tramites.index') }}" 
                       class="px-4 py-2 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center">
                        <i class="fas fa-times mr-2 text-xs"></i>
                        Limpiar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm bg-[#9d2449] text-white rounded-lg hover:bg-[#7a1d37] transition-colors duration-200 flex items-center">
                        <i class="fas fa-filter mr-2 text-xs"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Trámites -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            @if($tramites->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-file-alt text-[#9d2449]"></i>
                                        <span>Trámite</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-tasks text-[#9d2449]"></i>
                                        <span>Estado</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-clipboard-list text-[#9d2449]"></i>
                                        <span>Progreso</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-calendar-alt text-[#9d2449]"></i>
                                        <span>Fechas</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-cog text-[#9d2449]"></i>
                                        <span>Acciones</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tramites as $tramite)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-2">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ ucfirst($tramite->tipo_tramite) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-3 py-1.5 text-xs font-semibold rounded-full
                                            {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($tramite->estado == 'En Revision' ? 'bg-blue-100 text-blue-800' : 
                                               ($tramite->estado == 'Aprobado' ? 'bg-green-100 text-green-800' : 
                                               ($tramite->estado == 'Rechazado' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800'))) }}">
                                            <i class="fas {{ $tramite->estado == 'Pendiente' ? 'fa-clock' : 
                                                           ($tramite->estado == 'En Revision' ? 'fa-tasks' : 
                                                           ($tramite->estado == 'Aprobado' ? 'fa-check' : 
                                                           ($tramite->estado == 'Rechazado' ? 'fa-times' : 'fa-question'))) }} mr-1.5"></i>
                                            {{ $tramite->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            @php
                                                $secciones = [
                                                    1 => ['icon' => 'fa-user', 'color' => 'text-blue-600', 'title' => 'Datos Generales'],
                                                    2 => ['icon' => 'fa-home', 'color' => 'text-green-600', 'title' => 'Domicilio'],
                                                    3 => ['icon' => 'fa-building', 'color' => 'text-purple-600', 'title' => 'Constitución'],
                                                    4 => ['icon' => 'fa-users', 'color' => 'text-amber-600', 'title' => 'Accionistas'],
                                                    5 => ['icon' => 'fa-user-tie', 'color' => 'text-red-600', 'title' => 'Apoderado'],
                                                    6 => ['icon' => 'fa-file-alt', 'color' => 'text-indigo-600', 'title' => 'Documentos']
                                                ];
                                                
                                                $tipoPersona = strtolower(trim($tramite->solicitante->tipo_persona ?? 'fisica'));
                                                $seccionesVisibles = $tipoPersona === 'moral' ? [1,2,3,4,5,6] : [1,2,6];
                                                $seccionesRevision = $tramite->seccionesRevision ?? collect();
                                            @endphp
                                            
                                            @if(!empty($seccionesVisibles))
                                                @foreach($seccionesVisibles as $seccionId)
                                                    @php
                                                        $revision = $seccionesRevision->where('seccion_id', $seccionId)->first();
                                                        $estado = $revision ? $revision->estado : 'pendiente';
                                                        $color = $estado === 'aprobado' ? 'text-green-600' : 
                                                               ($estado === 'rechazado' ? 'text-red-600' : 'text-gray-400');
                                                        $bgColor = $estado === 'aprobado' ? 'bg-green-50' : 
                                                                 ($estado === 'rechazado' ? 'bg-red-50' : 'bg-gray-50');
                                                    @endphp
                                                    <div class="relative group">
                                                        <div class="h-10 w-10 rounded-xl {{ $bgColor }} flex items-center justify-center hover:bg-gray-100 transition-colors duration-200 shadow-sm">
                                                            <i class="fas {{ $secciones[$seccionId]['icon'] }} {{ $color }} text-lg"></i>
                                                        </div>
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap shadow-lg">
                                                            {{ $secciones[$seccionId]['title'] }} - {{ ucfirst($estado) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-sm text-gray-500">Sin secciones disponibles</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-plus mr-2 text-[#9d2449]"></i>
                                                <div>
                                                    <span class="text-xs text-gray-400">Inicio:</span>
                                                    <span class="ml-1">{{ $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y') : 'N/A' }}</span>
                                                </div>
                                            </div>
                                            @if($tramite->fecha_finalizacion)
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar-check mr-2 text-[#9d2449]"></i>
                                                    <div>
                                                        <span class="text-xs text-gray-400">Finalización:</span>
                                                        <span class="ml-1">{{ $tramite->fecha_finalizacion->format('d/m/Y') }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('mis-tramites.show', $tramite) }}" 
                                               class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center text-[#9d2449] hover:bg-[#9d2449] hover:text-white transition-colors duration-200"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($tramite->estado === 'Pendiente')
                                                <a href="{{ route('mis-tramites.edit', $tramite) }}" 
                                                   class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center text-blue-600 hover:bg-blue-600 hover:text-white transition-colors duration-200"
                                                   title="Editar trámite">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($tramite->estado === 'Aprobado')
                                                <a href="{{ route('mis-tramites.download', $tramite) }}" 
                                                   class="h-8 w-8 rounded-lg bg-gray-50 flex items-center justify-center text-green-600 hover:bg-green-600 hover:text-white transition-colors duration-200"
                                                   title="Descargar constancia">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($tramites->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                                    Anterior
                                </span>
                            @else
                                <a href="{{ $tramites->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Anterior
                                </a>
                            @endif

                            @if($tramites->hasMorePages())
                                <a href="{{ $tramites->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Siguiente
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                                    Siguiente
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-center">
                            <div class="flex flex-col items-center space-y-4">
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ $tramites->firstItem() }}</span>
                                    a
                                    <span class="font-medium">{{ $tramites->lastItem() }}</span>
                                    de
                                    <span class="font-medium">{{ $tramites->total() }}</span>
                                    resultados
                                </p>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    @if($tramites->onFirstPage())
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                            <span class="sr-only">Anterior</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $tramites->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Anterior</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    @php
                                        $start = max(1, $tramites->currentPage() - 2);
                                        $end = min($tramites->lastPage(), $tramites->currentPage() + 2);
                                    @endphp

                                    @if($start > 1)
                                        <a href="{{ $tramites->url(1) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            1
                                        </a>
                                        @if($start > 2)
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        @endif
                                    @endif

                                    @for($i = $start; $i <= $end; $i++)
                                        @if($i == $tramites->currentPage())
                                            <span class="relative inline-flex items-center px-4 py-2 border border-[#9d2449] bg-[#9d2449] text-sm font-medium text-white">
                                                {{ $i }}
                                            </span>
                                        @else
                                            <a href="{{ $tramites->url($i) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                {{ $i }}
                                            </a>
                                        @endif
                                    @endfor

                                    @if($end < $tramites->lastPage())
                                        @if($end < $tramites->lastPage() - 1)
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        @endif
                                        <a href="{{ $tramites->url($tramites->lastPage()) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $tramites->lastPage() }}
                                        </a>
                                    @endif

                                    @if($tramites->hasMorePages())
                                        <a href="{{ $tramites->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Siguiente</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                            <span class="sr-only">Siguiente</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <div class="mx-auto h-16 w-16 text-gray-400 mb-4">
                        <i class="fas fa-clipboard-list text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay trámites encontrados</h3>
                    <p class="text-gray-500">No se encontraron trámites que coincidan con los filtros seleccionados.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush 