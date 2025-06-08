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
            <div class="p-4 pt-2">
                <!-- Búsqueda RFC -->
                <div x-show="activeTab === 'rfc'" 
                     class="space-y-3">
                    
                    <div class="bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-3">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Consulta por RFC</h2>
                        </div>

                        <div class="space-y-2">
                            <div class="relative">
                                <input type="text" 
                                       id="rfcSearchInput"
                                       placeholder="XAXX010101000" 
                                       class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/10 transition-all duration-300 text-gray-700 text-sm font-mono bg-white/90 backdrop-blur-sm">
                            </div>

                            <div class="flex justify-end">
                                <button class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-1.5">
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
                     class="space-y-3"
                     x-data="{ fileName: '', isDragging: false }">
                    
                    <div class="bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-xl border border-[#9d2449]/10 p-3">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-lg p-1.5 shadow-md">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-gray-800">Cargar Constancia</h2>
                        </div>

                        <div class="space-y-3">
                            <!-- Drop Zone -->
                            <div :class="isDragging ? 'border-[#9d2449] bg-[#9d2449]/5' : 'border-gray-300 hover:border-[#9d2449]'"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave="isDragging = false"
                                 @drop.prevent="isDragging = false"
                                 class="border-2 border-dashed transition-all duration-300 rounded-xl p-4 text-center relative bg-white/50 hover:bg-white/80 cursor-pointer group">
                                <input type="file" 
                                       id="documentInput" 
                                       name="document" 
                                       accept=".pdf,.png,.jpg,.jpeg" 
                                       @change="fileName = $event.target.files[0]?.name || ''"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                
                                <div class="space-y-2">
                                    <div class="bg-[#9d2449]/10 w-10 h-10 rounded-lg flex items-center justify-center mx-auto group-hover:bg-[#9d2449]/20 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[#9d2449]" x-text="fileName ? 'Archivo seleccionado: ' + fileName : 'Arrastra tu constancia aquí'"></p>
                                        <p class="text-xs text-gray-500" x-text="fileName ? '' : 'o haz clic para seleccionar'"></p>
                                        <p class="text-xs text-gray-400 mt-1">PDF, PNG, JPG • Máx. 5MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incluir el componente de servicios -->
        @include('components.servicios-disponibles')
    </div>
</div>

<!-- Elementos necesarios para QR (ocultos) -->
<div class="hidden">
    <div id="qrResult"></div>
    <canvas id="pdfCanvas"></canvas>
    <div id="qr-file-name"></div>
    <div id="qr-file-status"></div>
    <div id="qr-processing-status"></div>
    <div id="qr-error-message"></div>
</div>

<!-- Botón flotante para ver el modal de nuevo -->
<div id="verSatModalBtn" class="fixed bottom-6 right-6 hidden">
    <button type="button"
            class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#9d2449] to-[#7a1d37] text-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span class="font-medium">Ver Datos SAT</span>
    </button>
</div>

<!-- Incluir componentes -->
@include('components.loading-indicator')
@include('components.modals.sat-data-modal')

<!-- Scripts -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script type="module">
    import { SATHandler } from '/js/modules/sat-handler.js';
    
    // Hacer satHandler disponible globalmente desde el inicio
    window.satHandler = null;
    
    // Configuración específica para index.blade.php
    const config = {
        fileNameElement: 'qr-file-name',
        previewAreaElement: 'qr-preview',
        uploadAreaElement: 'qr-upload-area',
        verDatosBtnElement: 'verDatosBtn'
    };

    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // Inicializar satHandler y asignarlo globalmente
            window.satHandler = new SATHandler(config);
            await window.satHandler.initialize();

            // Configurar eventos
            document.getElementById('documentInput')?.addEventListener('change', async (event) => {
                const file = event.target.files[0];
                if (file) {
                    await window.satHandler.handleFile(file);
                }
            });

            // Configurar el botón para ver el modal
            const verSatModalBtn = document.getElementById('verSatModalBtn');
            if (verSatModalBtn) {
                verSatModalBtn.onclick = () => window.satHandler?.showModal();
            }

            // Configurar cierre del modal y mostrar botón flotante
            window.closeSatModal = () => {
                window.satHandler?.closeModal();
                // Mostrar el botón flotante después de cerrar el modal
                if (verSatModalBtn) {
                    verSatModalBtn.classList.remove('hidden');
                }
            };

            // Cerrar modal al hacer clic fuera
            document.getElementById('satDataModal')?.addEventListener('click', (event) => {
                const modal = event.currentTarget;
                const modalContent = modal.querySelector('.bg-white');
                if (event.target === modal && !modalContent.contains(event.target)) {
                    window.closeSatModal();
                }
            });

        } catch (error) {
            console.error('Error durante la inicialización:', error);
        }
    });
</script>

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