@extends('layouts.app')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header de Revisión -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('revision.index') }}" 
                       class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors duration-200">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-[#9d2449] to-[#7a1d37] text-white shadow-md">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Revisión de Trámite #{{ str_pad($tramite->id, 6, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ ucfirst($tramite->tipo_tramite) }} • 
                            {{ $tramite->solicitante->razon_social ?? $tramite->solicitante->nombre_completo ?? 'Sin información' }} • 
                            RFC: {{ $tramite->solicitante->rfc ?? 'N/A' }} • 
                            Tipo: {{ $tramite->solicitante->tipo_persona ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                
                <!-- Estado y Acciones -->
                <div class="flex items-center space-x-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        {{ $tramite->estado == 'Pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                           ($tramite->estado == 'En Revision' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ $tramite->estado }}
                    </span>
                    
                    <!-- Botones de Acción -->
                    <div class="flex items-center space-x-2">
                        <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-check mr-2"></i>Aprobar Todo
                        </button>
                        <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Rechazar Todo
                        </button>
                        <button class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors duration-200">
                            <i class="fas fa-pause mr-2"></i>Pausar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progreso del Trámite -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progreso del Trámite</span>
                    <span class="text-sm text-gray-600">{{ $tramite->progreso_tramite }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] h-3 rounded-full transition-all duration-300" 
                         style="width: {{ $tramite->progreso_tramite }}%"></div>
                </div>
            </div>
        </div>

        <!-- Mensaje de Error (si existe) -->
        @if(isset($error))
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error al cargar datos</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $error }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formularios de Revisión -->
        <div class="space-y-6" x-data="{ 
            tipoPersona: '{{ strtolower($tramite->solicitante->tipo_persona ?? 'fisica') }}',
            esMoral: {{ ($tramite->solicitante->tipo_persona ?? 'Física') === 'Moral' ? 'true' : 'false' }},
            pdfVisible: {},
            sectionStatus: {
                datos_generales: 'pendiente',
                domicilio: 'pendiente', 
                constitucion: 'pendiente',
                accionistas: 'pendiente',
                apoderado: 'pendiente',
                documentos: 'pendiente'
            },
            sectionComments: {
                datos_generales: '',
                domicilio: '', 
                constitucion: '',
                accionistas: '',
                apoderado: '',
                documentos: ''
            },
            togglePdf(section) {
                this.pdfVisible[section] = !this.pdfVisible[section];
            },
            getStatusColor(status) {
                return {
                    'pendiente': 'bg-amber-50 text-amber-700 border-amber-200',
                    'aprobado': 'bg-emerald-50 text-emerald-700 border-emerald-200', 
                    'rechazado': 'bg-rose-50 text-rose-700 border-rose-200'
                }[status];
            },
            getStatusIcon(status) {
                return {
                    'pendiente': 'fas fa-clock text-amber-500',
                    'aprobado': 'fas fa-check-circle text-emerald-500', 
                    'rechazado': 'fas fa-times-circle text-rose-500'
                }[status];
            },
            approveSection(section) {
                this.sectionStatus[section] = 'aprobado';
                this.$nextTick(() => {
                    // Animación de éxito
                    const element = document.querySelector(`[data-section='${section}']`);
                    if (element) {
                        element.classList.add('animate-pulse');
                        setTimeout(() => element.classList.remove('animate-pulse'), 1000);
                    }
                });
            },
            rejectSection(section) {
                this.sectionStatus[section] = 'rechazado';
                this.$nextTick(() => {
                    // Enfocar en el textarea de comentarios
                    const textarea = document.querySelector(`[data-comment='${section}']`);
                    if (textarea) textarea.focus();
                });
            }
        }">

            <!-- 01. Datos Generales -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6" data-section="datos_generales">
                <div class="xl:col-span-12" :class="{ 'xl:col-span-8': pdfVisible.datos_generales }">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">01</span>
                                    <div class="flex items-center">
                                        <i class="fas fa-user-circle mr-3 text-2xl"></i>
                                        <span>Datos Generales</span>
                                    </div>
                                </h2>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center px-3 py-1.5 rounded-full border text-sm font-medium transition-all duration-200"
                                         :class="getStatusColor(sectionStatus.datos_generales)">
                                        <i :class="getStatusIcon(sectionStatus.datos_generales)" class="mr-2"></i>
                                        <span x-text="sectionStatus.datos_generales === 'pendiente' ? 'Pendiente' : 
                                                      sectionStatus.datos_generales === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
                                    </div>
                                    <button @click="togglePdf('datos_generales')" 
                                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2.5 rounded-xl transition-all duration-200 hover:scale-105"
                                            title="Ver documento PDF">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('components.formularios.seccion-datos-generales', [
                                'datosTramite' => $datosTramite,
                                'datosSolicitante' => $tramite->solicitante ? [
                                    'rfc' => $tramite->solicitante->rfc ?? $datosTramite['rfc'] ?? '',
                                    'curp' => $tramite->solicitante->curp ?? $datosTramite['curp'] ?? '',
                                    'tipo_persona' => $tramite->solicitante->tipo_persona ?? $datosTramite['tipo_persona'] ?? 'Física',
                                    'nombre_completo' => $tramite->solicitante->nombre_completo ?? $datosTramite['nombre_completo'] ?? '',
                                    'razon_social' => $tramite->solicitante->razon_social ?? $datosTramite['razon_social'] ?? '',
                                    'giro' => $tramite->solicitante->giro ?? $datosTramite['giro'] ?? ''
                                ] : [],
                                'readonly' => true
                            ])
                            
                            <!-- Panel de revisión mejorado -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                                            Revisión de Sección
                                        </h4>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-comment-alt mr-1"></i>
                                                Comentarios y Observaciones
                                            </label>
                                            <textarea x-model="sectionComments.datos_generales"
                                                      data-comment="datos_generales"
                                                      placeholder="Escriba sus observaciones sobre los datos generales..." 
                                                      class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 resize-none shadow-sm" 
                                                      rows="3"></textarea>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <button @click="approveSection('datos_generales')" 
                                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <i class="fas fa-check mr-2"></i>Aprobar Sección
                                            </button>
                                            <button @click="rejectSection('datos_generales')" 
                                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl hover:from-rose-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Viewer mejorado -->
                <div class="xl:col-span-4" x-show="pdfVisible.datos_generales" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl h-full min-h-[600px] flex items-center justify-center shadow-inner">
                        <div class="text-center text-gray-500">
                            <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">Documentos de Datos Generales</h3>
                            <p class="text-sm">El visor PDF se cargará aquí</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 02. Domicilio -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6" data-section="domicilio">
                <div class="xl:col-span-12" :class="{ 'xl:col-span-8': pdfVisible.domicilio }">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">02</span>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-3 text-2xl"></i>
                                        <span>Domicilio</span>
                                    </div>
                                </h2>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center px-3 py-1.5 rounded-full border text-sm font-medium transition-all duration-200"
                                         :class="getStatusColor(sectionStatus.domicilio)">
                                        <i :class="getStatusIcon(sectionStatus.domicilio)" class="mr-2"></i>
                                        <span x-text="sectionStatus.domicilio === 'pendiente' ? 'Pendiente' : 
                                                      sectionStatus.domicilio === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
                                    </div>
                                    <button @click="togglePdf('domicilio')" 
                                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2.5 rounded-xl transition-all duration-200 hover:scale-105"
                                            title="Ver documento PDF">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('components.formularios.seccion-domicilio', [
                                'datosDomicilio' => $datosDomicilio,
                                'datosSAT' => $datosSAT,
                                'readonly' => true
                            ])
                            
                            <!-- Panel de revisión mejorado -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                                            Revisión de Sección
                                        </h4>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-comment-alt mr-1"></i>
                                                Comentarios y Observaciones
                                            </label>
                                            <textarea x-model="sectionComments.domicilio"
                                                      data-comment="domicilio"
                                                      placeholder="Escriba sus observaciones sobre el domicilio..." 
                                                      class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 resize-none shadow-sm" 
                                                      rows="3"></textarea>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <button @click="approveSection('domicilio')" 
                                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <i class="fas fa-check mr-2"></i>Aprobar Sección
                                            </button>
                                            <button @click="rejectSection('domicilio')" 
                                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl hover:from-rose-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Viewer mejorado -->
                <div class="xl:col-span-4" x-show="pdfVisible.domicilio" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl h-full min-h-[600px] flex items-center justify-center shadow-inner">
                        <div class="text-center text-gray-500">
                            <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">Documentos de Domicilio</h3>
                            <p class="text-sm">El visor PDF se cargará aquí</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secciones adicionales (Constitución, Accionistas, Apoderado) -->
            <div class="space-y-6">
                    <!-- 03. Constitución -->
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6" data-section="constitucion">
                        <div class="xl:col-span-12" :class="{ 'xl:col-span-8': pdfVisible.constitucion }">
                            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300">
                                <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">03</span>
                                            <div class="flex items-center">
                                                <i class="fas fa-gavel mr-3 text-2xl"></i>
                                                <span>Constitución</span>
                                            </div>
                                        </h2>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center px-3 py-1.5 rounded-full border text-sm font-medium transition-all duration-200"
                                                 :class="getStatusColor(sectionStatus.constitucion)">
                                                <i :class="getStatusIcon(sectionStatus.constitucion)" class="mr-2"></i>
                                                <span x-text="sectionStatus.constitucion === 'pendiente' ? 'Pendiente' : 
                                                              sectionStatus.constitucion === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
                                            </div>
                                            <button @click="togglePdf('constitucion')" 
                                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2.5 rounded-xl transition-all duration-200 hover:scale-105"
                                                    title="Ver documento PDF">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @include('components.formularios.seccion-constitucion', [
                                        'datosConstitucion' => $constitucion,
                                        'readonly' => true
                                    ])
                                    
                                    <!-- Panel de revisión mejorado -->
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-bold text-gray-800 flex items-center">
                                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                                                    Revisión de Sección
                                                </h4>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        <i class="fas fa-comment-alt mr-1"></i>
                                                        Comentarios y Observaciones
                                                    </label>
                                                    <textarea x-model="sectionComments.constitucion"
                                                              data-comment="constitucion"
                                                              placeholder="Escriba sus observaciones sobre la constitución..." 
                                                              class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 resize-none shadow-sm" 
                                                              rows="3"></textarea>
                                                </div>
                                                
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <button @click="approveSection('constitucion')" 
                                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                                    </button>
                                                    <button @click="rejectSection('constitucion')" 
                                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl hover:from-rose-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Viewer mejorado -->
                        <div class="xl:col-span-4" x-show="pdfVisible.constitucion" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl h-full min-h-[600px] flex items-center justify-center shadow-inner">
                                <div class="text-center text-gray-500">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                        <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2">Documentos de Constitución</h3>
                                    <p class="text-sm">El visor PDF se cargará aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 04. Accionistas -->
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6" data-section="accionistas">
                        <div class="xl:col-span-12" :class="{ 'xl:col-span-8': pdfVisible.accionistas }">
                            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300">
                                <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">04</span>
                                            <div class="flex items-center">
                                                <i class="fas fa-users mr-3 text-2xl"></i>
                                                <span>Accionistas</span>
                                            </div>
                                        </h2>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center px-3 py-1.5 rounded-full border text-sm font-medium transition-all duration-200"
                                                 :class="getStatusColor(sectionStatus.accionistas)">
                                                <i :class="getStatusIcon(sectionStatus.accionistas)" class="mr-2"></i>
                                                <span x-text="sectionStatus.accionistas === 'pendiente' ? 'Pendiente' : 
                                                              sectionStatus.accionistas === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
                                            </div>
                                            <button @click="togglePdf('accionistas')" 
                                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2.5 rounded-xl transition-all duration-200 hover:scale-105"
                                                    title="Ver documento PDF">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @include('components.formularios.seccion-accionistas', [
                                        'accionistas' => $accionistas,
                                        'readonly' => true
                                    ])
                                    
                                    <!-- Panel de revisión mejorado -->
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-bold text-gray-800 flex items-center">
                                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                                                    Revisión de Sección
                                                </h4>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        <i class="fas fa-comment-alt mr-1"></i>
                                                        Comentarios y Observaciones
                                                    </label>
                                                    <textarea x-model="sectionComments.accionistas"
                                                              data-comment="accionistas"
                                                              placeholder="Escriba sus observaciones sobre los accionistas..." 
                                                              class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 resize-none shadow-sm" 
                                                              rows="3"></textarea>
                                                </div>
                                                
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <button @click="approveSection('accionistas')" 
                                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                                    </button>
                                                    <button @click="rejectSection('accionistas')" 
                                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl hover:from-rose-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Viewer mejorado -->
                        <div class="xl:col-span-4" x-show="pdfVisible.accionistas" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl h-full min-h-[600px] flex items-center justify-center shadow-inner">
                                <div class="text-center text-gray-500">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                        <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2">Documentos de Accionistas</h3>
                                    <p class="text-sm">El visor PDF se cargará aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 05. Apoderado Legal -->
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6" data-section="apoderado">
                        <div class="xl:col-span-12" :class="{ 'xl:col-span-8': pdfVisible.apoderado }">
                            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300">
                                <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">05</span>
                                            <div class="flex items-center">
                                                <i class="fas fa-user-tie mr-3 text-2xl"></i>
                                                <span>Apoderado Legal</span>
                                            </div>
                                        </h2>
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center px-3 py-1.5 rounded-full border text-sm font-medium transition-all duration-200"
                                                 :class="getStatusColor(sectionStatus.apoderado)">
                                                <i :class="getStatusIcon(sectionStatus.apoderado)" class="mr-2"></i>
                                                <span x-text="sectionStatus.apoderado === 'pendiente' ? 'Pendiente' : 
                                                              sectionStatus.apoderado === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
                                            </div>
                                            <button @click="togglePdf('apoderado')" 
                                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2.5 rounded-xl transition-all duration-200 hover:scale-105"
                                                    title="Ver documento PDF">
                                                <i class="fas fa-file-pdf text-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @include('components.formularios.seccion-apoderado', [
                                        'datosApoderado' => $apoderado,
                                        'readonly' => true
                                    ])
                                    
                                    <!-- Panel de revisión mejorado -->
                                    <div class="mt-8 pt-6 border-t border-gray-200">
                                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-bold text-gray-800 flex items-center">
                                                    <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                                                    Revisión de Sección
                                                </h4>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                                        <i class="fas fa-comment-alt mr-1"></i>
                                                        Comentarios y Observaciones
                                                    </label>
                                                    <textarea x-model="sectionComments.apoderado"
                                                              data-comment="apoderado"
                                                              placeholder="Escriba sus observaciones sobre el apoderado legal..." 
                                                              class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 resize-none shadow-sm" 
                                                              rows="3"></textarea>
                                                </div>
                                                
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <button @click="approveSection('apoderado')" 
                                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                        <i class="fas fa-check mr-2"></i>Aprobar Sección
                                                    </button>
                                                    <button @click="rejectSection('apoderado')" 
                                                            class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl hover:from-rose-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                        <i class="fas fa-times mr-2"></i>Rechazar Sección
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Viewer mejorado -->
                        <div class="xl:col-span-4" x-show="pdfVisible.apoderado" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl h-full min-h-[600px] flex items-center justify-center shadow-inner">
                                <div class="text-center text-gray-500">
                                    <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                        <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                    </div>
                                    <h3 class="font-semibold text-lg mb-2">Documentos de Apoderado Legal</h3>
                                    <p class="text-sm">El visor PDF se cargará aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- 06. Documentos -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6" data-section="documentos">
                <div class="xl:col-span-12" :class="{ 'xl:col-span-8': pdfVisible.documentos }">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">06</span>
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt mr-3 text-2xl"></i>
                                        <span>Documentos</span>
                                    </div>
                                </h2>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center px-3 py-1.5 rounded-full border text-sm font-medium transition-all duration-200"
                                         :class="getStatusColor(sectionStatus.documentos)">
                                        <i :class="getStatusIcon(sectionStatus.documentos)" class="mr-2"></i>
                                        <span x-text="sectionStatus.documentos === 'pendiente' ? 'Pendiente' : 
                                                      sectionStatus.documentos === 'aprobado' ? 'Aprobado' : 'Rechazado'"></span>
                                    </div>
                                    <button @click="togglePdf('documentos')" 
                                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2.5 rounded-xl transition-all duration-200 hover:scale-105"
                                            title="Ver documento PDF">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('components.formularios.seccion-documentos', [
                                'documentos' => $documentos,
                                'readonly' => true
                            ])
                            
                            <!-- Panel de revisión mejorado -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-clipboard-check text-[#9d2449] mr-2"></i>
                                            Revisión de Sección
                                        </h4>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-comment-alt mr-1"></i>
                                                Comentarios y Observaciones
                                            </label>
                                            <textarea x-model="sectionComments.documentos"
                                                      data-comment="documentos"
                                                      placeholder="Escriba sus observaciones sobre los documentos..." 
                                                      class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 resize-none shadow-sm" 
                                                      rows="3"></textarea>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <button @click="approveSection('documentos')" 
                                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <i class="fas fa-check mr-2"></i>Aprobar Sección
                                            </button>
                                            <button @click="rejectSection('documentos')" 
                                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-xl hover:from-rose-600 hover:to-rose-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <i class="fas fa-times mr-2"></i>Rechazar Sección
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Viewer mejorado -->
                <div class="xl:col-span-4" x-show="pdfVisible.documentos" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl h-full min-h-[600px] flex items-center justify-center shadow-inner">
                        <div class="text-center text-gray-500">
                            <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">Documentos Adjuntos</h3>
                            <p class="text-sm">El visor PDF se cargará aquí</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Comentarios Generales de Revisión -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-2xl transition-all duration-300 mt-8">
            <div class="bg-gradient-to-r from-[#9d2449] via-[#8a203f] to-[#7a1d37] px-6 py-5">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <span class="bg-white text-[#9d2449] rounded-full w-10 h-10 flex items-center justify-center text-sm font-bold mr-4 shadow-lg">
                        <i class="fas fa-comments text-lg"></i>
                    </span>
                    <span>Comentarios Generales de Revisión</span>
                </h3>
            </div>
            
            <div class="p-6" x-data="{ comentarios: [], nuevoComentario: '' }">
                <!-- Área de comentarios existentes -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 min-h-[140px] mb-6">
                    <div x-show="comentarios.length === 0" class="flex items-center justify-center h-24">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-comment-slash text-3xl mb-2 opacity-50"></i>
                            <p class="text-sm italic">No hay comentarios generales aún...</p>
                        </div>
                    </div>
                    
                    <template x-for="comentario in comentarios" :key="comentario.id">
                        <div class="bg-white rounded-xl p-4 mb-3 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] rounded-full w-8 h-8 flex items-center justify-center mr-3 shadow-sm">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" x-text="comentario.autor"></p>
                                        <p class="text-xs text-gray-500" x-text="comentario.fecha"></p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">General</span>
                            </div>
                            <p class="text-gray-700 text-sm leading-relaxed" x-text="comentario.texto"></p>
                        </div>
                    </template>
                </div>
                
                <!-- Nuevo comentario -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                    <label for="comentario_general" class="block text-sm font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-plus-circle text-[#9d2449] mr-2"></i>
                        Agregar Comentario General
                    </label>
                    <textarea id="comentario_general" rows="4" 
                              x-model="nuevoComentario"
                              class="w-full rounded-xl border-gray-300 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all duration-200 shadow-sm resize-none"
                              placeholder="Escriba sus observaciones generales sobre este trámite..."></textarea>
                    
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button class="px-6 py-3 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-xl hover:from-gray-500 hover:to-gray-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save mr-2"></i>Guardar Borrador
                        </button>
                        <button @click="comentarios.push({
                                    id: Date.now(),
                                    autor: 'Revisor',
                                    fecha: new Date().toLocaleDateString('es-ES'),
                                    texto: nuevoComentario
                                }); nuevoComentario = ''"
                                :disabled="!nuevoComentario.trim()"
                                :class="nuevoComentario.trim() ? 'bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#6d1a32]' : 'bg-gray-300 cursor-not-allowed'"
                                class="px-6 py-3 text-white rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i>Agregar Comentario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush 