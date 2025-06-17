@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-white/10 text-white shadow-md">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            Detalles del Proveedor
                        </h1>
                        <p class="text-sm text-white/80 mt-1">Información completa del proveedor</p>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('proveedores.index') }}" 
                       class="px-4 py-2 text-sm bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver
                    </a>
                    @can('proveedores.edit')
                    <a href="{{ route('proveedores.edit', $proveedor) }}" 
                       class="px-4 py-2 text-sm bg-white text-[#9d2449] rounded-lg hover:bg-gray-100 transition-colors duration-200 flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Editar
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Información Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Datos Generales -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-[#9d2449] mr-2"></i>
                            Datos Generales
                        </h2>
                        @if($proveedor->estado === 'Activo')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Activo
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Inactivo
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Razón Social</label>
                            <p class="text-gray-900">{{ $proveedor->razon_social }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">RFC</label>
                            <p class="text-gray-900">{{ $proveedor->rfc }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Persona</label>
                            <p class="text-gray-900">{{ $proveedor->tipo_persona }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Giro Empresarial</label>
                            <p class="text-gray-900">{{ $proveedor->giro_empresarial }}</p>
                        </div>
                    </div>
                </div>

                <!-- Domicilio -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
                        <i class="fas fa-map-marker-alt text-[#9d2449] mr-2"></i>
                        Domicilio
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Calle y Número</label>
                            <p class="text-gray-900">{{ $proveedor->calle_numero }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Colonia</label>
                            <p class="text-gray-900">{{ $proveedor->colonia }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Código Postal</label>
                            <p class="text-gray-900">{{ $proveedor->codigo_postal }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Municipio</label>
                            <p class="text-gray-900">{{ $proveedor->municipio }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                            <p class="text-gray-900">{{ $proveedor->estado }}</p>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
                        <i class="fas fa-address-book text-[#9d2449] mr-2"></i>
                        Información de Contacto
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Teléfono</label>
                            <p class="text-gray-900">{{ $proveedor->telefono }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Correo Electrónico</label>
                            <p class="text-gray-900">{{ $proveedor->correo_electronico }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Sitio Web</label>
                            <p class="text-gray-900">{{ $proveedor->sitio_web ?? 'No especificado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Resumen -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
                        <i class="fas fa-chart-pie text-[#9d2449] mr-2"></i>
                        Resumen
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Trámites Totales</span>
                            <span class="text-lg font-semibold text-[#9d2449]">{{ $proveedor->tramites->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Trámites Activos</span>
                            <span class="text-lg font-semibold text-green-600">
                                {{ $proveedor->tramites->where('estado', 'Activo')->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Última Actualización</span>
                            <span class="text-sm text-gray-600">
                                {{ $proveedor->updated_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
                        <i class="fas fa-file-alt text-[#9d2449] mr-2"></i>
                        Documentos
                    </h2>
                    
                    <div class="space-y-4">
                        @forelse($proveedor->documentos as $documento)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ $documento->nombre }}</span>
                                </div>
                                <a href="{{ Storage::url($documento->ruta) }}" 
                                   class="text-[#9d2449] hover:text-[#7a1d37] transition-colors duration-200"
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center">No hay documentos disponibles</p>
                        @endforelse
                    </div>
                </div>

                <!-- Historial de Cambios -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center mb-6">
                        <i class="fas fa-history text-[#9d2449] mr-2"></i>
                        Historial de Cambios
                    </h2>
                    
                    <div class="space-y-4">
                        @forelse($proveedor->historial as $cambio)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-[#9d2449]/10 flex items-center justify-center">
                                        <i class="fas fa-edit text-[#9d2449]"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-900">{{ $cambio->descripcion }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $cambio->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center">No hay cambios registrados</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush 