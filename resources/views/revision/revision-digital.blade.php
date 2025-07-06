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

        <!-- Sección de Cotejo Presencial -->
        <div x-data="agendarCitaData()" class="relative overflow-hidden rounded-xl shadow-lg border border-gray-100">
            @if($citaCotejo)
                <!-- Cita Existente - Compacto -->
                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d3a] p-4 text-white">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-white bg-opacity-10 rounded-full -translate-y-4 translate-x-4"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">Cita Agendada</h3>
                                    <p class="text-white text-opacity-80 text-sm">Cotejo Presencial</p>
                                </div>
                            </div>
                            
                            <div class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg">
                                <span class="text-xs font-semibold text-white">
                                    @php
                                        $estadosLabel = [
                                            'pendiente' => 'Pendiente',
                                            'confirmada' => 'Confirmada', 
                                            'completada' => 'Completada',
                                            'cancelada' => 'Cancelada'
                                        ];
                                    @endphp
                                    {{ $estadosLabel[$citaCotejo->estado] ?? ucfirst($citaCotejo->estado) }}
                                </span>
                            </div>
                        </div>

                        <!-- Información Compacta -->
                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 mb-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="text-center">
                                        <div class="flex items-center space-x-2">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm font-bold text-white">
                                                {{ $citaCotejo->fecha_hora->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-bold text-white">
                                                {{ $citaCotejo->fecha_hora->format('H:i') }} hrs
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-xs text-white text-opacity-75">
                                        {{ $citaCotejo->fecha_hora->locale('es')->isoFormat('dddd') }}
                                    </p>
                                    <p class="text-xs text-white text-opacity-75 mt-1">
                                        @if($citaCotejo->fecha_hora->isFuture())
                                            {{ $citaCotejo->fecha_hora->diffForHumans() }}
                                        @else
                                            {{ $citaCotejo->fecha_hora->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones Compactos -->
                        <div class="flex justify-between items-center">
                            @if($citaCotejo->notas)
                                <div class="flex-1 mr-3">
                                    <p class="text-xs text-white text-opacity-75 truncate">
                                        {{ Str::limit($citaCotejo->notas, 50) }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($citaCotejo->estado === 'pendiente' || $citaCotejo->estado === 'confirmada')
                                <button
                                    type="button"
                                    @click="initialized && (showAgendarCitaModal = true)"
                                    :disabled="!initialized"
                                    class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 backdrop-blur-sm text-white font-medium rounded-lg hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-200 text-sm">
                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Reagendar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Sin Cita - Compacto -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-4 border border-amber-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">Cotejo Presencial</h3>
                                <p class="text-xs text-gray-600">Agende su cita para el cotejo presencial</p>
                            </div>
                        </div>
                        
                        <button
                            type="button"
                            @click="initialized && (showAgendarCitaModal = true)"
                            :disabled="!initialized"
                            :class="initialized ? 'bg-[#9d2449] hover:bg-[#7a1d3a]' : 'bg-gray-400 cursor-not-allowed'"
                            class="inline-flex items-center px-4 py-2 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9d2449] focus:ring-offset-2 transition-colors duration-200 shadow-lg text-sm">
                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Agendar
                        </button>
                    </div>
                </div>
            @endif

            <!-- Mensajes de éxito -->
            <div x-show="success" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-4 p-3 bg-green-100 border border-green-300 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-green-800 font-medium" x-text="success"></span>
                </div>
            </div>

            <!-- Modal para agendar cita -->
            <div x-show="initialized && showAgendarCitaModal" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true"
                 style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="resetCitaForm()"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div>
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-[#9d2449] bg-opacity-10">
                                <svg class="h-6 w-6 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    @if($citaCotejo)
                                        Reagendar Cita de Cotejo Presencial
                                    @else
                                        Agendar Cita de Cotejo Presencial
                                    @endif
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Programe la cita para el cotejo presencial de todos los documentos del trámite
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="agendarCita()" class="mt-5 space-y-4">
                            <div>
                                <label for="fecha_cita" class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
                                <input type="datetime-local" 
                                       id="fecha_cita"
                                       x-model="fechaCita"
                                       required
                                       :min="minDateTime"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm">
                            </div>

                            <div>
                                <label for="motivo_cita" class="block text-sm font-medium text-gray-700">Motivo</label>
                                <input type="text" 
                                       id="motivo_cita"
                                       x-model="motivoCita"
                                       required
                                       readonly
                                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm">
                            </div>

                            <div>
                                <label for="notas_cita" class="block text-sm font-medium text-gray-700">Notas adicionales</label>
                                <textarea x-model="notasCita"
                                          id="notas_cita"
                                          rows="3"
                                          placeholder="Información adicional sobre la cita, documentos específicos a revisar, etc..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#9d2449] focus:ring-[#9d2449] sm:text-sm"></textarea>
                            </div>

                            <!-- Mensajes de error -->
                            <div x-show="error" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 class="p-3 bg-red-100 border border-red-300 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="text-red-800 font-medium" x-text="error"></span>
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit"
                                        :disabled="isLoadingCita"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#9d2449] text-base font-medium text-white hover:bg-[#7a1d3a] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:col-start-2 sm:text-sm disabled:opacity-50">
                                    <svg x-show="isLoadingCita" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="isLoadingCita ? 'Procesando...' : (@if($citaCotejo) 'Reagendar Cita' @else 'Agendar Cita' @endif)"></span>
                                </button>
                                <button type="button"
                                        @click="resetCitaForm()"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor de Secciones -->
        <div x-data="{ 
            activeSection: null,
            isValidated(estado) {
                return estado === 'aprobado' || estado === 'rechazado';
            }
        }" class="grid grid-cols-1 gap-6">
            <!-- Sección de Datos Generales -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div x-data="{ 
                    isOpen: !isValidated('{{ $revisionesExistentes[1]['estado'] ?? 'pendiente' }}'),
                    estado: '{{ $revisionesExistentes[1]['estado'] ?? 'pendiente' }}'
                }">
                    <div @click="isOpen = !isOpen" 
                         class="p-6 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h2 class="text-xl font-bold text-gray-800">Datos Generales</h2>
                                <i class="fas" :class="isOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </div>
                            @if(isset($revisionesExistentes[1]))
                                <span id="estado_seccion_1" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2
                                    @if($revisionesExistentes[1]['estado'] === 'aprobado') bg-green-100 text-green-800
                                    @elseif($revisionesExistentes[1]['estado'] === 'rechazado') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>{{ ucfirst($revisionesExistentes[1]['estado']) }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-6 pt-0">
                        @php
                            $documentosDatosGenerales = collect($documentosPorSeccion['datos_generales'] ?? [])->toArray();
                            $formularioDatosGenerales = view('components.formularios.seccion-datos-generales', [
                                'datosTramite' => $datosTramite,
                                'readonly' => true
                            ])->render();
                        @endphp
                        
                        <x-revision.comparador-documento
                            :titulo="'Datos Generales y Documentación'"
                            :documento="$documentosDatosGenerales[0] ?? []"
                            :formulario="$formularioDatosGenerales"
                            :tramiteId="$tramite->id"
                        />

                        <x-revision.seccion-revision 
                            :seccionId="1"
                            :estado="$revisionesExistentes[1]['estado'] ?? null"
                            :comentario="$revisionesExistentes[1]['comentario'] ?? null"
                            :tramiteId="$tramite->id"
                        />
                    </div>
                </div>
            </div>

            <!-- Sección de Domicilio -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div x-data="{ 
                    isOpen: !isValidated('{{ $revisionesExistentes[2]['estado'] ?? 'pendiente' }}'),
                    estado: '{{ $revisionesExistentes[2]['estado'] ?? 'pendiente' }}'
                }">
                    <div @click="isOpen = !isOpen" 
                         class="p-6 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h2 class="text-xl font-bold text-gray-800">Domicilio</h2>
                                <i class="fas" :class="isOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </div>
                            @if(isset($revisionesExistentes[2]))
                                <span id="estado_seccion_2" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2
                                    @if($revisionesExistentes[2]['estado'] === 'aprobado') bg-green-100 text-green-800
                                    @elseif($revisionesExistentes[2]['estado'] === 'rechazado') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>{{ ucfirst($revisionesExistentes[2]['estado']) }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-6 pt-0">
                        @php
                            $documentosDomicilio = collect($documentosPorSeccion['domicilio'] ?? [])->toArray();
                            $formularioDomicilio = view('components.formularios.seccion-domicilio', [
                                'datosDomicilio' => $datosDomicilio,
                                'readonly' => true
                            ])->render();
                        @endphp

                        <x-revision.comparador-documento
                            :titulo="'Domicilio y Comprobante'"
                            :documento="$documentosDomicilio[0] ?? []"
                            :formulario="$formularioDomicilio"
                            :tramiteId="$tramite->id"
                        />

                        <x-revision.seccion-revision 
                            :seccionId="2"
                            :estado="$revisionesExistentes[2]['estado'] ?? null"
                            :comentario="$revisionesExistentes[2]['comentario'] ?? null"
                            :tramiteId="$tramite->id"
                        />
                    </div>
                </div>
            </div>

            @if($tramite->solicitante && $tramite->solicitante->tipo_persona === 'Moral')
            <!-- Sección de Constitución -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div x-data="{ 
                    isOpen: !isValidated('{{ $revisionesExistentes[3]['estado'] ?? 'pendiente' }}'),
                    estado: '{{ $revisionesExistentes[3]['estado'] ?? 'pendiente' }}'
                }">
                    <div @click="isOpen = !isOpen" 
                         class="p-6 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h2 class="text-xl font-bold text-gray-800">Datos de Constitución</h2>
                                <i class="fas" :class="isOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </div>
                            @if(isset($revisionesExistentes[3]))
                                <span id="estado_seccion_3" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2
                                    @if($revisionesExistentes[3]['estado'] === 'aprobado') bg-green-100 text-green-800
                                    @elseif($revisionesExistentes[3]['estado'] === 'rechazado') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>{{ ucfirst($revisionesExistentes[3]['estado']) }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-6 pt-0">
                        @php
                            $documentosConstitucion = collect($documentosPorSeccion['constitucion'] ?? [])->toArray();
                            $formularioConstitucion = view('components.formularios.seccion-constitucion', [
                                'datosConstitucion' => $datosConstitucion,
                                'readonly' => true
                            ])->render();
                        @endphp

                        <x-revision.comparador-documento
                            :titulo="'Constitución y Acta Constitutiva'"
                            :documento="$documentosConstitucion[0] ?? []"
                            :formulario="$formularioConstitucion"
                            :tramiteId="$tramite->id"
                        />

                        <x-revision.seccion-revision 
                            :seccionId="3"
                            :estado="$revisionesExistentes[3]['estado'] ?? null"
                            :comentario="$revisionesExistentes[3]['comentario'] ?? null"
                            :tramiteId="$tramite->id"
                        />
                    </div>
                </div>
            </div>

            <!-- Sección de Accionistas -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div x-data="{ 
                    isOpen: !isValidated('{{ $revisionesExistentes[4]['estado'] ?? 'pendiente' }}'),
                    estado: '{{ $revisionesExistentes[4]['estado'] ?? 'pendiente' }}'
                }">
                    <div @click="isOpen = !isOpen" 
                         class="p-6 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h2 class="text-xl font-bold text-gray-800">Accionistas</h2>
                                <i class="fas" :class="isOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </div>
                            @if(isset($revisionesExistentes[4]))
                                <span id="estado_seccion_4" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2
                                    @if($revisionesExistentes[4]['estado'] === 'aprobado') bg-green-100 text-green-800
                                    @elseif($revisionesExistentes[4]['estado'] === 'rechazado') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>{{ ucfirst($revisionesExistentes[4]['estado']) }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-6 pt-0">
                        @php
                            $documentosAccionistas = collect($documentosPorSeccion['accionistas'] ?? [])->toArray();
                            $formularioAccionistas = view('components.formularios.seccion-accionistas', [
                                'accionistas' => $datosAccionistas,
                                'readonly' => true
                            ])->render();
                        @endphp

                        <x-revision.comparador-documento
                            :titulo="'Accionistas y Documentación'"
                            :documento="$documentosAccionistas[0] ?? []"
                            :formulario="$formularioAccionistas"
                            :tramiteId="$tramite->id"
                        />

                        <x-revision.seccion-revision 
                            :seccionId="4"
                            :estado="$revisionesExistentes[4]['estado'] ?? null"
                            :comentario="$revisionesExistentes[4]['comentario'] ?? null"
                            :tramiteId="$tramite->id"
                        />
                    </div>
                </div>
            </div>

            <!-- Sección de Apoderado Legal -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div x-data="{ 
                    isOpen: !isValidated('{{ $revisionesExistentes[5]['estado'] ?? 'pendiente' }}'),
                    estado: '{{ $revisionesExistentes[5]['estado'] ?? 'pendiente' }}'
                }">
                    <div @click="isOpen = !isOpen" 
                         class="p-6 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h2 class="text-xl font-bold text-gray-800">Apoderado Legal</h2>
                                <i class="fas" :class="isOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </div>
                            @if(isset($revisionesExistentes[5]))
                                <span id="estado_seccion_5" class="px-3 py-1 text-sm rounded-full flex items-center space-x-2
                                    @if($revisionesExistentes[5]['estado'] === 'aprobado') bg-green-100 text-green-800
                                    @elseif($revisionesExistentes[5]['estado'] === 'rechazado') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    <i class="fas fa-circle text-xs"></i>
                                    <span>{{ ucfirst($revisionesExistentes[5]['estado']) }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-6 pt-0">
                        @php
                            $documentosApoderado = collect($documentosPorSeccion['apoderado'] ?? [])->toArray();
                            $formularioApoderado = view('components.formularios.seccion-apoderado', [
                                'datosApoderado' => $datosApoderado,
                                'readonly' => true
                            ])->render();
                        @endphp

                        <x-revision.comparador-documento
                            :titulo="'Apoderado Legal y Poder Notarial'"
                            :documento="$documentosApoderado[0] ?? []"
                            :formulario="$formularioApoderado"
                            :tramiteId="$tramite->id"
                        />

                        <x-revision.seccion-revision 
                            :seccionId="5"
                            :estado="$revisionesExistentes[5]['estado'] ?? null"
                            :comentario="$revisionesExistentes[5]['comentario'] ?? null"
                            :tramiteId="$tramite->id"
                        />
                    </div>
                </div>
            </div>
            @endif

            <!-- Sección de Documentos -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                <div x-data="{ 
                    isOpen: !isValidated('{{ $revisionesExistentes[6]['estado'] ?? 'pendiente' }}'),
                    estado: '{{ $revisionesExistentes[6]['estado'] ?? 'pendiente' }}'
                }">
                    <!-- Encabezado desplegable -->
                    <div @click="isOpen = !isOpen" 
                         class="p-6 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <h2 class="text-xl font-bold text-gray-800">Documentos</h2>
                                <i class="fas" :class="isOpen ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
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

                    <!-- Contenido desplegable -->
                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-6 pt-0">
                        
                        <!-- Contenedor de la sección de documentos -->
                        <div class="space-y-6">
                            @include('components.formularios.seccion-documentos', [
                                'title' => 'Documentos Requeridos',
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
                                'en_revision' => true
                            ])

                            @if(!empty($documentosPorSeccion['documentos']))
                                <div class="mt-6">
                                    <x-revision.comparador-documento
                                        :titulo="'Documentos Generales'"
                                        :documento="$documentosPorSeccion['documentos'][0] ?? []"
                                        :tramiteId="$tramite->id"
                                    />
                                </div>
                            @endif
                        </div>

                        <!-- Sección de revisión -->
                        <x-revision.seccion-revision 
                            :seccionId="6"
                            :estado="$revisionesExistentes[6]['estado'] ?? 'pendiente'"
                            :observaciones="$revisionesExistentes[6]['observaciones'] ?? ''"
                            :tramiteId="$tramite->id"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Prevenir parpadeo de Alpine.js durante la inicialización */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Asegurar que el modal esté completamente oculto por defecto */
        .modal-hidden {
            display: none !important;
            opacity: 0;
            visibility: hidden;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function agendarCitaData() {
            return {
                // Asegurar que el modal esté oculto desde el inicio
                showAgendarCitaModal: false,
                isLoadingCita: false,
                fechaCita: '',
                motivoCita: 'Cotejo físico de documentos - Trámite completo',
                notasCita: '',
                minDateTime: '',
                error: null,
                success: null,
                initialized: false,
                
                init() {
                    // Asegurar que el modal esté oculto al inicializar
                    this.showAgendarCitaModal = false;
                    
                    // Establecer fecha mínima (mañana)
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    tomorrow.setHours(8, 0, 0, 0); // 8:00 AM
                    this.minDateTime = tomorrow.toISOString().slice(0, 16);
                    
                    // Marcar como inicializado después de un pequeño delay
                    setTimeout(() => {
                        this.initialized = true;
                    }, 100);
                },

                resetCitaForm() {
                    this.fechaCita = '';
                    this.notasCita = '';
                    this.motivoCita = 'Cotejo físico de documentos - Trámite completo';
                    this.error = null;
                    this.success = null;
                    // Asegurar que el modal esté cerrado
                    this.showAgendarCitaModal = false;
                },

                async agendarCita() {
                    if (this.isLoadingCita) return;
                    
                    this.isLoadingCita = true;
                    this.error = null;
                    this.success = null;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch(`/revision/{{ $tramite->id }}/agendar-cita`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                fecha_hora: this.fechaCita,
                                motivo: this.motivoCita,
                                notas: this.notasCita,
                                general: true
                            })
                        });

                        const data = await response.json();
                        
                        if (response.ok && data.success) {
                            // Éxito - cita agendada correctamente
                            this.success = 'Cita agendada correctamente para el cotejo presencial del trámite';
                            this.showAgendarCitaModal = false;
                            this.resetCitaForm();
                            
                            // Mostrar notificación de éxito
                            setTimeout(() => {
                                this.success = null;
                            }, 5000);
                        } else if (response.ok && !data.success && data.error_type === 'validation') {
                            // Error de validación - mostrar solo en interfaz (sin logs en consola)
                            this.error = data.message || 'Error de validación al agendar la cita';
                        } else {
                            // Error del servidor - mostrar en interfaz y console
                            this.error = data.message || 'Error interno del servidor al agendar la cita';
                            console.error('Error del servidor al agendar cita:', {
                                status: response.status,
                                message: data.message,
                                response: data
                            });
                        }

                    } catch (error) {
                        // Error de red o parsing - mostrar en interfaz y console
                        this.error = 'Error de conexión al agendar la cita. Por favor, intente nuevamente.';
                        console.error('Error de red al agendar cita:', error);
                    } finally {
                        this.isLoadingCita = false;
                    }
                }
            };
        }
    </script>
    @endpush
@endsection 