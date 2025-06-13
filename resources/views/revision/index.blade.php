@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#7a1d37] text-white shadow-md">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Revisión de Trámites
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">Gestión de trámites pendientes de revisión</p>
                    </div>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-[#9d2449]">{{ $tramites->total() }}</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-600">
                            {{ $tramites->where('estado', 'Pendiente')->count() }}
                        </div>
                        <div class="text-xs text-gray-500">Pendientes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $tramites->where('estado', 'En Revision')->count() }}
                        </div>
                        <div class="text-xs text-gray-500">En Revisión</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" action="{{ route('revision.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filtro por Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado" id="estado" class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Revision" {{ request('estado') == 'En Revision' ? 'selected' : '' }}>En Revisión</option>
                        <option value="Por Cotejar" {{ request('estado') == 'Por Cotejar' ? 'selected' : '' }}>Por Cotejar</option>
                    </select>
                </div>

                <!-- Filtro por Tipo de Trámite -->
                <div>
                    <label for="tipo_tramite" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Trámite</label>
                    <select name="tipo_tramite" id="tipo_tramite" class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20">
                        <option value="">Todos los tipos</option>
                        <option value="Inscripcion" {{ request('tipo_tramite') == 'Inscripcion' ? 'selected' : '' }}>Inscripción</option>
                        <option value="Renovacion" {{ request('tipo_tramite') == 'Renovacion' ? 'selected' : '' }}>Renovación</option>
                        <option value="Actualizacion" {{ request('tipo_tramite') == 'Actualizacion' ? 'selected' : '' }}>Actualización</option>
                    </select>
                </div>

                <!-- Búsqueda por RFC -->
                <div>
                    <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">RFC</label>
                    <input type="text" name="rfc" id="rfc" value="{{ request('rfc') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20"
                           placeholder="Buscar por RFC">
                </div>

                <!-- Botones -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-[#9d2449] text-white px-4 py-2 rounded-lg hover:bg-[#7a1d37] transition-colors duration-300">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                    <a href="{{ route('revision.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-300">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista de Trámites -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            @if($tramites->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trámite
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Solicitante
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Progreso
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tramites as $tramite)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center
                                                    {{ $tramite->tipo_tramite == 'Inscripcion' ? 'bg-green-100 text-green-600' : 
                                                       ($tramite->tipo_tramite == 'Renovacion' ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600') }}">
                                                    <i class="fas {{ $tramite->tipo_tramite == 'Inscripcion' ? 'fa-plus' : 
                                                                   ($tramite->tipo_tramite == 'Renovacion' ? 'fa-sync' : 'fa-edit') }}"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ ucfirst($tramite->tipo_tramite) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $tramite->solicitante->razon_social ?? 'Sin información' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            RFC: {{ $tramite->solicitante->rfc ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($tramite->estado == 'En Revision' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                            {{ $tramite->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-[#9d2449] h-2 rounded-full" style="width: {{ $tramite->progreso_tramite }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ $tramite->progreso_tramite }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $tramite->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $tramite->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button class="text-[#9d2449] hover:text-[#7a1d37] transition-colors duration-200" 
                                                    title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('revision.show', $tramite) }}" 
                                               class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                               title="Iniciar revisión">
                                                <i class="fas fa-play"></i>
                                            </a>
                                            <button class="text-green-600 hover:text-green-800 transition-colors duration-200"
                                                    title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-800 transition-colors duration-200"
                                                    title="Rechazar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $tramites->links() }}
                </div>
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                        <i class="fas fa-clipboard-list text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay trámites pendientes</h3>
                    <p class="text-gray-500">No se encontraron trámites que requieran revisión.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush 