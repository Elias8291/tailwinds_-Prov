@extends('layouts.app')

@section('content')
    <div class="max-w-[1800px] mx-auto px-8 py-6 space-y-6">
        <!-- Header Principal -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-all duration-300 border border-gray-100">
            <x-revision.header 
                :tramiteId="$tramite->id"
                :tipoTramite="$tramite->tipo_tramite"
                :rfc="$tramite->solicitante->rfc ?? ''"
            />
        </div>

        <!-- Contenedor de Secciones -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Sección de Datos Generales -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Datos Generales</h2>
                        @if(isset($revisionesExistentes[1]))
                            <span id="estado_seccion_1" class="px-3 py-1 text-sm rounded-full 
                                @if($revisionesExistentes[1]['estado'] === 'aprobado') bg-green-100 text-green-800
                                @elseif($revisionesExistentes[1]['estado'] === 'rechazado') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($revisionesExistentes[1]['estado']) }}
                            </span>
                        @endif
                    </div>
                    <x-formularios.seccion-datos-generales 
                        :datosTramite="$datosTramite"
                        :readonly="true"
                    />
                    <x-revision.seccion-revision 
                        :seccionId="1"
                        :estado="$revisionesExistentes[1]['estado'] ?? null"
                        :comentario="$revisionesExistentes[1]['comentario'] ?? null"
                    />
                </div>
            </div>

            <!-- Sección de Domicilio -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Domicilio</h2>
                        @if(isset($revisionesExistentes[2]))
                            <span id="estado_seccion_2" class="px-3 py-1 text-sm rounded-full 
                                @if($revisionesExistentes[2]['estado'] === 'aprobado') bg-green-100 text-green-800
                                @elseif($revisionesExistentes[2]['estado'] === 'rechazado') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($revisionesExistentes[2]['estado']) }}
                            </span>
                        @endif
                    </div>
                    <x-formularios.seccion-domicilio 
                        :datosDomicilio="$datosDomicilio"
                        :readonly="true"
                    />
                    <x-revision.seccion-revision 
                        :seccionId="2"
                        :estado="$revisionesExistentes[2]['estado'] ?? null"
                        :comentario="$revisionesExistentes[2]['comentario'] ?? null"
                    />
                </div>
            </div>

            @if($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral')
            <!-- Sección de Constitución (Solo para persona moral) -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Datos de Constitución</h2>
                        @if(isset($revisionesExistentes[3]))
                            <span id="estado_seccion_3" class="px-3 py-1 text-sm rounded-full 
                                @if($revisionesExistentes[3]['estado'] === 'aprobado') bg-green-100 text-green-800
                                @elseif($revisionesExistentes[3]['estado'] === 'rechazado') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($revisionesExistentes[3]['estado']) }}
                            </span>
                        @endif
                    </div>
                    <x-formularios.seccion-constitucion 
                        :datosConstitucion="$datosConstitucion"
                        :readonly="true"
                    />
                    <x-revision.seccion-revision 
                        :seccionId="3"
                        :estado="$revisionesExistentes[3]['estado'] ?? null"
                        :comentario="$revisionesExistentes[3]['comentario'] ?? null"
                    />
                </div>
            </div>

            <!-- Sección de Accionistas (Solo para persona moral) -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Accionistas</h2>
                        @if(isset($revisionesExistentes[4]))
                            <span id="estado_seccion_4" class="px-3 py-1 text-sm rounded-full 
                                @if($revisionesExistentes[4]['estado'] === 'aprobado') bg-green-100 text-green-800
                                @elseif($revisionesExistentes[4]['estado'] === 'rechazado') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($revisionesExistentes[4]['estado']) }}
                            </span>
                        @endif
                    </div>
                    <x-formularios.seccion-accionistas 
                        :datosAccionistas="$datosAccionistas"
                        :readonly="true"
                    />
                    <x-revision.seccion-revision 
                        :seccionId="4"
                        :estado="$revisionesExistentes[4]['estado'] ?? null"
                        :comentario="$revisionesExistentes[4]['comentario'] ?? null"
                    />
                </div>
            </div>

            <!-- Sección de Apoderado Legal (Solo para persona moral) -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Apoderado Legal</h2>
                        @if(isset($revisionesExistentes[5]))
                            <span id="estado_seccion_5" class="px-3 py-1 text-sm rounded-full 
                                @if($revisionesExistentes[5]['estado'] === 'aprobado') bg-green-100 text-green-800
                                @elseif($revisionesExistentes[5]['estado'] === 'rechazado') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($revisionesExistentes[5]['estado']) }}
                            </span>
                        @endif
                    </div>
                    <x-formularios.seccion-apoderado 
                        :datosApoderado="$datosApoderado"
                        :readonly="true"
                    />
                    <x-revision.seccion-revision 
                        :seccionId="5"
                        :estado="$revisionesExistentes[5]['estado'] ?? null"
                        :comentario="$revisionesExistentes[5]['comentario'] ?? null"
                    />
                </div>
            </div>
            @endif

            <!-- Sección de Documentos -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Documentos</h2>
                        @if(isset($revisionesExistentes[6]))
                            <span id="estado_seccion_6" class="px-3 py-1 text-sm rounded-full 
                                @if($revisionesExistentes[6]['estado'] === 'aprobado') bg-green-100 text-green-800
                                @elseif($revisionesExistentes[6]['estado'] === 'rechazado') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($revisionesExistentes[6]['estado']) }}
                            </span>
                        @endif
                    </div>
                    <x-formularios.seccion-documentos 
                        :documentos="$documentos"
                        :documentosPorSeccion="$documentosPorSeccion"
                        :readonly="true"
                    />
                    <x-revision.seccion-revision 
                        :seccionId="6"
                        :estado="$revisionesExistentes[6]['estado'] ?? null"
                        :comentario="$revisionesExistentes[6]['comentario'] ?? null"
                    />
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @endpush
@endsection