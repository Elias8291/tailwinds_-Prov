@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-3">
                    <i class="fas fa-edit text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Actualización de Datos</h1>
                    <p class="text-gray-600">Seleccione qué información desea actualizar</p>
                </div>
            </div>
            
            <!-- Info del Proveedor -->
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-green-800">
                            Proveedor Activo: {{ $proveedor->pv }} 
                            <span class="text-sm font-normal">({{ $tramiteAprobado->solicitante->tipo_persona }})</span>
                        </h3>
                        <p class="text-green-700 text-sm">Su registro permanecerá activo durante la actualización</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secciones para actualizar -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- 1. Datos Generales (Siempre disponible) -->
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-100 rounded-lg p-2">
                            <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                            <p class="text-sm text-gray-600">Razón social, giro, actividades</p>
                        </div>
                    </div>
                    
                    @if(isset($datosActuales['datos_generales']))
                        <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs">
                            <strong>Actual:</strong><br>
                            <span class="text-gray-700">{{ $datosActuales['datos_generales']['razon_social'] ?? 'N/A' }}</span><br>
                            <span class="text-gray-600">{{ $datosActuales['datos_generales']['giro'] ?? 'N/A' }}</span>
                        </div>
                    @endif
                    
                    <form action="{{ route('tramites.actualizacion.seccion', 1) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                            Actualizar Datos Generales
                        </button>
                    </form>
                </div>
            </div>

            <!-- 2. Domicilio (Siempre disponible) -->
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-green-100 rounded-lg p-2">
                            <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Domicilio</h3>
                            <p class="text-sm text-gray-600">Dirección fiscal</p>
                        </div>
                    </div>
                    
                    @if(isset($datosActuales['domicilio']))
                        <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs">
                            <strong>Actual:</strong><br>
                            <span class="text-gray-700">{{ $datosActuales['domicilio']['estado_nombre'] ?? 'N/A' }}</span><br>
                            <span class="text-gray-600">{{ $datosActuales['domicilio']['municipio_nombre'] ?? 'N/A' }}</span>
                        </div>
                    @endif
                    
                    <form action="{{ route('tramites.actualizacion.seccion', 2) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                            Actualizar Domicilio
                        </button>
                    </form>
                </div>
            </div>

            <!-- 3. Constitución (Solo Persona Moral) -->
            @if($tramiteAprobado->solicitante->tipo_persona === 'Moral')
                <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-purple-100 rounded-lg p-2">
                                <i class="fas fa-building text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Constitución</h3>
                                <p class="text-sm text-gray-600">Datos notariales</p>
                            </div>
                        </div>
                        
                        @if(isset($datosActuales['constitucion']))
                            <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs">
                                <strong>Actual:</strong><br>
                                <span class="text-gray-700">Notario: {{ $datosActuales['constitucion']['numero_notario'] ?? 'N/A' }}</span><br>
                                <span class="text-gray-600">Fecha: {{ $datosActuales['constitucion']['fecha_constitucion'] ?? 'N/A' }}</span>
                            </div>
                        @endif
                        
                        <form action="{{ route('tramites.actualizacion.seccion', 3) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Constitución
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 4. Accionistas (Solo Persona Moral) -->
                <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2">
                                <i class="fas fa-users text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Accionistas</h3>
                                <p class="text-sm text-gray-600">Socios y participaciones</p>
                            </div>
                        </div>
                        
                        @if(isset($datosActuales['accionistas']) && count($datosActuales['accionistas']) > 0)
                            <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs">
                                <strong>Actual:</strong><br>
                                <span class="text-gray-700">{{ count($datosActuales['accionistas']) }} accionista(s) registrado(s)</span>
                            </div>
                        @endif
                        
                        <form action="{{ route('tramites.actualizacion.seccion', 4) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Accionistas
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 5. Apoderado Legal (Solo Persona Moral) -->
                <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-red-100 rounded-lg p-2">
                                <i class="fas fa-user-tie text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Apoderado Legal</h3>
                                <p class="text-sm text-gray-600">Representante legal</p>
                            </div>
                        </div>
                        
                        @if(isset($datosActuales['apoderado']))
                            <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs">
                                <strong>Actual:</strong><br>
                                <span class="text-gray-700">{{ $datosActuales['apoderado']['nombre'] ?? 'N/A' }}</span><br>
                                <span class="text-gray-600">{{ $datosActuales['apoderado']['cargo'] ?? 'N/A' }}</span>
                            </div>
                        @endif
                        
                        <form action="{{ route('tramites.actualizacion.seccion', 5) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Apoderado
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- 6. Documentos (Siempre disponible) -->
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-yellow-100 rounded-lg p-2">
                            <i class="fas fa-file-alt text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Documentos</h3>
                            <p class="text-sm text-gray-600">Archivos y comprobantes</p>
                        </div>
                    </div>
                    
                    @if(isset($datosActuales['documentos']) && count($datosActuales['documentos']) > 0)
                        <div class="bg-gray-50 rounded-lg p-3 mb-3 text-xs">
                            <strong>Actual:</strong><br>
                            <span class="text-gray-700">{{ count($datosActuales['documentos']) }} documento(s) registrado(s)</span>
                        </div>
                    @endif
                    
                    <form action="{{ route('tramites.actualizacion.seccion', $tramiteAprobado->solicitante->tipo_persona === 'Moral' ? 6 : 3) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg transition-colors">
                            Actualizar Documentos
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Botón de cancelar -->
        <div class="text-center mt-8">
            <a href="{{ route('tramites.solicitante.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
                Cancelar y volver
            </a>
        </div>
    </div>
</div>
@endsection 