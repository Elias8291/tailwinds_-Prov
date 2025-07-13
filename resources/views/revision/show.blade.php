@extends('layouts.app')

@section('content')
<div class="max-w-[1200px] mx-auto px-3 sm:px-4 md:px-6 py-4 md:py-8">
    <!-- Header y Contenido Principal con Estado de Alpine.js -->
    <div x-data="{ currentStep: '{{ $tramite->estado === 'Por Cotejar' ? 'cotejo' : 'revision' }}' }">
        <!-- Header Principal Rediseñado -->
        <div class="relative bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 p-4 mb-6">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-gradient-to-br from-[#9d2449]/5 to-transparent rounded-full opacity-50"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-gradient-to-tr from-[#9d2449]/5 to-transparent rounded-full opacity-50"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#9d2449] to-[#7a1d3a] rounded-xl flex items-center justify-center shadow-md ring-2 ring-white">
                            <i class="fas fa-file-signature text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Proceso de Revisión</h1>
                            <p class="text-sm text-gray-500">Trámite de {{ ucfirst($tramite->tipo_tramite) }}</p>
                        </div>
                </div>
                    <div class="mt-4 sm:mt-0 flex-shrink-0">
                        <span class="inline-flex items-center bg-[#9d2449]/10 text-[#9d2449] text-sm px-3 py-1.5 rounded-full font-bold">
                            <span class="w-2 h-2 bg-[#9d2449] rounded-full mr-2 animate-pulse"></span>
                            En proceso
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 border-t border-gray-200/80 pt-3 flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-2 sm:space-y-0 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-hashtag text-gray-400 w-4 text-center mr-2"></i>
                        <strong>Folio:</strong><span class="ml-2 font-mono">#{{ $tramite->id }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user-tie text-gray-400 w-4 text-center mr-2"></i>
                        <strong>Solicitante:</strong><span class="ml-2 truncate">{{ $tramite->solicitante->nombre_completo ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-id-card-alt text-gray-400 w-4 text-center mr-2"></i>
                        <strong>RFC:</strong><span class="ml-2 truncate">{{ $tramite->solicitante->rfc ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($tramite->estado !== 'Cancelado')
            <!-- Stepper Horizontal de Proceso -->
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-md mb-6">
                <div class="flex items-center justify-between space-x-2 sm:space-x-4">
                    <!-- Paso 1: Revisión Digital -->
                    <div @click="currentStep = 'revision'" 
                         class="flex flex-col items-center flex-1 group cursor-pointer p-2 rounded-lg transition-colors duration-200 hover:bg-gray-50" 
                         :class="{'bg-gray-100': currentStep === 'revision'}">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ in_array($tramite->estado, ['Por Cotejar', 'Aprobado', 'Rechazado']) ? 'bg-green-100 ring-4 ring-green-50' : 'bg-gray-100 ring-4 ring-white' }} transition-all duration-300 mb-2">
                            <i class="fas fa-check text-lg {{ in_array($tramite->estado, ['Por Cotejar', 'Aprobado', 'Rechazado']) ? 'text-green-600' : 'text-gray-400' }}"></i>
                        </div>
                        <h4 class="font-bold text-center text-sm {{ in_array($tramite->estado, ['Por Cotejar', 'Aprobado', 'Rechazado']) ? 'text-gray-800 group-hover:text-green-700' : 'text-gray-400' }}">Revisión Digital</h4>
                        <p class="text-xs text-center {{ in_array($tramite->estado, ['Por Cotejar', 'Aprobado', 'Rechazado']) ? 'text-green-600' : 'text-gray-400' }} font-semibold mt-0.5">{{ in_array($tramite->estado, ['Por Cotejar', 'Aprobado', 'Rechazado']) ? 'Completada' : 'Pendiente' }}</p>
                    </div>
                    
                    <!-- Conector -->
                    <div class="flex-auto border-t-2 {{ in_array($tramite->estado, ['Por Cotejar', 'Aprobado', 'Rechazado']) ? 'border-green-500' : 'border-dashed border-gray-300' }} mx-2 sm:mx-4"></div>
                    
                    <!-- Paso 2: Cotejo Presencial -->
                    <div @click="currentStep = 'cotejo'" 
                         class="flex flex-col items-center flex-1 group cursor-pointer p-2 rounded-lg transition-colors duration-200 hover:bg-gray-50" 
                         :class="{'bg-gray-100': currentStep === 'cotejo'}">
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full {{ $tramite->estado === 'Por Cotejar' ? 'bg-amber-100 ring-4 ring-amber-50' : (in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'bg-green-100 ring-4 ring-green-50' : 'bg-gray-100 ring-4 ring-white') }} mb-2">
                            @if($tramite->estado === 'Por Cotejar')
                                <span class="absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75 animate-ping"></span>
                                <i class="relative fas fa-user-clock text-amber-600 text-sm"></i>
                            @elseif(in_array($tramite->estado, ['Aprobado', 'Rechazado']))
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            @else
                                <i class="fas fa-user-clock text-gray-400 text-sm"></i>
                            @endif
                        </div>
                        <h4 class="font-bold text-center text-sm {{ $tramite->estado === 'Por Cotejar' ? 'text-gray-800 group-hover:text-amber-700' : (in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'text-gray-800 group-hover:text-green-700' : 'text-gray-400') }}">Cotejo Presencial</h4>
                        <p class="text-xs text-center {{ $tramite->estado === 'Por Cotejar' ? 'text-amber-600' : (in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'text-green-600' : 'text-gray-400') }} font-semibold">
                            {{ $tramite->estado === 'Por Cotejar' ? 'Acción Requerida' : (in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'Completada' : 'Pendiente') }}
                        </p>
    </div>

                    <!-- Conector -->
                    <div class="flex-auto border-t-2 {{ in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'border-green-500' : 'border-dashed border-gray-300' }} mx-2 sm:mx-4"></div>
                    
                    <!-- Paso 3: Futuro -->
                    <div class="flex flex-col items-center flex-1 cursor-not-allowed p-2 rounded-lg">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'bg-green-100 ring-4 ring-green-50' : 'bg-gray-100 ring-4 ring-white' }} mb-2">
                            <i class="fas fa-flag-checkered text-sm {{ in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'text-green-600' : 'text-gray-400' }}"></i>
                        </div>
                        <h4 class="font-bold text-center text-sm {{ in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'text-gray-800' : 'text-gray-400' }}">Finalizado</h4>
                        <p class="text-xs text-center {{ in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'text-green-600' : 'text-gray-400' }} font-semibold">{{ in_array($tramite->estado, ['Aprobado', 'Rechazado']) ? 'Completado' : 'Pendiente' }}</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Mensaje de Trámite Cancelado -->
            <div class="bg-red-50 rounded-xl p-6 border border-red-200 shadow-md mb-8">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-ban text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-red-800">Trámite Cancelado</h3>
                        <p class="text-sm text-red-600">Este trámite ha sido cancelado y no se puede continuar con el proceso de revisión.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Opciones de Revisión (Contenido Dinámico) -->
        <div class="mb-6 md:mb-8">
            <!-- Revisión Digital -->
            <div x-show="currentStep === 'revision'" x-transition.opacity.duration.300ms>
                <div class="group bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-xl hover:border-green-300 transition-all duration-300 p-4 sm:p-6 h-full flex flex-col">
                    <!-- Cabecera -->
                    <div class="sm:flex sm:items-start sm:justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-green-100 rounded-full">
                                <i class="fas fa-clipboard-check text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Revisión Digital</h3>
                                <p class="text-sm text-gray-500">Validación de documentos</p>
                            </div>
                        </div>
                        <span class="mt-2 sm:mt-0 text-xs font-medium bg-green-100 text-green-700 px-3 py-1 rounded-full inline-block">Completada</span>
                    </div>

                    <!-- Información de la Revisión -->
                    <div class="pl-11 mb-6 flex-grow">
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            La validación de todos los documentos y secciones digitales subidos por el solicitante ha finalizado.
                        </p>
                        <div class="border-l-2 border-dashed border-gray-300 pl-5 py-2 space-y-2">
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-copy w-5 text-center text-gray-400 mr-3"></i>
                                <span class="font-semibold">{{ $tramite->seccionesRevision->count() }} Secciones Revisadas</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-calendar-check w-5 text-center text-gray-400 mr-3"></i>
                                <span class="font-semibold">Finalizado el: {{ $tramite->fecha_revision ? $tramite->fecha_revision->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Acción -->
                    <div class="mt-auto pl-11">
                    <a href="{{ route('revision.v2', $tramite) }}" 
                           class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 text-sm shadow-md hover:shadow-lg transform group-hover:scale-105">
                            <i class="fas fa-eye"></i>
                            <span>Ver Detalles de Revisión</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sección de Cotejo Presencial -->
            <div x-show="currentStep === 'cotejo'" x-transition.opacity.duration.300ms>
                 <div x-data="{ showVerifyModal: false }" 
                     class="group rounded-xl border border-gray-200 shadow-md hover:shadow-xl transition-all duration-300 h-full
                            @if($tramite->estado === 'Por Cotejar') hover:border-[#9d2449]/50 
                            @elseif($tramite->estado === 'Cancelado') hover:border-red-300 
                            @endif">
            @if($tramite->estado === 'Cancelado')
                <!-- Trámite Cancelado -->
                        <div class="bg-red-50/60 p-4 sm:p-6 h-full rounded-xl">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600 text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-red-700">Trámite Cancelado</h3>
                            <p class="text-xs sm:text-sm text-red-600 mt-1">
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
                            
                            <!-- Botón de Verificación de Identidad disponible incluso si está cancelado -->
                            <div class="mt-4 border-t border-red-200 pt-4">
                                <button @click="showVerifyModal = true"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 sm:py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2 text-sm transform group-hover:scale-105">
                                    <i class="fas fa-id-card text-sm"></i>
                                    <span>Ver Credencial para Comparación</span>
                                </button>
                                <p class="text-xs text-red-400 mt-2 text-center px-2">
                                    Disponible para verificación y comparación de credencial
                                </p>
                    </div>
                </div>
                    @elseif($tramite->estado === 'Por Cotejar')
                        <!-- Nuevo Diseño para Cotejo Presencial -->
                        <div class="bg-white p-4 sm:p-6 h-full flex flex-col rounded-xl">
                            <!-- Cabecera -->
                            <div class="sm:flex sm:items-start sm:justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-[#9d2449]/10 rounded-full">
                                        <i class="fas fa-user-check text-[#9d2449]"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800">Verificación de Identidad</h3>
                                        <p class="text-sm text-gray-500">Cotejo Presencial del Solicitante</p>
                                    </div>
                                </div>
                                <span class="mt-2 sm:mt-0 text-xs font-medium bg-amber-100 text-amber-700 px-3 py-1 rounded-full inline-block">Acción Requerida</span>
                            </div>

                            <!-- Información de la Cita y Verificación -->
                            <div class="pl-11 mb-6 flex-grow">
                                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                    Para continuar con el trámite, el solicitante debe presentarse en la fecha y hora agendada para el cotejo de su identificación oficial.
                                </p>
                                @if($citaCotejo)
                                <div class="border-l-2 border-dashed border-gray-300 pl-5 py-2 space-y-2">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-calendar-alt w-5 text-center text-gray-400 mr-3"></i>
                                        <span class="font-semibold">Cita: {{ $citaCotejo->fecha_hora->format('d/m/Y H:i') }} hrs</span>
                                    </div>
                                </div>
                                @else
                                <div class="border-l-2 border-dashed border-gray-300 pl-5 py-2">
                                    <p class="text-sm text-amber-600 font-semibold">No se ha programado una cita para el cotejo de identidad.</p>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Botón de Acción -->
                            <div class="mt-auto pl-11">
                                <button @click="showVerifyModal = true"
                                        class="w-full sm:w-auto bg-[#9d2449] hover:bg-[#7a1d3a] text-white px-4 py-2.5 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 text-sm shadow-md hover:shadow-lg transform group-hover:scale-105">
                                    <i class="fas fa-id-card"></i>
                                    <span>Iniciar Cotejo de Identidad</span>
                                </button>
                            </div>
                        </div>
                    @elseif(in_array($tramite->estado, ['Aprobado', 'Rechazado', 'Finalizado']))
                        <!-- Estado Completado o Finalizado -->
                        <div class="bg-white p-4 sm:p-6 h-full flex flex-col rounded-xl">
                            <!-- Cabecera -->
                            <div class="sm:flex sm:items-start sm:justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-green-100 rounded-full">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800">Identidad Verificada</h3>
                                        <p class="text-sm text-gray-500">Credencial oficial del solicitante.</p>
                                    </div>
                                        </div>
                                <span class="mt-2 sm:mt-0 text-xs font-medium bg-green-100 text-green-700 px-3 py-1 rounded-full inline-block">Completada</span>
                                        </div>

                            <!-- Información de la Verificación -->
                            <div class="pl-11 mb-6 flex-grow">
                                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                    La credencial oficial del solicitante ha sido verificada y coincide con la copia digital en el sistema.
                                </p>
                                <div class="border-l-2 border-dashed border-gray-300 pl-5 py-2 space-y-2">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-calendar-check w-5 text-center text-gray-400 mr-3"></i>
                                        <span class="font-semibold">Verificado el: {{ $tramite->fecha_verificacion ? $tramite->fecha_verificacion->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de Acción -->
                            <div class="mt-auto pl-11">
                                <a href="{{ route('revision.v2', $tramite) }}" 
                                   class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 text-sm shadow-md hover:shadow-lg transform group-hover:scale-105">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver Detalles de Revisión</span>
                                </a>
                    </div>
                </div>
            @else
                        <!-- Estado no válido para cotejo -->
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-xl h-full">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar-times text-gray-400 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm sm:text-base font-semibold text-gray-400 truncate">Cotejo Presencial</h3>
                                <p class="text-xs text-gray-400">
                                            No disponible en este estado del trámite
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

                    <!-- Modal de Verificación de Identidad - Solo si está Por Cotejar -->
                    @if($tramite->estado === 'Por Cotejar')
                <div x-show="showVerifyModal" 
                     x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-0"
                     aria-labelledby="modal-title" 
                     role="dialog" 
                     aria-modal="true">
                            <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <!-- Overlay -->
                                <div x-show="showVerifyModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showVerifyModal = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                <!-- Contenido del Modal Rediseñado -->
                        <div x-show="showVerifyModal"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                    
                                    <!-- Encabezado del Modal -->
                                    <div class="bg-white px-6 py-4 border-b border-gray-200 flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-[#9d2449]/10 rounded-full">
                                                <i class="fas fa-user-check text-lg text-[#9d2449]"></i>
                                            </div>
                            <div>
                                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                                    Verificación de Identidad
                                                </h3>
                                                <p class="text-sm text-gray-500">Cotejo de documento oficial.</p>
                                </div>
                                        </div>
                                        <button @click="showVerifyModal = false" class="text-gray-400 hover:text-gray-600 transition">
                                            <span class="sr-only">Cerrar</span>
                                            <i class="fas fa-times h-6 w-6"></i>
                                        </button>
                                        </div>

                                    <!-- Cuerpo del Modal -->
                                    <div class="px-6 py-5">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Columna de Verificación -->
                                            <div>
                                                <h4 class="text-base font-semibold text-gray-800 mb-3">Puntos Clave</h4>
                                                <ul class="space-y-3 text-sm text-gray-700">
                                                    <li class="flex items-start"><i class="fas fa-id-card-alt text-amber-500 mt-1 mr-3"></i><span>Identificación vigente y aceptada.</span></li>
                                                    <li class="flex items-start"><i class="fas fa-user-circle text-amber-500 mt-1 mr-3"></i><span>Fotografía coincide con persona.</span></li>
                                                    <li class="flex items-start"><i class="fas fa-info-circle text-amber-500 mt-1 mr-3"></i><span>Datos coinciden con documentos digitales.</span></li>
                                                    <li class="flex items-start"><i class="fas fa-exclamation-triangle text-amber-500 mt-1 mr-3"></i><span>Documento sin alteraciones ni daños.</span></li>
                                                </ul>
                                                </div>
                                            <!-- Columna de Documento -->
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                <h4 class="text-base font-semibold text-gray-800 mb-3">Documento Digital</h4>
                                                <p class="text-sm text-gray-600 mb-4">
                                                    Visualice el documento para comparar con el físico.
                                                </p>
                                                @php
                                                    // Buscar el documento de identificación usando la misma lógica del controlador
                                                    $documentoIdentificacion = $tramite->documentosSolicitante()
                                                        ->whereHas('documento', function($query) {
                                                            $query->where('nombre', 'like', '%identificación%')
                                                                ->orWhere('nombre', 'like', '%identificacion%')
                                                                ->orWhere('nombre', 'like', '%INE%')
                                                                ->orWhere('nombre', 'like', '%IFE%')
                                                                ->orWhere('nombre', 'like', '%pasaporte%');
                                                        })
                                                        ->first();
                                                @endphp
                                                @if($documentoIdentificacion)
                                                    <a href="{{ route('revision.ver-documento', ['tramite' => $tramite->id, 'documento' => $documentoIdentificacion->id]) }}?inline=1" 
                                                       target="_blank"
                                                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 hover:bg-gray-100 transition-colors duration-200 text-sm font-semibold shadow-sm">
                                                        <i class="fas fa-external-link-alt mr-2"></i>
                                                        Ver Identificación Digital
                                                    </a>
                                                    <p class="text-xs text-gray-500 mt-2 text-center">
                                                        Se abrirá en una nueva pestaña para comparación
                                                    </p>
                                                @else
                                                    <div class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 text-sm font-semibold">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        Documento de identificación no encontrado
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pie del Modal con Acciones -->
                                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                        <h4 class="text-sm font-semibold text-gray-600 mb-3 text-center sm:text-left">Acción después de verificar:</h4>
                                        <div class="flex flex-col sm:flex-row-reverse gap-3">
                                <button type="button"
                                        @click="showVerifyModal = false; window.location.href = '{{ route('revision.presencial', $tramite) }}'"
                                                    class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-[#9d2449] text-base font-medium text-white hover:bg-[#7a1d3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] transition-colors duration-200">
                                                <i class="fas fa-check-double mr-2"></i>
                                                <span>Identidad Cotejada</span>
                                </button>
                                <button type="button"
                                        @click="showVerifyModal = false; window.location.href = '{{ route('citas.reagendar', ['tramite' => $tramite->id, 'motivo' => 'identificacion_no_coincide']) }}'"
                                                    class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200">
                                                <i class="fas fa-calendar-times mr-2 text-amber-600"></i>
                                                <span>Reagendar Cita</span>
                                </button>
                                <button type="button"
                                        @click="showVerifyModal = false; window.location.href = '{{ route('tramites.cancelar', ['tramite' => $tramite->id, 'motivo' => 'identificacion_invalida']) }}'"
                                                    class="w-full sm:w-auto inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                                <i class="fas fa-ban mr-2 text-red-600"></i>
                                                <span>Cancelar Trámite</span>
                                </button>
                            </div>
                        </div>

        </div>
    </div>
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
    // Ya no se necesitan scripts adicionales aquí para esta funcionalidad
</script>
@endpush
@endsection