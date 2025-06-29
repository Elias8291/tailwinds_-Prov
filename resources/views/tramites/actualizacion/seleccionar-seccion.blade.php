@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-3 shadow-md">
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
                        <h3 class="font-semibold text-green-800">Proveedor Activo: {{ $proveedor->pv }}</h3>
                        <p class="text-green-700 text-sm">Su registro de proveedor permanecerá activo durante la actualización</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secciones Disponibles -->
        <div class="grid md:grid-cols-2 gap-6">
            @if($tramiteAprobado->solicitante->tipo_persona === 'Moral')
                <!-- Datos Generales -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 rounded-lg p-2">
                                    <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                                    <p class="text-sm text-gray-600">Razón social, giro, actividades</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vista previa de datos actuales -->
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Razón Social:</span>
                                <span class="font-medium">{{ $datosActuales['datos_generales']['razon_social'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Giro:</span>
                                <span class="font-medium">{{ $datosActuales['datos_generales']['giro'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('tramites.actualizacion.iniciar-seccion', ['seccion' => 1]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Datos Generales
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Domicilio -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 rounded-lg p-2">
                                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Domicilio</h3>
                                    <p class="text-sm text-gray-600">Dirección fiscal</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estado:</span>
                                <span class="font-medium">{{ $datosActuales['domicilio']['estado_nombre'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Municipio:</span>
                                <span class="font-medium">{{ $datosActuales['domicilio']['municipio_nombre'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('tramites.actualizacion.iniciar-seccion', ['seccion' => 2]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Domicilio
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Constitución -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-100 rounded-lg p-2">
                                    <i class="fas fa-building text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Constitución</h3>
                                    <p class="text-sm text-gray-600">Datos constitutivos</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Notario:</span>
                                <span class="font-medium">{{ $datosActuales['constitucion']['numero_notario'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('tramites.actualizacion.iniciar-seccion', ['seccion' => 3]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Constitución
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Accionistas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-100 rounded-lg p-2">
                                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Accionistas</h3>
                                    <p class="text-sm text-gray-600">Estructura accionaria</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Accionistas:</span>
                                <span class="font-medium">{{ count($datosActuales['accionistas'] ?? []) }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('tramites.actualizacion.iniciar-seccion', ['seccion' => 4]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Accionistas
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Apoderado Legal -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 rounded-lg p-2">
                                    <i class="fas fa-user-tie text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Apoderado Legal</h3>
                                    <p class="text-sm text-gray-600">Representante legal</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nombre:</span>
                                <span class="font-medium">{{ $datosActuales['apoderado']['nombre'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('tramites.actualizacion.iniciar-seccion', ['seccion' => 5]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Apoderado
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-red-100 rounded-lg p-2">
                                    <i class="fas fa-folder text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Documentos</h3>
                                    <p class="text-sm text-gray-600">Documentación soporte</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Documentos:</span>
                                <span class="font-medium">{{ count($datosActuales['documentos'] ?? []) }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('tramites.actualizacion.iniciar-seccion', ['seccion' => 6]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tramite_base_id" value="{{ $tramiteAprobado->id }}">
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors">
                                Actualizar Documentos
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Para Persona Física solo hay 3 secciones -->
                <!-- Similar pero solo: Datos Generales, Domicilio, Documentos -->
            @endif
        </div>

        <!-- Botones de acción -->
        <div class="mt-8 flex justify-between">
            <a href="{{ route('tramites.solicitante.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg transition-colors">
                Cancelar
            </a>
            
            <div class="text-sm text-gray-600">
                💡 <strong>Tip:</strong> Solo la sección que modifique será enviada a revisión
            </div>
        </div>
    </div>
</div>
@endsection 