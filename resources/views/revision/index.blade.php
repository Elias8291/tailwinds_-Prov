@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-4 shadow-lg">
                        <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Revisión de Trámites
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Gestión de trámites pendientes de revisión</p>
                    </div>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="flex items-center space-x-6">
                    <div class="text-center bg-gray-50 rounded-lg p-3 border">
                        <div class="text-2xl font-bold text-[#B4325E]">{{ $tramites->total() }}</div>
                        <div class="text-xs text-gray-600">Total</div>
                    </div>
                    <div class="text-center bg-amber-50 rounded-lg p-3 border border-amber-200">
                        <div class="text-2xl font-bold text-amber-600">
                            {{ $tramites->where('estado', 'Pendiente')->count() }}
                        </div>
                        <div class="text-xs text-amber-700">Pendientes</div>
                    </div>
                    <div class="text-center bg-blue-50 rounded-lg p-3 border border-blue-200">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $tramites->where('estado', 'En Revision')->count() }}
                        </div>
                        <div class="text-xs text-blue-700">En Revisión</div>
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
                
                <form method="GET" action="{{ route('revision.index') }}" class="space-y-5">
                    <!-- Fila de filtros y botones -->
                    <div class="flex flex-col xl:flex-row xl:items-end xl:gap-4">
                        <!-- Filtros -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 flex-1">
                            <!-- Búsqueda por RFC -->
                            <div class="group">
                                <label for="rfc" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-pink-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    RFC
                                </label>
                                <div class="relative">
                                    <input type="text" name="rfc" id="rfc" 
                                           value="{{ request('rfc') }}" 
                                           placeholder="Buscar RFC..."
                                           class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 placeholder-gray-400 hover:border-pink-300 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 transition-all duration-200">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por Tipo de Persona -->
                            <div class="group">
                                <label for="tipo_persona" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    Tipo Persona
                                </label>
                                <div class="relative">
                                    <select name="tipo_persona" id="tipo_persona" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                                        <option value="">Todos</option>
                                        <option value="Fisica" {{ request('tipo_persona') == 'Fisica' ? 'selected' : '' }}>Persona Física</option>
                                        <option value="Moral" {{ request('tipo_persona') == 'Moral' ? 'selected' : '' }}>Persona Moral</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por Estado -->
                            <div class="group">
                                <label for="estado" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    Estado
                                </label>
                                <div class="relative">
                                    <select name="estado" id="estado" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-green-300 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all duration-200">
                                        <option value="">Todos</option>
                                        <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="En Revision" {{ request('estado') == 'En Revision' ? 'selected' : '' }}>En Revisión</option>
                                        <option value="Por Cotejar" {{ request('estado') == 'Por Cotejar' ? 'selected' : '' }}>Por Cotejar</option>
                                        <option value="Aprobado" {{ request('estado') == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                                        <option value="Rechazado" {{ request('estado') == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por Tipo de Trámite -->
                            <div class="group">
                                <label for="tipo_tramite" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    Tipo
                                </label>
                                <div class="relative">
                                    <select name="tipo_tramite" id="tipo_tramite" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-purple-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                                        <option value="">Todos</option>
                                        <option value="Inscripcion" {{ request('tipo_tramite') == 'Inscripcion' ? 'selected' : '' }}>Inscripción</option>
                                        <option value="Renovacion" {{ request('tipo_tramite') == 'Renovacion' ? 'selected' : '' }}>Renovación</option>
                                        <option value="Actualizacion" {{ request('tipo_tramite') == 'Actualizacion' ? 'selected' : '' }}>Actualización</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por Tiempo de Revisión -->
                            <div class="group">
                                <label for="tiempo_revision" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    Tiempo
                                </label>
                                <div class="relative">
                                    <select name="tiempo_revision" id="tiempo_revision" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-orange-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all duration-200">
                                        <option value="todos" {{ request('tiempo_revision') == 'todos' ? 'selected' : '' }}>Todos</option>
                                        <option value="hoy" {{ request('tiempo_revision') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                                        <option value="semana" {{ request('tiempo_revision') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                                        <option value="mes" {{ request('tiempo_revision') == 'mes' ? 'selected' : '' }}>Este mes</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Filtro por Fecha de Finalización -->
                            <div class="group">
                                <label for="fecha_finalizacion" class="block text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <div class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    Finalización
                                </label>
                                <div class="relative">
                                    <select name="fecha_finalizacion" id="fecha_finalizacion" class="w-full appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200">
                                        <option value="">Todos</option>
                                        <option value="recientes" {{ request('fecha_finalizacion') == 'recientes' ? 'selected' : '' }}>Recientes</option>
                                        <option value="antiguos" {{ request('fecha_finalizacion') == 'antiguos' ? 'selected' : '' }}>Antiguos</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Botones de acción -->
                        <div class="flex gap-2 mt-4 xl:mt-0 xl:ml-4 justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#7a1d37] text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B4325E]/30 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                                </svg>
                                Filtrar
                            </button>
                            <a href="{{ route('revision.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400/20 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Limpiar
                            </a>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="border-t border-gray-200 my-6"></div>

                    <!-- Tabla de Revisiones -->
                    <div class="overflow-hidden">

                <!-- Vista desktop -->
                <div class="hidden md:block">
                    @if($tramites->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-[#B4325E] to-[#93264B]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Trámite</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Solicitante</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Secciones</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Fechas</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($tramites as $tramite)
                                    <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ ucfirst($tramite->tipo_tramite) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $tramite->solicitante->razon_social ?? $tramite->solicitante->nombre_completo ?? 'Sin información' }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                RFC: {{ $tramite->solicitante->rfc ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $tramite->solicitante->tipo_persona ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium
                                                {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-50 text-yellow-700 border border-yellow-100' : 
                                                   ($tramite->estado == 'En Revision' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 
                                                   ($tramite->estado == 'Aprobado' ? 'bg-green-50 text-green-700 border border-green-100' : 
                                                   ($tramite->estado == 'Rechazado' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-purple-50 text-purple-700 border border-purple-100'))) }}">
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
                                                    <i class="fas fa-calendar-plus mr-2 text-[#B4325E]"></i>
                                                    <div>
                                                        <span class="text-xs text-gray-400">Inicio:</span>
                                                        <span class="ml-1">{{ $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                @if($tramite->fecha_finalizacion)
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar-check mr-2 text-[#B4325E]"></i>
                                                        <div>
                                                            <span class="text-xs text-gray-400">Finalización:</span>
                                                            <span class="ml-1">{{ $tramite->fecha_finalizacion->format('d/m/Y') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <div class="flex items-center justify-center space-x-3">
                                                <a href="{{ route('revision.show', $tramite) }}" 
                                                   class="text-[#B4325E] hover:text-[#93264B] transform hover:scale-110 transition-all duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                                @if($tramite->estado === 'Pendiente' || $tramite->estado === 'Por Cotejar')
                                                <a href="{{ route('revision.show', $tramite) }}" 
                                                   class="text-blue-600 hover:text-blue-900 transform hover:scale-110 transition-all duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>
                                                @endif
                                                @if($tramite->estado === 'En Revision')
                                                <button class="text-green-600 hover:text-green-900 transform hover:scale-110 transition-all duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                                <button class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-900 mb-1">No hay trámites encontrados</h3>
                        <p class="text-xs text-gray-500">No se encontraron trámites que coincidan con los filtros seleccionados</p>
                    </div>
                    @endif
                </div>
                    
                <!-- Vista móvil -->
                <div class="block md:hidden">
                    @if($tramites->count() > 0)
                    <div class="space-y-4 p-4">
                        @foreach($tramites as $tramite)
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900">#{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium
                                        {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-50 text-yellow-700 border border-yellow-100' : 
                                           ($tramite->estado == 'En Revision' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 
                                           ($tramite->estado == 'Aprobado' ? 'bg-green-50 text-green-700 border border-green-100' : 
                                           ($tramite->estado == 'Rechazado' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-purple-50 text-purple-700 border border-purple-100'))) }}">
                                        {{ $tramite->estado }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">{{ ucfirst($tramite->tipo_tramite) }}</p>
                                
                                <!-- Solicitante -->
                                <div class="mt-3">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Solicitante:</p>
                                    <div class="text-sm text-gray-900">
                                        {{ $tramite->solicitante->razon_social ?? $tramite->solicitante->nombre_completo ?? 'Sin información' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        RFC: {{ $tramite->solicitante->rfc ?? 'N/A' }} • {{ $tramite->solicitante->tipo_persona ?? 'N/A' }}
                                    </div>
                                </div>

                                <!-- Fechas -->
                                <div class="mt-3">
                                    <p class="text-xs font-medium text-gray-700 mb-2">Fechas:</p>
                                    <div class="text-xs text-gray-500">
                                        Inicio: {{ $tramite->fecha_inicio ? $tramite->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                        @if($tramite->fecha_finalizacion)
                                        <br>Finalización: {{ $tramite->fecha_finalizacion->format('d/m/Y') }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <!-- Secciones -->
                                    <div class="flex items-center space-x-2">
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
                                            @foreach(array_slice($seccionesVisibles, 0, 3) as $seccionId)
                                                @php
                                                    $revision = $seccionesRevision->where('seccion_id', $seccionId)->first();
                                                    $estado = $revision ? $revision->estado : 'pendiente';
                                                    $color = $estado === 'aprobado' ? 'text-green-600' : 
                                                           ($estado === 'rechazado' ? 'text-red-600' : 'text-gray-400');
                                                    $bgColor = $estado === 'aprobado' ? 'bg-green-50' : 
                                                             ($estado === 'rechazado' ? 'bg-red-50' : 'bg-gray-50');
                                                @endphp
                                                <div class="h-8 w-8 rounded-lg {{ $bgColor }} flex items-center justify-center">
                                                    <i class="fas {{ $secciones[$seccionId]['icon'] }} {{ $color }} text-sm"></i>
                                                </div>
                                            @endforeach
                                            @if(count($seccionesVisibles) > 3)
                                                <div class="text-xs text-gray-500">+{{ count($seccionesVisibles) - 3 }}</div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Acciones -->
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('revision.show', $tramite) }}" 
                                           class="text-[#B4325E] hover:text-[#93264B] transform hover:scale-110 transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($tramite->estado === 'En Revision')
                                        <button class="text-green-600 hover:text-green-900 transform hover:scale-110 transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-900 mb-1">No hay trámites encontrados</h3>
                        <p class="text-xs text-gray-500">No se encontraron trámites que coincidan con los filtros seleccionados</p>
                    </div>
                    @endif
                </div>

                <!-- Paginación usando componente -->
                @include('components.pagination', ['items' => $tramites, 'label' => 'trámites'])
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush 