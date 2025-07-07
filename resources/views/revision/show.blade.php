@extends('layouts.app')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-[#9d2449] transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right w-3 h-3 text-gray-400 mx-1"></i>
                    <a href="{{ route('revision.index') }}" class="text-sm text-gray-600 hover:text-[#9d2449] transition-colors duration-200">
                        Revisiones
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right w-3 h-3 text-gray-400 mx-1"></i>
                    <span class="text-sm text-gray-500">Trámite #{{ $tramite->id ?? '' }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Principal -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-sync-alt animate-spin-slow text-white text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-800">Proceso de Revisión</h1>
                        <span class="bg-[#9d2449]/10 text-[#9d2449] text-sm px-3 py-1 rounded-full font-medium">
                            En proceso
                        </span>
                    </div>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-hashtag mr-1"></i>
                            {{ $tramite->id }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            {{ $tramite->solicitante->rfc ?? 'N/A' }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-briefcase mr-1"></i>
                            {{ ucfirst($tramite->tipo_tramite) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opciones de Revisión -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Revisión Digital -->
        <div class="group bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-[#9d2449]/10 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-check-circle text-white text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors duration-200">
                                Revisión Digital
                            </h3>
                            <div class="flex items-center text-sm text-emerald-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span>Revisión completada</span>
                            </div>
                        </div>
                        <span class="text-xs font-medium bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                            Paso 1 - Completado
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Los documentos digitales han sido validados correctamente.
                    </p>
                    <a href="{{ route('revision.digital', $tramite) }}" 
                       class="inline-flex items-center text-emerald-600 text-sm font-medium hover:text-emerald-700">
                        <span>Ver detalles de la revisión</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sección de Cotejo Presencial -->
        <div x-data="{ showVerifyModal: false }" class="relative overflow-hidden rounded-xl shadow-lg border border-gray-200">
            @if($tramite->estado === 'Cancelado')
                <!-- Trámite Cancelado -->
                <div class="bg-red-50 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-red-700">Trámite Cancelado</h3>
                            <p class="text-sm text-red-600 mt-1">
                                @php
                                    $motivo = str_replace('_', ' ', $tramite->motivo_cancelacion);
                                    $motivo = ucfirst($motivo);
                                @endphp
                                Motivo: {{ $motivo }}
                            </p>
                            @if($citaCotejo)
                            <p class="text-xs text-red-500 mt-2">
                                Tenía cita programada para: {{ \Carbon\Carbon::parse($citaCotejo->fecha_hora)->format('d/m/Y H:i') }} hrs
                            </p>
                            @endif
                            <p class="text-xs text-red-500 mt-1">
                                Cancelado el: {{ $tramite->fecha_cancelacion ? \Carbon\Carbon::parse($tramite->fecha_cancelacion)->format('d/m/Y H:i') : 'N/A' }} hrs
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($citaCotejo && $citaCotejo->estado === 'pendiente')
                <!-- Cita Existente - Compacto -->
                <div class="bg-white p-4">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-[#9d2449]/10 rounded-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Cita Agendada</h3>
                                    <p class="text-gray-500 text-sm">Cotejo Presencial</p>
                                </div>
                            </div>
                            
                            <div class="px-3 py-1 bg-[#9d2449]/10 rounded-lg">
                                <span class="text-xs font-semibold text-[#9d2449]">Pendiente</span>
                            </div>
                        </div>

                        <!-- Información de la Cita -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="text-center">
                                        <div class="flex items-center space-x-2">
                                            <svg class="h-4 w-4 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ $citaCotejo->fecha_hora->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <svg class="h-4 w-4 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ $citaCotejo->fecha_hora->format('H:i') }} hrs
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de Verificación de Identidad -->
                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <button @click="showVerifyModal = true"
                                        class="w-full bg-[#9d2449] hover:bg-[#7a1d3a] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-id-card"></i>
                                    <span>Verificar Identidad</span>
                                </button>
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    Haga clic para verificar la identidad del solicitante antes de proceder con el cotejo
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Sin Cita o Cita No Pendiente -->
                <div class="bg-gray-50 p-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-400">Cotejo Presencial</h3>
                                <p class="text-xs text-gray-400">
                                    @if(!$citaCotejo)
                                        No hay cita programada
                                    @else
                                        Cita {{ strtolower($citaCotejo->estado) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal de Verificación de Identidad -->
            @if($tramite->estado !== 'Cancelado')
                <div x-show="showVerifyModal" 
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto"
                     aria-labelledby="modal-title" 
                     role="dialog" 
                     aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Overlay de fondo -->
                        <div x-show="showVerifyModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                             @click="showVerifyModal = false"
                             aria-hidden="true"></div>

                        <!-- Centrado del modal -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Contenido del Modal -->
                        <div x-show="showVerifyModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div>
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-[#9d2449]/10">
                                    <i class="fas fa-id-card text-[#9d2449] text-lg"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Verificación de Identidad del Solicitante
                                    </h3>
                                    <div class="mt-4">
                                        <!-- Botón para ver identificación -->
                                        <div class="mb-4">
                                            <a href="{{ route('documentos.ver', ['documentoSolicitante' => $documentoIdentificacion->id ?? '']) }}" 
                                               target="_blank"
                                               class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                                                <i class="fas fa-id-card mr-2"></i>
                                                Ver Identificación Digital
                                            </a>
                                            @if(!isset($documentoIdentificacion))
                                                <p class="text-sm text-red-600 mt-2">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    No se encontró el documento de identificación
                                                </p>
                                            @endif
                                        </div>

                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-exclamation-triangle text-amber-400"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-amber-800">
                                                        Importante
                                                    </h3>
                                                    <div class="mt-2 text-sm text-amber-700">
                                                        <p class="text-left">
                                                            Antes de proceder, verifique:
                                                        </p>
                                                        <ul class="list-disc list-inside mt-2 space-y-1 text-left">
                                                            <li>La identificación oficial vigente</li>
                                                            <li>Que la foto coincida con la persona presente</li>
                                                            <li>Que los datos coincidan con los documentos digitales</li>
                                                            <li>Que no presente alteraciones o daños</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 space-y-3">
                                <button type="button"
                                        @click="showVerifyModal = false; window.location.href = '{{ route('revision.presencial', $tramite) }}'"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-3 bg-[#9d2449] text-base font-medium text-white hover:bg-[#7a1d3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:text-sm transition-colors duration-200">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Identidad Verificada - Continuar con el Cotejo
                                </button>
                                
                                <button type="button"
                                        @click="showVerifyModal = false; window.location.href = '{{ route('citas.reagendar', ['tramite' => $tramite->id, 'motivo' => 'identificacion_no_coincide']) }}'"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-3 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:text-sm transition-colors duration-200">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Reagendar Cita - Identificación No Coincide
                                </button>

                                <button type="button"
                                        @click="showVerifyModal = false; window.location.href = '{{ route('tramites.cancelar', ['tramite' => $tramite->id, 'motivo' => 'identificacion_invalida']) }}'"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-3 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-colors duration-200">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Cancelar Trámite - Identificación Inválida
                                </button>

                                <button type="button"
                                        @click="showVerifyModal = false"
                                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:text-sm transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i>
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Nota Informativa -->
    <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl p-6 border border-gray-100 shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d3a]/5 rounded-full transform -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-[#9d2449]/5 to-[#7a1d3a]/5 rounded-full transform translate-y-12 -translate-x-12"></div>
        
        <div class="relative flex items-start space-x-4">
            <div class="w-10 h-10 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-xl flex items-center justify-center">
                <i class="fas fa-info-circle text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Estado del Proceso</h3>
                <div class="mt-3 space-y-3">
                    @if($tramite->estado === 'Cancelado')
                        <div class="flex items-center space-x-2">
                            <span class="flex-shrink-0 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-sm"></i>
                            </span>
                            <span class="text-sm text-red-600">El trámite ha sido cancelado</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <span class="flex-shrink-0 w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-sm"></i>
                            </span>
                            <span class="text-sm text-gray-600">Revisión digital completada exitosamente</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="flex-shrink-0 w-8 h-8 bg-[#9d2449] text-white rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-sm"></i>
                            </span>
                            <span class="text-sm text-gray-600">Pendiente realizar el cotejo presencial de documentos</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Ya no necesitamos esta función ya que manejamos todo con Alpine.js directamente
    function verificarIdentidad(tramiteId, tieneIdentificacion) {
        if (!tieneIdentificacion) {
            alert('Error: No se ha encontrado el documento de identificación en el sistema.');
            return;
        }
        window.location.href = "{{ route('revision.presencial', $tramite) }}";
    }
</script>
@endpush
@endsection