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
                            {{ $tramite->solicitante->razon_social ?? 'Sin información' }} • 
                            RFC: {{ $tramite->solicitante->rfc ?? 'N/A' }}
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

        <!-- Formularios de Revisión -->
        <div class="space-y-8" x-data="{ 
            tipoPersona: 'moral',
            pdfVisible: {},
            sectionStatus: {
                datos_generales: 'pendiente',
                domicilio: 'pendiente', 
                constitucion: 'pendiente',
                accionistas: 'pendiente',
                apoderado: 'pendiente',
                documentos: 'pendiente'
            },
            togglePdf(section) {
                this.pdfVisible[section] = !this.pdfVisible[section];
            },
            getStatusColor(status) {
                return {
                    'pendiente': 'bg-gray-100 text-gray-600',
                    'aprobado': 'bg-green-100 text-green-600', 
                    'rechazado': 'bg-red-100 text-red-600'
                }[status];
            }
        }">

            <!-- 01. Datos Generales -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12" :class="{ 'lg:col-span-6': pdfVisible.datos_generales }">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="bg-white text-[#9d2449] rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">01</span>
                                    <i class="fas fa-user-circle mr-3"></i>
                                    Datos Generales
                                </h2>
                                <div class="flex items-center space-x-2">
                                    <button @click="togglePdf('datos_generales')" 
                                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-colors duration-200"
                                            title="Ver documento PDF">
                                        <i class="fas fa-file-pdf text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('components.formularios.seccion-datos-generales', [
                                'datosTramite' => $datosTramite,
                                'datosSolicitante' => isset($solicitante) ? [
                                    'rfc' => $solicitante->rfc ?? $datosTramite['rfc'] ?? '',
                                    'curp' => $solicitante->curp ?? $datosTramite['curp'] ?? '',
                                    'tipo_persona' => $solicitante->tipo_persona ?? $datosTramite['tipo_persona'] ?? 'Física',
                                    'nombre_completo' => $solicitante->nombre_completo ?? $datosTramite['nombre_completo'] ?? '',
                                    'razon_social' => $solicitante->razon_social ?? $datosTramite['razon_social'] ?? '',
                                    'objeto_social' => $solicitante->objeto_social ?? $datosTramite['objeto_social'] ?? ''
                                ] : []
                            ])
                            
                            <!-- Panel de revisión por sección -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">Estado de Revisión</h4>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                          :class="getStatusColor(sectionStatus.datos_generales)"
                                          x-text="sectionStatus.datos_generales === 'pendiente' ? 'Pendiente' : 
                                                  sectionStatus.datos_generales === 'aprobado' ? 'Aprobado' : 'Rechazado'">
                                    </span>
                                </div>
                                <textarea placeholder="Comentarios sobre datos generales..." 
                                          class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 mb-3" 
                                          rows="2"></textarea>
                                <div class="flex space-x-2">
                                    <button @click="sectionStatus.datos_generales = 'aprobado'" 
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        <i class="fas fa-check mr-1"></i>Aprobar
                                    </button>
                                    <button @click="sectionStatus.datos_generales = 'rechazado'" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                        <i class="fas fa-times mr-1"></i>Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Viewer para Datos Generales -->
                <div class="col-span-6" x-show="pdfVisible.datos_generales" x-transition>
                    <div class="bg-gray-100 rounded-2xl h-full min-h-[600px] flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-file-pdf text-4xl mb-4"></i>
                            <p>Documentos de Datos Generales</p>
                            <p class="text-sm">PDF se cargará aquí</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 02. Domicilio -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12" :class="{ 'lg:col-span-6': pdfVisible.domicilio }">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="bg-white text-[#9d2449] rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">02</span>
                                    <i class="fas fa-map-marker-alt mr-3"></i>
                                    Domicilio
                                </h2>
                                <button @click="togglePdf('domicilio')" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-colors duration-200"
                                        title="Ver documento PDF">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('components.formularios.seccion-domicilio', [
                                'datosDomicilio' => $datosDomicilio,
                                'datosSAT' => $datosSAT
                            ])
                            
                            <!-- Panel de revisión por sección -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">Estado de Revisión</h4>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                          :class="getStatusColor(sectionStatus.domicilio)"
                                          x-text="sectionStatus.domicilio === 'pendiente' ? 'Pendiente' : 
                                                  sectionStatus.domicilio === 'aprobado' ? 'Aprobado' : 'Rechazado'">
                                    </span>
                                </div>
                                <textarea placeholder="Comentarios sobre domicilio..." 
                                          class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 mb-3" 
                                          rows="2"></textarea>
                                <div class="flex space-x-2">
                                    <button @click="sectionStatus.domicilio = 'aprobado'" 
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        <i class="fas fa-check mr-1"></i>Aprobar
                                    </button>
                                    <button @click="sectionStatus.domicilio = 'rechazado'" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                        <i class="fas fa-times mr-1"></i>Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Viewer para Domicilio -->
                <div class="col-span-6" x-show="pdfVisible.domicilio" x-transition>
                    <div class="bg-gray-100 rounded-2xl h-full min-h-[600px] flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-file-pdf text-4xl mb-4"></i>
                            <p>Documentos de Domicilio</p>
                            <p class="text-sm">PDF se cargará aquí</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secciones para Persona Moral solamente -->
            <template x-if="tipoPersona === 'moral'">
                <div class="space-y-8">
                    <!-- 03. Constitución -->
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12" :class="{ 'lg:col-span-6': pdfVisible.constitucion }">
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <span class="bg-white text-[#9d2449] rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">03</span>
                                            <i class="fas fa-gavel mr-3"></i>
                                            Constitución
                                        </h2>
                                        <button @click="togglePdf('constitucion')" 
                                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-colors duration-200"
                                                title="Ver documento PDF">
                                            <i class="fas fa-file-pdf text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @include('components.formularios.seccion-constitucion')
                                    
                                    <!-- Panel de revisión por sección -->
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-700">Estado de Revisión</h4>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                                  :class="getStatusColor(sectionStatus.constitucion)"
                                                  x-text="sectionStatus.constitucion === 'pendiente' ? 'Pendiente' : 
                                                          sectionStatus.constitucion === 'aprobado' ? 'Aprobado' : 'Rechazado'">
                                            </span>
                                        </div>
                                        <textarea placeholder="Comentarios sobre constitución..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 mb-3" 
                                                  rows="2"></textarea>
                                        <div class="flex space-x-2">
                                            <button @click="sectionStatus.constitucion = 'aprobado'" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                            <button @click="sectionStatus.constitucion = 'rechazado'" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Viewer para Constitución -->
                        <div class="col-span-6" x-show="pdfVisible.constitucion" x-transition>
                            <div class="bg-gray-100 rounded-2xl h-full min-h-[600px] flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-file-pdf text-4xl mb-4"></i>
                                    <p>Documentos de Constitución</p>
                                    <p class="text-sm">PDF se cargará aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 04. Accionistas -->
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12" :class="{ 'lg:col-span-6': pdfVisible.accionistas }">
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <span class="bg-white text-[#9d2449] rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">04</span>
                                            <i class="fas fa-users mr-3"></i>
                                            Accionistas
                                        </h2>
                                        <button @click="togglePdf('accionistas')" 
                                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-colors duration-200"
                                                title="Ver documento PDF">
                                            <i class="fas fa-file-pdf text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @include('components.formularios.seccion-accionistas')
                                    
                                    <!-- Panel de revisión por sección -->
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-700">Estado de Revisión</h4>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                                  :class="getStatusColor(sectionStatus.accionistas)"
                                                  x-text="sectionStatus.accionistas === 'pendiente' ? 'Pendiente' : 
                                                          sectionStatus.accionistas === 'aprobado' ? 'Aprobado' : 'Rechazado'">
                                            </span>
                                        </div>
                                        <textarea placeholder="Comentarios sobre accionistas..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 mb-3" 
                                                  rows="2"></textarea>
                                        <div class="flex space-x-2">
                                            <button @click="sectionStatus.accionistas = 'aprobado'" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                            <button @click="sectionStatus.accionistas = 'rechazado'" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Viewer para Accionistas -->
                        <div class="col-span-6" x-show="pdfVisible.accionistas" x-transition>
                            <div class="bg-gray-100 rounded-2xl h-full min-h-[600px] flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-file-pdf text-4xl mb-4"></i>
                                    <p>Documentos de Accionistas</p>
                                    <p class="text-sm">PDF se cargará aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 05. Apoderado Legal -->
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12" :class="{ 'lg:col-span-6': pdfVisible.apoderado }">
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                                <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-xl font-bold text-white flex items-center">
                                            <span class="bg-white text-[#9d2449] rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">05</span>
                                            <i class="fas fa-user-tie mr-3"></i>
                                            Apoderado Legal
                                        </h2>
                                        <button @click="togglePdf('apoderado')" 
                                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-colors duration-200"
                                                title="Ver documento PDF">
                                            <i class="fas fa-file-pdf text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-6">
                                    @include('components.formularios.seccion-apoderado')
                                    
                                    <!-- Panel de revisión por sección -->
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-700">Estado de Revisión</h4>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                                  :class="getStatusColor(sectionStatus.apoderado)"
                                                  x-text="sectionStatus.apoderado === 'pendiente' ? 'Pendiente' : 
                                                          sectionStatus.apoderado === 'aprobado' ? 'Aprobado' : 'Rechazado'">
                                            </span>
                                        </div>
                                        <textarea placeholder="Comentarios sobre apoderado legal..." 
                                                  class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 mb-3" 
                                                  rows="2"></textarea>
                                        <div class="flex space-x-2">
                                            <button @click="sectionStatus.apoderado = 'aprobado'" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                                <i class="fas fa-check mr-1"></i>Aprobar
                                            </button>
                                            <button @click="sectionStatus.apoderado = 'rechazado'" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i>Rechazar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- PDF Viewer para Apoderado -->
                        <div class="col-span-6" x-show="pdfVisible.apoderado" x-transition>
                            <div class="bg-gray-100 rounded-2xl h-full min-h-[600px] flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-file-pdf text-4xl mb-4"></i>
                                    <p>Documentos de Apoderado Legal</p>
                                    <p class="text-sm">PDF se cargará aquí</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- 03/06. Documentos (número cambia según tipo de persona) -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12" :class="{ 'lg:col-span-6': pdfVisible.documentos }">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-[#9d2449] to-[#7a1d37] px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <span class="bg-white text-[#9d2449] rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3" 
                                          x-text="tipoPersona === 'fisica' ? '03' : '06'">06</span>
                                    <i class="fas fa-file-alt mr-3"></i>
                                    Documentos
                                </h2>
                                <button @click="togglePdf('documentos')" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-lg transition-colors duration-200"
                                        title="Ver documento PDF">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('components.formularios.seccion-documentos')
                            
                            <!-- Panel de revisión por sección -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">Estado de Revisión</h4>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                          :class="getStatusColor(sectionStatus.documentos)"
                                          x-text="sectionStatus.documentos === 'pendiente' ? 'Pendiente' : 
                                                  sectionStatus.documentos === 'aprobado' ? 'Aprobado' : 'Rechazado'">
                                    </span>
                                </div>
                                <textarea placeholder="Comentarios sobre documentos..." 
                                          class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20 mb-3" 
                                          rows="2"></textarea>
                                <div class="flex space-x-2">
                                    <button @click="sectionStatus.documentos = 'aprobado'" 
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        <i class="fas fa-check mr-1"></i>Aprobar
                                    </button>
                                    <button @click="sectionStatus.documentos = 'rechazado'" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                        <i class="fas fa-times mr-1"></i>Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PDF Viewer para Documentos -->
                <div class="col-span-6" x-show="pdfVisible.documentos" x-transition>
                    <div class="bg-gray-100 rounded-2xl h-full min-h-[600px] flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-file-pdf text-4xl mb-4"></i>
                            <p>Documentos Adjuntos</p>
                            <p class="text-sm">PDF se cargará aquí</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Comentarios Generales de Revisión -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-8 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-comments text-[#9d2449] mr-2"></i>
                Comentarios Generales de Revisión
            </h3>
            
            <div class="space-y-4">
                <!-- Área de comentarios -->
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 min-h-[120px]">
                    <p class="text-sm text-gray-500 italic">No hay comentarios generales aún...</p>
                </div>
                
                <!-- Nuevo comentario -->
                <div>
                    <label for="comentario_general" class="block text-sm font-medium text-gray-700 mb-2">
                        Agregar Comentario General
                    </label>
                    <textarea id="comentario_general" rows="4" 
                              class="w-full rounded-lg border-gray-300 focus:border-[#9d2449] focus:ring focus:ring-[#9d2449]/20"
                              placeholder="Escriba sus observaciones generales sobre este trámite..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Guardar Borrador
                    </button>
                    <button class="px-4 py-2 bg-[#9d2449] text-white rounded-lg hover:bg-[#7a1d37] transition-colors duration-200">
                        Agregar Comentario
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