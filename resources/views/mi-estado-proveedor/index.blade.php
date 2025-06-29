@extends('layouts.app')

@section('title', 'Mi Estado')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Mi Estado como Proveedor</h1>
            <p class="text-gray-600 mt-2">Consulte el estado de su inscripción y información general</p>
        </div>
        <div class="flex space-x-4">
            @if($proveedor)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($estadoInscripcion === 'Activa') bg-green-100 text-green-800
                    @elseif($estadoInscripcion === 'Por vencer') bg-yellow-100 text-yellow-800
                    @elseif($estadoInscripcion === 'Vencida') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $estadoInscripcion }}
                </span>
            @endif
        </div>
    </div>

    @if(!$proveedor)
        <!-- Sin inscripción -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sin Inscripción como Proveedor</h3>
            <p class="text-gray-500 mb-6">No se encontró una inscripción activa como proveedor en el sistema.</p>
            <a href="{{ route('tramites.solicitante.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark transition-colors">
                Ver mis trámites
            </a>
        </div>
    @else
        <!-- Alertas -->
        @if(!empty($alertas))
            <div class="mb-6 space-y-3">
                @foreach($alertas as $alerta)
                    <div class="flex items-center p-4 rounded-lg border
                        @if($alerta['tipo'] === 'danger') bg-red-50 border-red-200 text-red-800
                        @elseif($alerta['tipo'] === 'warning') bg-yellow-50 border-yellow-200 text-yellow-800
                        @else bg-blue-50 border-blue-200 text-blue-800 @endif">
                        <svg class="flex-shrink-0 h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            @if($alerta['tipo'] === 'danger')
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            @elseif($alerta['tipo'] === 'warning')
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                        <div class="flex-1">
                            {{ $alerta['mensaje'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Cards principales -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Estado General -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Estado General</h3>
                    <div class="h-10 w-10 rounded-full flex items-center justify-center
                        @if($estadoInscripcion === 'Activa') bg-green-100
                        @elseif($estadoInscripcion === 'Por vencer') bg-yellow-100
                        @elseif($estadoInscripcion === 'Vencida') bg-red-100
                        @else bg-gray-100 @endif">
                        <svg class="h-5 w-5 
                            @if($estadoInscripcion === 'Activa') text-green-600
                            @elseif($estadoInscripcion === 'Por vencer') text-yellow-600
                            @elseif($estadoInscripcion === 'Vencida') text-red-600
                            @else text-gray-600 @endif" fill="currentColor" viewBox="0 0 20 20">
                            @if($estadoInscripcion === 'Activa')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Número PV:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $proveedor->pv }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Estado:</span>
                        <span class="text-sm font-medium">{{ $proveedor->estado }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Fecha de registro:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $proveedor->fecha_registro ? $proveedor->fecha_registro->format('d/m/Y') : 'No definida' }}</span>
                    </div>
                </div>
            </div>

            <!-- Información de Vencimiento -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vencimiento</h3>
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Fecha de vencimiento:</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $fechaVencimiento ? $fechaVencimiento->format('d/m/Y') : 'No definida' }}
                        </span>
                    </div>
                    @if($diasRestantes !== null)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Días restantes:</span>
                            <span class="text-sm font-medium 
                                @if($diasRestantes < 0) text-red-600
                                @elseif($diasRestantes <= 30) text-yellow-600
                                @else text-green-600 @endif">
                                {{ $diasRestantes >= 0 ? $diasRestantes : 'Vencido hace ' . abs($diasRestantes) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resumen de Actividad -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Actividad</h3>
                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Trámites activos:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $tramitesActivos->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Último trámite:</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $tramitesActivos->first() ? $tramitesActivos->first()->created_at->format('d/m/Y') : 'Sin trámites' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trámites activos -->
        @if($tramitesActivos->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Trámites Activos</h3>
                    <p class="text-sm text-gray-600 mt-1">Sus trámites actualmente en proceso</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($tramitesActivos as $tramite)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-xs font-medium text-primary">
                                                    {{ $tramite->id }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $tramite->detalleTramite ? $tramite->detalleTramite->tipo_tramite : 'Tipo no especificado' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Iniciado el {{ $tramite->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($tramite->estado === 'en_proceso') bg-blue-100 text-blue-800
                                        @elseif($tramite->estado === 'en_revision') bg-yellow-100 text-yellow-800
                                        @elseif($tramite->estado === 'pendiente') bg-gray-100 text-gray-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $tramite->estado)) }}
                                    </span>
                                    <a href="{{ route('tramites.solicitante.estado', $tramite) }}" class="text-primary hover:text-primary-dark text-sm font-medium">
                                        Ver detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($tramitesActivos->count() >= 5)
                    <div class="px-6 py-4 bg-gray-50 text-center">
                        <a href="{{ route('tramites.solicitante.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium">
                            Ver todos mis trámites
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Información adicional -->
        @if($proveedor->observaciones)
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Observaciones</h3>
                <p class="text-sm text-gray-600">{{ $proveedor->observaciones }}</p>
            </div>
        @endif
    @endif
</div>
@endsection 