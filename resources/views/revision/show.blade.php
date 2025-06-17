@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#7a1d37] text-white shadow-md">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Revisión de Trámite #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ ucfirst($tramite->tipo_tramite) }} - 
                            {{ $tramite->solicitante->tipo_persona === 'Moral' ? 'Persona Moral' : 'Persona Física' }}
                        </p>
                    </div>
                </div>
                
                <!-- Estado del trámite -->
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                           ($tramite->estado == 'En Revision' ? 'bg-blue-100 text-blue-800' : 
                           ($tramite->estado == 'Aprobado' ? 'bg-green-100 text-green-800' : 
                           ($tramite->estado == 'Rechazado' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800'))) }}">
                        <i class="fas {{ $tramite->estado == 'Pendiente' ? 'fa-clock' : 
                                       ($tramite->estado == 'En Revision' ? 'fa-spinner fa-spin' : 
                                       ($tramite->estado == 'Aprobado' ? 'fa-check' : 
                                       ($tramite->estado == 'Rechazado' ? 'fa-times' : 'fa-question'))) }} mr-2"></i>
                        {{ $tramite->estado }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Secciones de Revisión -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @php
                $secciones = [
                    1 => [
                        'icon' => 'fa-user',
                        'label' => 'Datos Generales',
                        'color' => 'from-blue-500 to-blue-600',
                        'fields' => [
                            'nombre_completo' => 'Nombre Completo',
                            'rfc' => 'RFC',
                            'curp' => 'CURP',
                            'email' => 'Correo Electrónico',
                            'telefono' => 'Teléfono'
                        ]
                    ],
                    2 => [
                        'icon' => 'fa-home',
                        'label' => 'Domicilio',
                        'color' => 'from-green-500 to-green-600',
                        'fields' => [
                            'calle' => 'Calle',
                            'numero_exterior' => 'Número Exterior',
                            'numero_interior' => 'Número Interior',
                            'colonia' => 'Colonia',
                            'codigo_postal' => 'Código Postal',
                            'municipio' => 'Municipio',
                            'estado' => 'Estado'
                        ]
                    ],
                    3 => [
                        'icon' => 'fa-building',
                        'label' => 'Constitución',
                        'color' => 'from-purple-500 to-purple-600',
                        'fields' => [
                            'razon_social' => 'Razón Social',
                            'fecha_constitucion' => 'Fecha de Constitución',
                            'numero_acta' => 'Número de Acta',
                            'notario' => 'Notario',
                            'numero_notario' => 'Número de Notario'
                        ]
                    ],
                    4 => [
                        'icon' => 'fa-users',
                        'label' => 'Accionistas',
                        'color' => 'from-amber-500 to-amber-600',
                        'fields' => [
                            'accionistas' => 'Lista de Accionistas'
                        ]
                    ],
                    5 => [
                        'icon' => 'fa-user-tie',
                        'label' => 'Apoderado Legal',
                        'color' => 'from-red-500 to-red-600',
                        'fields' => [
                            'nombre_apoderado' => 'Nombre del Apoderado',
                            'rfc_apoderado' => 'RFC del Apoderado',
                            'poder_notarial' => 'Poder Notarial'
                        ]
                    ],
                    6 => [
                        'icon' => 'fa-file-alt',
                        'label' => 'Documentos',
                        'color' => 'from-indigo-500 to-indigo-600',
                        'fields' => [
                            'documentos' => 'Documentos Requeridos'
                        ]
                    ]
                ];

                $tipoPersona = strtolower($tramite->solicitante->tipo_persona ?? 'fisica');
                $seccionesVisibles = $tipoPersona === 'moral' ? [1,2,3,4,5,6] : [1,2,6];
            @endphp

            @foreach($seccionesVisibles as $seccionId)
                @php
                    $seccion = $secciones[$seccionId];
                    $revision = $tramite->seccionesRevision->where('seccion_id', $seccionId)->first();
                    $estado = $revision ? $revision->estado : 'pendiente';
                    $color = $estado === 'aprobado' ? 'bg-green-100 text-green-800' : 
                           ($estado === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800');
                @endphp
                <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-sm p-6 border border-gray-100">
                    <!-- Encabezado de la sección -->
                    <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-gradient-to-br {{ $seccion['color'] }} text-white shadow-sm">
                                <i class="fas {{ $seccion['icon'] }}"></i>
                            </div>
                                        <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $seccion['label'] }}</h3>
                                <p class="text-sm text-gray-500">Revisión de información y documentos</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                            <i class="fas {{ $estado === 'aprobado' ? 'fa-check' : ($estado === 'rechazado' ? 'fa-times' : 'fa-clock') }} mr-1"></i>
                            {{ ucfirst($estado) }}
                        </span>
                </div>
                
                    <!-- Contenido de la sección -->
                    <div class="space-y-4">
                        @if($seccionId === 6)
                            <!-- Documentos -->
                            <div class="space-y-3">
                                @foreach($tramite->documentos as $documento)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                                <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $documento->nombre }}</p>
                                                <p class="text-xs text-gray-500">{{ $documento->seccion->nombre }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ Storage::url($documento->ruta) }}" 
                                               target="_blank"
                                               class="p-1.5 text-gray-600 hover:text-[#9d2449] transition-colors duration-200"
                                               title="Ver documento">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ Storage::url($documento->ruta) }}" 
                                               download
                                               class="p-1.5 text-gray-600 hover:text-[#9d2449] transition-colors duration-200"
                                               title="Descargar documento">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Campos de la sección -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($seccion['fields'] as $field => $label)
                                    @if($field === 'accionistas' && $seccionId === 4)
                                        <!-- Tabla de Accionistas -->
                                        <div class="col-span-2">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $label }}</h4>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">RFC</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Porcentaje</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach($tramite->solicitante->accionistas as $accionista)
                                                            <tr>
                                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $accionista->nombre }}</td>
                                                                <td class="px-3 py-2 text-sm text-gray-500">{{ $accionista->rfc }}</td>
                                                                <td class="px-3 py-2 text-sm text-gray-500">{{ $accionista->porcentaje }}%</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                                <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                                            <div class="text-sm text-gray-900">
                                                {{ $tramite->solicitante->$field ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <!-- Campo de revisión presencial -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    <span class="text-sm font-medium text-gray-700">Revisión Presencial</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" 
                                            class="px-3 py-1.5 text-sm bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-check mr-1.5"></i>
                                        Aprobar
                                    </button>
                                    <button type="button" 
                                            class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-times mr-1.5"></i>
                                        Rechazar
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <textarea class="w-full text-sm rounded-lg border-gray-200 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20" 
                                          rows="2" 
                                          placeholder="Observaciones de la revisión presencial..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush