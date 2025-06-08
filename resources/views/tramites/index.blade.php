@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm" x-data="{ success: false, error: false }">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="bg-white rounded-xl shadow-lg border-l-4 border-[#9d2449] p-4 mb-2">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <p class="text-sm font-medium text-gray-800">{{ session('success') }}</p>
                    </div>
                    </div>
            @endif
            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="bg-white rounded-xl shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-800">{{ session('error') }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Encabezado Compacto -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg p-4 mb-5 border border-gray-200/80">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                    <svg class="w-6 h-6 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                        Trámites Disponibles
                    </h1>
                    <p class="text-xs text-gray-500">Seleccione su método de consulta</p>
                </div>
            </div>
        </div>
        
        <!-- Contenedor Principal con Tabs Mejorados -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80" x-data="{ activeTab: 'rfc' }">
            <!-- Tabs Compactos y Modernos -->
            <div class="p-3">
                <div class="bg-gray-100/80 p-1 rounded-lg flex gap-1">
                    <button @click="activeTab = 'rfc'" 
                            :class="activeTab === 'rfc' ? 'tab-active text-white' : 'tab-inactive text-gray-600'"
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="font-semibold">Consulta RFC</span>
                    </button>
                    <button @click="activeTab = 'constancia'" 
                            :class="activeTab === 'constancia' ? 'tab-active text-white' : 'tab-inactive text-gray-600'"
                            class="tab-button flex-1 px-4 py-2 text-sm font-medium rounded-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="font-semibold">Cargar Constancia</span>
                    </button>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-5 pt-3">
                <!-- Búsqueda RFC -->
                <div x-show="activeTab === 'rfc'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-4"
                     class="space-y-4">
                    
                    <div class="bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-4">
                        <div class="flex items-center gap-2.5 mb-4">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-base font-semibold text-gray-800">Consulta por RFC</h2>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="relative">
                                <input type="text" 
                                       id="rfcSearchInput"
                                       placeholder="XAXX010101000" 
                                       class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 transition-all duration-300 text-gray-700 text-sm font-mono bg-white/90 backdrop-blur-sm"
                                       pattern="^[A-ZÑ&]{3,4}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]$">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2v6a2 2 0 01-2 2h-6a2 2 0 01-2-2V9a2 2 0 012-2m0 0V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-1.5">
                                    <span>Consultar RFC</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carga Constancia -->
                <div x-show="activeTab === 'constancia'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-4"
                     class="space-y-4"
                     x-data="{ fileName: '', isDragging: false }">
                    
                    <div class="bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/20 p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-2 shadow-md">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Cargar Constancia</h2>
                        </div>

                        <div class="space-y-4">
                            <!-- Drop Zone -->
                            <div :class="isDragging ? 'border-[#9d2449] bg-[#9d2449]/5 scale-105' : 'border-gray-300 hover:border-[#9d2449]'"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave="isDragging = false"
                                 @drop.prevent="isDragging = false"
                                 class="border-2 border-dashed transition-all duration-300 rounded-xl p-8 text-center relative bg-white/50 hover:bg-white/80 cursor-pointer group">
                                <input type="file" 
                                       id="documentInput" 
                                       name="document" 
                                       accept=".pdf,.png,.jpg,.jpeg" 
                                       @change="fileName = $event.target.files[0]?.name || ''"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                
                                <div class="space-y-3">
                                    <div class="bg-[#9d2449]/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto group-hover:bg-[#9d2449]/20 transition-colors duration-300">
                                        <svg class="w-8 h-8 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-[#9d2449]" x-text="fileName ? 'Archivo seleccionado' : 'Arrastra tu constancia aquí'"></p>
                                        <p class="text-sm text-gray-500 mt-1" x-text="fileName || 'o haz clic para seleccionar'"></p>
                                        <p class="text-xs text-gray-400 mt-2">PDF, PNG, JPG • Máx. 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <button class="flex-1 bg-gradient-to-r from-[#9d2449] to-[#7a1d37] text-white px-6 py-3.5 rounded-xl font-semibold hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 flex items-center justify-center gap-2 hover:shadow-lg hover:-translate-y-0.5 active:transform active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Procesar Documento</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor de Servicios -->
        <div class="mt-6 bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-gray-200/80">
            <div class="p-5">
                <!-- Encabezado del contenedor -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-2.5 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                            Servicios Disponibles
                        </h2>
                        <p class="text-sm text-gray-500">Seleccione el tipo de trámite que desea realizar</p>
                    </div>
                </div>

                <!-- Grid de servicios -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Inscripción -->
                    <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-6">
                            <div class="mb-4">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                                Inscripción
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                Registre su primera vez al padrón de contribuyentes con todos los requisitos necesarios.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                                    Nuevo registro
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Renovación -->
                    <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-6">
                            <div class="mb-4">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                                Renovación
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                Renueve su registro existente para mantener vigente su situación fiscal.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                                    Renovar registro
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Actualización -->
                    <div class="group relative bg-gradient-to-br from-white to-gray-50/50 rounded-xl border-2 border-gray-200/50 hover:border-[#9d2449]/30 transition-all duration-300 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-[#7a1d37]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-6">
                            <div class="mb-4">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-[#9d2449] transition-colors duration-300">
                                Actualización
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                Modifique sus datos personales, fiscales o de actividad económica.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                    Modificar datos
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#9d2449] group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-6 bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-4">
                    <div class="flex items-start gap-3">
                        <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md mt-0.5">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-1">Información Importante</h4>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Todos los trámites requieren documentación específica. Asegúrese de tener los documentos necesarios antes de iniciar el proceso. 
                                Para más información, puede consultar nuestra guía de requisitos o contactar con nuestro equipo de soporte.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tab-button {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .tab-active {
        background: linear-gradient(135deg, #9d2449 0%, #7a1d37 100%);
        box-shadow: 0 4px 12px rgba(157, 36, 73, 0.2);
    }
    .tab-inactive {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }
    .tab-inactive:hover {
        background: rgba(157, 36, 73, 0.05);
        transform: translateY(-1px);
    }
    .content-transition {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

@endsection