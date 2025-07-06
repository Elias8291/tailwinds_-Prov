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

        <!-- Sección de Documentos para Cotejo -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div x-data="{ 
                isOpen: true,
                estado: '{{ $revisionesExistentes[6]['estado'] ?? 'pendiente' }}'
            }">
                <!-- Encabezado -->
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-bold text-gray-800">Cotejo de Documentos</h2>
                        </div>
                        @if(isset($revisionesExistentes[6]))
                            <span id="estado_seccion_6" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2"
                                :class="{
                                    'bg-green-100 text-green-800': estado === 'aprobado',
                                    'bg-red-100 text-red-800': estado === 'rechazado',
                                    'bg-yellow-100 text-yellow-800': estado === 'pendiente'
                                }">
                                <i class="fas fa-circle text-xs"></i>
                                <span x-text="estado.charAt(0).toUpperCase() + estado.slice(1)"></span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-6 pt-0">
                    <!-- Contenedor de la sección de documentos -->
                    <div class="space-y-6">
                        @include('components.formularios.seccion-documentos', [
                            'title' => 'Documentos para Cotejo',
                            'tramite' => $tramite,
                            'mostrar_navegacion' => false,
                            'documentos' => collect($documentos ?? [])->map(function($doc) {
                                return [
                                    'id' => $doc['id'] ?? null,
                                    'nombre' => $doc['nombre'] ?? 'Documento sin nombre',
                                    'descripcion' => $doc['descripcion'] ?? 'Sin descripción',
                                    'estado' => $doc['estado'] ?? 'Pendiente',
                                    'ruta_archivo' => $doc['ruta_archivo'] ?? null,
                                    'fecha_subida' => $doc['fecha_entrega'] ?? null,
                                    'observaciones' => $doc['observaciones'] ?? null,
                                    'nombre_original' => $doc['ruta_archivo'] ? basename($doc['ruta_archivo']) : 'archivo.pdf',
                                    'documento_id' => $doc['documento_id'] ?? null,
                                    'validacion_ia' => $doc['validacion_ia'] ?? null,
                                    'documento_cotejado' => $doc['documento_cotejado'] ?? false,
                                    'comentario_revision' => $doc['comentario_revision'] ?? null
                                ];
                            })->toArray(),
                            'readonly' => true,
                            'modo_cotejo' => true
                        ])
                    </div>

                    <!-- Sección de revisión -->
                    <x-revision.seccion-revision 
                        :seccionId="6"
                        :estado="$revisionesExistentes[6]['estado'] ?? 'pendiente'"
                        :observaciones="$revisionesExistentes[6]['observaciones'] ?? ''"
                        :tramiteId="$tramite->id"
                        :modo_cotejo="true"
                    />
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @endpush
@endsection 