@extends('layouts.app')

@section('content')
<div class="bg-gray-50/50 min-h-screen" 
     x-data="cotejoFlowManager({{ $tramite->id }})">
    <div class="max-w-screen-xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <!-- Header Principal Rediseñado -->
        <div class="relative bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 p-6 mb-8">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-gradient-to-br from-[#9d2449]/5 to-transparent rounded-full opacity-50"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-gradient-to-tr from-[#9d2449]/5 to-transparent rounded-full opacity-50"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-xl flex items-center justify-center shadow-md ring-2 ring-white">
                            <i class="fas fa-search-plus text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Cotejo de Documentos Físicos</h1>
                            <p class="text-sm text-gray-500">Verificación presencial - {{ ucfirst($tramite->tipo_tramite) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex-shrink-0">
                        <span class="inline-flex items-center bg-[#9d2449]/10 text-[#9d2449] text-sm px-4 py-2 rounded-full font-bold">
                            <span class="w-2 h-2 bg-[#9d2449] rounded-full mr-2 animate-pulse"></span>
                            En cotejo
                        </span>
                    </div>
                </div>
                
                <div class="mt-6 border-t border-gray-200/80 pt-4 flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-2 sm:space-y-0 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-hashtag text-gray-400 w-5 text-center mr-2"></i>
                        <strong>Folio:</strong><span class="ml-2 font-mono">#{{ $tramite->id }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user-tie text-gray-400 w-5 text-center mr-2"></i>
                        <strong>Solicitante:</strong><span class="ml-2 truncate">{{ $tramite->solicitante->nombre_completo ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-id-card-alt text-gray-400 w-5 text-center mr-2"></i>
                        <strong>RFC:</strong><span class="ml-2 truncate">{{ $tramite->solicitante->rfc ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout Principal -->
        <div class="space-y-5">
                @php
                    $documentosParaCotejo = collect($documentos ?? [])->map(function($doc) {
                                return [
                                    'id' => $doc['id'] ?? null,
                                    'nombre' => $doc['nombre'] ?? 'Documento sin nombre',
                                    'estado' => $doc['estado'] ?? 'Pendiente',
                                    'ruta_archivo' => $doc['ruta_archivo'] ?? null,
                                    'fecha_subida' => $doc['fecha_entrega'] ?? null,
                                    'documento_cotejado' => $doc['documento_cotejado'] ?? false,
                                'comentario_revision' => $doc['comentario_revision'] ?? null,
                                'descripcion' => $doc['descripcion'] ?? 'No hay descripción disponible.'
                                ];
                    })->toArray();
                @endphp

                @forelse ($documentosParaCotejo as $documento)
                    @php
                        $estado = $documento['estado'] ?? 'Pendiente';
                        $hasFile = !empty($documento['ruta_archivo']);
                        $isCotejado = $documento['documento_cotejado'] ?? false;

                        $cardClasses = match(true) {
                            $estado === 'Aprobado' && $isCotejado => 'bg-green-50/70 border-green-400',
                            $estado === 'Rechazado' => 'bg-red-50/70 border-red-400',
                            $isCotejado => 'bg-rose-50/70 border-rose-400',
                            default => 'bg-white border-gray-300',
                        };
                        $iconContainerClasses = match(true) {
                            $estado === 'Aprobado' && $isCotejado => 'bg-green-600 text-white',
                            $estado === 'Rechazado' => 'bg-red-600 text-white',
                            $isCotejado => 'bg-rose-600 text-white',
                            default => 'bg-slate-500 text-white',
                        };
                         $badgeClasses = match(true) {
                            $estado === 'Aprobado' && $isCotejado => 'bg-green-100 text-green-800',
                            $estado === 'Rechazado' => 'bg-red-100 text-red-700',
                            $isCotejado => 'bg-rose-100 text-rose-800',
                            default => 'bg-slate-100 text-slate-700',
                        };
                        $estadoTexto = match(true) {
                            $estado === 'Aprobado' && $isCotejado => 'Aprobado',
                            $estado === 'Rechazado' => 'Rechazado',
                            $isCotejado => 'Cotejado',
                            default => 'Pendiente',
                        };
                    @endphp
                    <div x-data="cotejoDocumento({
                            documentoId: {{ $documento['id'] }},
                            tramiteId: {{ $tramite->id }},
                            comentarioInicial: '{{ e($documento['comentario_revision']) }}',
                            esCotejado: {{ $documento['documento_cotejado'] ? 'true' : 'false' }},
                            estadoInicial: '{{ $documento['estado'] }}'
                        })"
                         class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border documento-item"
                         :data-id="{{ $documento['id'] }}"
                         :data-nombre="'{{ e($documento['nombre']) }}'"
                         :data-estado="estado"
                         :data-cotejado="isCotejado"
                         :class="isCotejado ? (estado === 'Aprobado' ? 'bg-green-50/70 border-green-400' : 'bg-red-50/70 border-red-400') : 'bg-white border-gray-300'">
                        
                    <!-- Cabecera del Documento (siempre visible) -->
                    <div class="p-4 flex items-center gap-4">
                            <div class="relative flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg" :class="iconContainerClasses">
                                <i class="fas fa-file-alt text-xl"></i>
                                <template x-if="isCotejado">
                                    <div class="absolute -top-1.5 -right-1.5 h-5 w-5 bg-white rounded-full flex items-center justify-center shadow">
                                    <i class="fas text-lg" :class="estado === 'Aprobado' ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'"></i>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="font-bold text-gray-800 truncate" title="{{ $documento['nombre'] }}">
                                    {{ $documento['nombre'] }}
                                </p>
                            <p class="text-xs text-slate-500 mt-1">
                                    @if($documento['fecha_subida'])
                                        Subido: {{ \Carbon\Carbon::parse($documento['fecha_subida'])->format('d/m/Y') }}
                                    @else
                                        Sin entregar
                                    @endif
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-3">
                             <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider" 
                                  :class="isCotejado ? (estado === 'Aprobado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') : 'bg-slate-100 text-slate-700'" 
                                  x-text="isCotejado ? estado : 'Pendiente'">
                            </span>
                                @if($hasFile)
                                <a href="{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => $documento['id']]) }}?inline=1" target="_blank" class="h-9 w-9 inline-flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-200/70 hover:text-slate-800 transition-all" title="Ver Documento">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                @endif
                            </div>
                    </div>

                    <!-- Vista Completa (antes de cotejar) -->
                    <div x-show="!isCotejado" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <!-- Requisitos del Documento -->
                        <div class="px-4 pb-4 border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-tasks mr-2 text-gray-400"></i>
                                Requisitos del Documento
                            </h4>
                            <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-200/80 whitespace-pre-wrap">{{ $documento['descripcion'] }}</div>
                                    </div>

                        <!-- Panel de Acciones de Revisión -->
                        <div class="border-t border-gray-200 bg-gray-100/60 p-4">
                            <div class="space-y-4">
                                <div>
                                    <label for="comentario_{{ $documento['id'] }}" class="block text-sm font-semibold text-gray-700 mb-1">
                                        <i class="fas fa-edit mr-1"></i> Observaciones
                                    </label>
                                    <textarea x-model="comentario" id="comentario_{{ $documento['id'] }}" rows="2" class="w-full p-2 text-sm bg-white border-gray-300 rounded-lg shadow-sm focus:ring-[#9d2449] focus:border-[#9d2449] transition" placeholder="Añadir observaciones (obligatorio si se rechaza)..."></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <button @click="rechazar" :disabled="loading" class="group w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-bold rounded-lg text-gray-700 bg-white hover:bg-red-50 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 disabled:opacity-50">
                                            <i class="fas fa-times-circle mr-2 text-red-500 group-hover:text-red-600 transition-colors"></i>
                                        <span x-show="!loading">Rechazar</span>
                                        <span x-show="loading" class="animate-pulse">...</span>
                                        </button>
                                    <button @click="aprobar" :disabled="loading" class="group w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600 transition-all duration-150 disabled:opacity-50">
                                            <i class="fas fa-check-circle mr-2"></i>
                                        <span x-show="!loading">Aprobar</span>
                                        <span x-show="loading" class="animate-pulse">...</span>
                                        </button>
                                    </div>
                </div>
            </div>
        </div>
                    
                    <!-- Vista Minimizada (cuando ya está cotejado) -->
                    <div x-show="isCotejado" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                        <div class="p-4 bg-gray-50 border-t border-gray-200/80">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <i class="fas text-2xl" :class="{ 'fa-check-circle text-green-500': estado === 'Aprobado', 'fa-times-circle text-red-500': estado === 'Rechazado' }"></i>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-800" x-text="`Decisión: ${estado}`"></p>
                                        <p class="text-xs text-gray-500 italic truncate" x-show="comentario" :title="comentario" x-text="`Obs: ${comentario}`"></p>
                                    </div>
                                </div>
                                <button @click="isCotejado = false" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex-shrink-0">
                                    Editar
                                </button>
                </div>
            </div>
        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm border">
                        <div class="mx-auto h-16 w-16 text-gray-400 mb-4 flex items-center justify-center bg-gray-100 rounded-full">
                            <i class="fas fa-folder-open text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">Sin Documentos</h3>
                        <p class="mt-2 text-sm text-gray-500">No hay documentos asociados a este trámite para cotejar.</p>
                    </div>
                @endforelse
            </div>

        <!-- Botón de Finalización Estático (al final del contenido) -->
        <div class="mt-12 flex justify-center">
            <button @click="finalizar" 
                    :disabled="loading"
                    class="w-full max-w-lg inline-flex items-center justify-center px-8 py-3 bg-primary hover:bg-primary-dark text-white text-base font-bold rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-primary/50"
                    :class="{'opacity-50 cursor-not-allowed': loading}">
                <i class="fas mr-3 text-lg" :class="loading ? 'fa-spinner fa-spin' : 'fa-check-double'"></i>
                <span x-show="!loading">Finalizar Proceso de Cotejo</span>
                <span x-show="loading">Procesando...</span>
            </button>
        </div>

        <!-- Botón de Finalización (FAB) -->
        <div x-data="{ fabExpanded: false }"
            @mouseenter="fabExpanded = true"
            @mouseleave="fabExpanded = false"
            @click="loading ? null : finalizar()"
            class="fixed bottom-6 right-6 z-40 group"
            :class="loading ? 'cursor-not-allowed' : 'cursor-pointer'">
            <div class="flex items-center justify-center transition-all duration-300 ease-in-out">
                <div class="flex items-center justify-center h-14 w-14 rounded-full shadow-lg group-hover:shadow-2xl text-white transform transition-all duration-300 ease-in-out"
                    :class="{
                        'bg-primary group-hover:w-64': !loading, 
                        'bg-gray-400': loading,
                        'w-64': fabExpanded && !loading,
                        'w-14': !fabExpanded || loading
                    }">
                    
                    <i class="fas text-xl transition-opacity duration-200"
                        :class="{
                            'fa-check-double': !loading,
                            'fa-spinner fa-spin': loading,
                            'opacity-0': fabExpanded && !loading
                        }"></i>
                    
                    <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                            :class="{
                                'opacity-100': fabExpanded && !loading,
                                'opacity-0': !fabExpanded || loading
                            }">
                        <span class="text-base font-bold whitespace-nowrap">Finalizar Proceso</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de Acción Flotante (FAB) para Instrucciones -->
    <div x-data="{ fabExpanded: false }"
         @mouseenter="fabExpanded = true"
         @mouseleave="fabExpanded = false"
         @click="instructionsModalOpen = true"
         class="fixed bottom-24 right-6 z-40 group cursor-pointer">
        <div class="flex items-center justify-center transition-all duration-300 ease-in-out">
            <div class="flex items-center justify-center h-14 w-14 bg-[#9d2449] rounded-full shadow-lg group-hover:shadow-2xl text-white transform transition-all duration-300 ease-in-out"
                 :class="fabExpanded ? 'w-60' : 'w-14'">
                
                <i class="fas fa-book-open text-xl transition-opacity duration-200"
                   :class="fabExpanded ? 'opacity-0' : 'opacity-100'"></i>
                
                <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                     :class="fabExpanded ? 'opacity-100' : 'opacity-0'">
                    <span class="text-base font-bold whitespace-nowrap">Ver Instrucciones</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Instrucciones -->
    <div x-show="instructionsModalOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4">

        <div @click.away="instructionsModalOpen = false"
             class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 text-left">
             
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Instrucciones de Cotejo</h3>
            
            <ul class="space-y-4 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-stamp text-[#9d2449] mt-1 mr-4 text-lg"></i>
                    <div>
                        <strong class="font-semibold text-gray-800">Calidad y Originalidad:</strong>
                        <p>Verifique la calidad del papel, sellos de tinta y firmas originales. Busque indicios de falsificación o alteración.</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-shield-alt text-[#9d2449] mt-1 mr-4 text-lg"></i>
                    <div>
                        <strong class="font-semibold text-gray-800">Elementos de Seguridad:</strong>
                        <p>Busque elementos de seguridad como hologramas, marcas de agua, o papel seguridad. Estos son comunes en documentos oficiales.</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-copy text-[#9d2449] mt-1 mr-4 text-lg"></i>
                    <div>
                        <strong class="font-semibold text-gray-800">Coincidencia Exacta:</strong>
                        <p>Asegúrese de que el documento físico coincida *exactamente* con la versión digital cargada en el sistema. Revise fechas, nombres y números.</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-tasks text-[#9d2449] mt-1 mr-4 text-lg"></i>
                    <div>
                        <strong class="font-semibold text-gray-800">Decisión Final:</strong>
                        <p>Para cada documento, añada observaciones si es necesario y marque como "Aprobado" o "Rechazado" basándose en una revisión cuidadosa.</p>
                    </div>
                </li>
            </ul>

            <div class="mt-6 text-right">
                <button @click="instructionsModalOpen = false"
                        class="px-5 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Documentos Pendientes -->
    <div x-show="documentosPendientesModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4">

        <div @click.away="documentosPendientesModal = false"
             class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 text-center">
             
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Atención</h3>
            
            <p class="text-gray-600">
                Debe revisar y tomar una decisión (Aprobar/Rechazar) para todos los documentos antes de poder finalizar el proceso de cotejo.
            </p>

            <div class="mt-6">
                <button @click="documentosPendientesModal = false"
                        class="w-full px-5 py-2.5 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Resumen de Cotejo -->
    <div x-show="showSummaryModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 backdrop-blur-sm p-4">

    <div @click.away="showSummaryModal = false"
         x-show="showSummaryModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-lg bg-gray-50 rounded-2xl shadow-2xl overflow-hidden transform transition-all">
        
        <!-- Barra de estado superior -->
        <div class="absolute top-0 left-0 right-0 h-1.5 transition-all duration-300"
             :class="{
                'bg-green-500': resumenCotejo.aprobados > resumenCotejo.rechazados,
                'bg-red-500': resumenCotejo.rechazados >= resumenCotejo.aprobados
             }"></div>
        
        <!-- Contenido del Modal -->
        <div class="relative">
            <!-- Estado de Carga -->
            <div x-show="loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-20">
                <i class="fas fa-spinner fa-spin text-primary text-5xl"></i>
            </div>

            <!-- Cabecera -->
            <div class="p-6 text-center bg-white">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4"
                     :class="{
                        'bg-green-100': resumenCotejo.aprobados > resumenCotejo.rechazados,
                        'bg-red-100': resumenCotejo.rechazados >= resumenCotejo.aprobados
                     }">
                    <i class="fas text-3xl" :class="{
                        'fa-check-circle text-green-600': resumenCotejo.aprobados > resumenCotejo.rechazados,
                        'fa-exclamation-triangle text-red-600': resumenCotejo.rechazados >= resumenCotejo.aprobados
                    }"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800" 
                    x-text="resumenCotejo.rechazados >= resumenCotejo.aprobados ? 'Trámite Será Cancelado' : 'Trámite Completado'">
                </h3>
                <p class="mt-2 text-sm text-gray-600 max-w-md mx-auto"
                   x-text="resumenCotejo.rechazados >= resumenCotejo.aprobados ? 
                          'Se encontraron más documentos rechazados que aprobados. El trámite será cancelado y deberá iniciarse nuevamente.' : 
                          'Los documentos han sido cotejados exitosamente. Puede proceder a finalizar el proceso.'">
                </p>
            </div>

            <!-- Contenido Principal del Modal -->
            <div x-show="!loading" x-transition class="px-6 pb-6 space-y-4">
                <!-- Resumen de conteo -->
                <div class="p-4 border rounded-lg bg-white text-sm text-gray-600 flex justify-around items-center">
                    <div class="text-center">
                        <span class="font-bold text-2xl text-red-600" x-text="resumenCotejo.rechazados"></span>
                        <p class="text-xs uppercase tracking-wide">Rechazado(s)</p>
                    </div>
                    <div class="text-center">
                        <span class="font-bold text-2xl text-green-600" x-text="resumenCotejo.aprobados"></span>
                        <p class="text-xs uppercase tracking-wide">Aprobado(s)</p>
                    </div>
                </div>

                <!-- Lista de Documentos -->
                <div class="border rounded-xl bg-white p-2">
                    <ul class="divide-y divide-gray-200 max-h-56 overflow-y-auto">
                        <template x-for="doc in resumenCotejo.documentos" :key="doc.id">
                            <li class="flex items-center justify-between p-3">
                                <div class="flex items-center min-w-0">
                                    <i class="fas mr-3" :class="{
                                        'fa-check-circle text-green-500': doc.estado === 'Aprobado',
                                        'fa-times-circle text-red-500': doc.estado === 'Rechazado'
                                    }"></i>
                                    <span class="truncate" x-text="doc.nombre"></span>
                                </div>
                                <span class="flex-shrink-0 text-xs font-bold rounded-full px-2.5 py-1"
                                    :class="{
                                        'bg-green-100 text-green-800': doc.estado === 'Aprobado',
                                        'bg-red-100 text-red-800': doc.estado === 'Rechazado'
                                    }"
                                    x-text="doc.estado"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Pie del Modal con Botones -->
            <div class="p-5 bg-gray-100 border-t border-gray-200">
                <div x-show="!loading" class="flex flex-col-reverse sm:flex-row sm:justify-end sm:items-center gap-3">
                    <button @click="showSummaryModal = false"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-200/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all">
                        <span>Cancelar</span>
                    </button>

                    <button @click="confirmarFinalizacion()"
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 text-sm font-bold text-white border border-transparent rounded-lg shadow-sm transition-all duration-300"
                            :class="{
                                'bg-green-600 hover:bg-green-700': resumenCotejo.aprobados > resumenCotejo.rechazados,
                                'bg-red-600 hover:bg-red-700': resumenCotejo.rechazados >= resumenCotejo.aprobados,
                                'opacity-50 cursor-not-allowed': loading
                            }">
                        <i class="fas mr-2" :class="{
                            'fa-check-double': !loading && resumenCotejo.aprobados > resumenCotejo.rechazados,
                            'fa-times': !loading && resumenCotejo.rechazados >= resumenCotejo.aprobados,
                            'fa-spinner fa-spin': loading
                        }"></i>
                        <span x-show="!loading" x-text="resumenCotejo.rechazados >= resumenCotejo.aprobados ? 'Cancelar Trámite' : 'Finalizar Proceso'"></span>
                        <span x-show="loading">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

    @push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

    @push('scripts')
    <script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cotejoDocumento', (config) => ({
        documentoId: config.documentoId,
        tramiteId: config.tramiteId,
        loading: false,
        comentario: config.comentarioInicial,
        isCotejado: config.esCotejado,
        estado: config.estadoInicial,
        
        get cardClasses() {
            return `bg-white border-gray-300`;
        },
        get iconContainerClasses() {
            return `bg-slate-500 text-white`;
        },
        get badgeClasses() {
            return `bg-slate-100 text-slate-700`;
        },
        get estadoTexto() {
            return this.isCotejado ? 'Cotejado' : 'Pendiente';
        },

        async aprobar() {
            await this.enviarDecision('aprobar');
        },
        async rechazar() {
            if (!this.comentario.trim()) {
                this.mostrarNotificacion('error', 'Debe proporcionar un comentario para rechazar.');
                return;
            }
            await this.enviarDecision('rechazar');
        },
        async enviarDecision(decision) {
            this.loading = true;
            try {
                const response = await fetch(`/revision/${this.tramiteId}/documento/${this.documentoId}/${decision}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        observaciones: this.comentario, // Corregido: de 'comentario' a 'observaciones'
                        documento_cotejado: true,
                        cotejo_presencial: true,
                        observaciones_cotejo: `Documento ${decision} en cotejo físico. ${this.comentario}`,
                        fecha_cotejo: new Date().toISOString()
                    })
                });
                const data = await response.json();
                if (data.success) {
                    this.isCotejado = true;
                    this.estado = (decision === 'aprobar') ? 'Aprobado' : 'Rechazado';
                    this.mostrarNotificacion('success', `Documento ${this.estado.toLowerCase()} correctamente.`);
                } else {
                    this.mostrarNotificacion('error', data.message || `Error al ${decision} el documento.`);
                }
            } catch (error) {
                console.error('Error:', error);
                this.mostrarNotificacion('error', 'Error de conexión al procesar la decisión.');
            } finally {
                this.loading = false;
            }
        },
        mostrarNotificacion(tipo, mensaje) {
            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-5 right-5 z-[100] px-4 py-3 rounded-lg shadow-xl text-white transform transition-all duration-300 ${
                tipo === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notificacion.innerHTML = `
                <div class="flex items-center">
                <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-3"></i>
                <span class="font-medium">${mensaje}</span>
                </div>
            `;
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.style.opacity = '0';
                notificacion.style.transform = 'translateY(-20px)';
                setTimeout(() => notificacion.remove(), 300);
            }, 5000);
        }
    }));

    Alpine.data('cotejoFlowManager', (tramiteId) => ({
        tramiteId: tramiteId,
        loading: false,
        instructionsModalOpen: false,
        showSummaryModal: false,
        documentosPendientesModal: false,
        resumenCotejo: {
            aprobados: 0,
            rechazados: 0,
            documentos: []
        },

        finalizar() {
            let todosCotejados = true;
            const documentosData = [];
            let aprobados = 0;
            let rechazados = 0;

            document.querySelectorAll('.documento-item').forEach(el => {
                if (el.dataset.cotejado !== 'true') {
                    todosCotejados = false;
                }
                const estado = el.dataset.estado;
                documentosData.push({
                    id: el.dataset.id,
                    nombre: el.dataset.nombre,
                    estado: estado
                });

                if (estado === 'Aprobado') {
                    aprobados++;
                } else if (estado === 'Rechazado') {
                    rechazados++;
                }
            });

            if (!todosCotejados) {
                this.documentosPendientesModal = true;
                return;
            }
            
            this.resumenCotejo = {
                aprobados,
                rechazados,
                documentos: documentosData
            };
            this.showSummaryModal = true;
        },

        async confirmarFinalizacion() {
            this.loading = true;
            try {
                const response = await fetch(`/revision/${this.tramiteId}/finalizar-cotejo`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.mostrarNotificacion('success', 'Proceso de cotejo finalizado correctamente. Redirigiendo...');
                    setTimeout(() => {
                        window.location.href = data.redirect_url || '/revision';
                    }, 2000);
                } else {
                    this.mostrarNotificacion('error', data.message || 'Error al finalizar el cotejo.');
                    this.loading = false;
                }
            } catch (error) {
                console.error('Error:', error);
                this.mostrarNotificacion('error', 'Error de conexión al finalizar el proceso.');
                this.loading = false;
            }
        },

        mostrarNotificacion(tipo, mensaje) {
            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-5 right-5 z-[100] px-4 py-3 rounded-lg shadow-xl text-white transform transition-all duration-300 ${
                tipo === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notificacion.innerHTML = `
                <div class="flex items-center">
                <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-3"></i>
                <span class="font-medium">${mensaje}</span>
                </div>
            `;
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.style.opacity = '0';
                notificacion.style.transform = 'translateY(-20px)';
                setTimeout(() => notificacion.remove(), 300);
            }, 5000);
        }
    }));
});
    </script>
    @endpush