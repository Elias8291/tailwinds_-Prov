@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <!-- Título del Trámite -->
    <div class="max-w-4xl mx-auto mb-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 backdrop-blur-lg border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-[#9d2449] to-[#7a1d37] rounded-xl p-3 shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent animate-shimmer"></div>
                    <svg class="w-6 h-6 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-[#9d2449] to-[#7a1d37] bg-clip-text text-transparent">
                        {{ ucfirst($datosTramite['tipo_tramite'] ?? 'Trámite') }} al Padrón de Proveedores
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Complete el formulario con la información requerida</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="max-w-4xl mx-auto mt-4 sm:mt-8 md:mt-16 bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-8 relative z-10"
         x-data="{ 
            currentStep: 1,
            totalSteps: 0,
            tipoPersona: '',
            isPersonaFisica: false,
            rfc: '',
            curp: '',
            tramiteId: null,
            steps: [],
            async init() {
                // Obtener datos del trámite desde el controlador
                try {
                    const response = await fetch('/tramites-solicitante/datos-tramite');
                    const data = await response.json();
                    
                    this.currentStep = data.paso_inicial || 1;
                    this.tipoPersona = data.tipo_persona;
                    this.isPersonaFisica = data.tipo_persona === 'Física';
                    this.totalSteps = this.isPersonaFisica ? 3 : 6;
                    this.rfc = data.rfc;
                    this.curp = data.curp;
                    this.tramiteId = data.tramite_id;
                    this.steps = this.isPersonaFisica ? 
                        [
                            {number: '01', label: 'Datos Generales'},
                            {number: '02', label: 'Domicilio'},
                            {number: '03', label: 'Documentos'}
                        ] : 
                        [
                            {number: '01', label: 'Datos Generales'},
                            {number: '02', label: 'Domicilio'},
                            {number: '03', label: 'Constitución'},
                            {number: '04', label: 'Accionistas'},
                            {number: '05', label: 'Apoderado Legal'},
                            {number: '06', label: 'Documentos'}
                        ];
                } catch (error) {
                    console.error('Error al cargar datos del trámite:', error);
                }
                
                this.$nextTick(() => {
                    this.$el.classList.remove('invisible');
                });
            }
         }"
         class="invisible">
         
        <!-- Información del Solicitante -->
        <div class="mb-8 bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5 rounded-lg border border-[#9d2449]/10 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="bg-white/80 rounded-lg p-2">
                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500">Tipo de Persona</span>
                        <p class="text-sm font-semibold text-gray-800" x-text="tipoPersona"></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="bg-white/80 rounded-lg p-2">
                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500">RFC</span>
                        <p class="text-sm font-semibold text-gray-800 font-mono" x-text="rfc"></p>
                    </div>
                </div>

                <div class="flex items-center gap-3" x-show="isPersonaFisica && curp" x-cloak>
                    <div class="bg-white/80 rounded-lg p-2">
                        <svg class="w-5 h-5 text-[#9d2449]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500">CURP</span>
                        <p class="text-sm font-semibold text-gray-800 font-mono" x-text="curp"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Progress Indicator -->
        <div class="md:hidden mb-4 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-800 text-white shadow-lg">
                <span class="text-xl font-bold" x-text="currentStep">1</span>
                <span class="text-xs">/</span>
                <span class="text-sm" x-text="totalSteps"></span>
            </div>
            <div class="mt-1 text-xs text-gray-600 font-medium" x-text="steps[currentStep - 1]?.label || ''"></div>
        </div>

        <!-- Desktop Progress Container -->
        <div class="hidden md:block">
            <div class="max-w-3xl mx-auto mb-10 h-[100px] flex flex-col md:flex-row items-center gap-6">
                <!-- Progress Info -->
                <div class="flex flex-col items-center min-w-[80px]">
                    <span class="text-2xl md:text-3xl font-bold text-red-800 h-[36px] flex items-center" x-text="Math.round((currentStep / totalSteps) * 100) + '%'">0%</span>
                    <span class="text-xs uppercase text-gray-500 tracking-wide">Completado</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-2 relative">
                    <div class="h-2 bg-gray-200 rounded-full absolute inset-0">
                        <div class="h-full bg-red-800 rounded-full transition-all duration-500 transform-gpu" x-bind:style="'width: ' + (currentStep / totalSteps * 100) + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- Progress Tracker (Steps) - Only visible on desktop -->
            <div class="relative max-w-3xl mx-auto mb-12 h-[80px]">
                <div class="absolute top-4 left-10 right-10 h-0.5 bg-gray-200"></div>
                <div class="absolute top-4 left-10 h-0.5 bg-red-800 transition-all duration-600 transform-gpu" x-bind:style="'width: ' + (currentStep / totalSteps * 100) + '%'"></div>
                <div class="flex justify-between">
                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex flex-col items-center relative z-10 w-24">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 border-2 border-gray-200 text-gray-500 font-semibold text-sm transition-all duration-300 transform-gpu"
                                 :class="{
                                    'bg-red-800 border-red-800 text-white': currentStep > index + 1,
                                    'bg-red-800 border-red-800 text-white shadow-[0_0_0_3px_rgba(157,36,73,0.2)]': currentStep === index + 1,
                                    'bg-gray-100 border-gray-200 text-gray-500': currentStep < index + 1
                                 }"
                                 @click="if(currentStep > index + 1) currentStep = index + 1">
                                <span x-text="step.number"></span>
                            </div>
                            <span class="mt-2 text-xs text-center text-gray-500 font-medium" x-text="step.label"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Form Sections Container -->
        <div class="min-h-[400px] sm:min-h-[500px]">
            <!-- Form Sections -->
            <div class="max-w-3xl mx-auto">
                <!-- Datos Generales -->
                <div x-show="currentStep === 1" x-cloak>
                    @include('components.formularios.seccion-datos-generales', [
                        'datosTramite' => isset($datosTramite) ? $datosTramite : [],
                        'datosSolicitante' => isset($solicitante) ? [
                            'rfc' => $solicitante->rfc ?? $datosTramite['rfc'] ?? '',
                            'curp' => $solicitante->curp ?? $datosTramite['curp'] ?? '',
                            'tipo_persona' => $solicitante->tipo_persona ?? $datosTramite['tipo_persona'] ?? 'Física',
                            'nombre_completo' => $solicitante->nombre_completo ?? $datosTramite['nombre_completo'] ?? '',
                            'razon_social' => $solicitante->razon_social ?? $datosTramite['razon_social'] ?? '',
                            'objeto_social' => $solicitante->objeto_social ?? $datosTramite['objeto_social'] ?? ''
                        ] : [
                            'rfc' => $datosTramite['rfc'] ?? '',
                            'curp' => $datosTramite['curp'] ?? '',
                            'tipo_persona' => $datosTramite['tipo_persona'] ?? 'Física',
                            'nombre_completo' => $datosTramite['nombre_completo'] ?? '',
                            'razon_social' => $datosTramite['razon_social'] ?? '',
                            'objeto_social' => $datosTramite['objeto_social'] ?? ''
                        ]
                    ])
                </div>

                <!-- Domicilio -->
                <div x-show="currentStep === 2" x-cloak @next-step="currentStep++">
                    @include('components.formularios.seccion-domicilio', [
                        'datosDomicilio' => isset($datosDomicilio) ? $datosDomicilio : [],
                        'datosSAT' => isset($datosSAT) ? $datosSAT : null,
                        'datosSolicitante' => [
                            'rfc' => $datosTramite['rfc'] ?? '',
                            'curp' => $datosTramite['curp'] ?? '',
                            'tipo_persona' => $datosTramite['tipo_persona'] ?? 'Física'
                        ]
                    ])
                </div>

                <!-- Constitución - Solo para Persona Moral -->
                <div x-show="currentStep === 3 && isPersonaFisica === false" x-cloak>
                    @include('components.formularios.seccion-constitucion')
                </div>

                <!-- Documentos - Para Persona Física en paso 3, para Moral en paso 6 -->
                <div x-show="(isPersonaFisica === true && currentStep === 3) || (isPersonaFisica === false && currentStep === 6)" x-cloak>
                    @include('components.formularios.seccion-documentos')
                </div>

                <!-- Accionistas - Solo para Persona Moral -->
                <div x-show="isPersonaFisica === false && currentStep === 4" x-cloak>
                    @include('components.formularios.seccion-accionistas')
                </div>

                <!-- Apoderado Legal - Solo para Persona Moral -->
                <div x-show="isPersonaFisica === false && currentStep === 5" x-cloak>
                    @include('components.formularios.seccion-apoderado')
                </div>

                <!-- Navigation Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-3 mt-6">
                    <button type="button" 
                            x-show="currentStep > 1"
                            x-cloak
                            @click="currentStep--"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-gray-600 text-white text-sm sm:text-base rounded-lg hover:bg-gray-700 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        <i class="fas fa-arrow-left mr-1"></i> Anterior
                    </button>
                    <button type="button" 
                            x-show="currentStep < totalSteps"
                            x-cloak
                            @click="currentStep++"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-red-800 text-white text-sm sm:text-base rounded-lg hover:bg-red-900 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        Siguiente <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                    <button type="button" 
                            x-show="currentStep === totalSteps"
                            x-cloak
                            @click="finalizarTramite()"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-red-800 text-white text-sm sm:text-base rounded-lg hover:bg-red-900 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        Finalizar <i class="fas fa-check ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { 
        display: none !important; 
    }
    
    .bg-white {
        background-color: #ffffff !important;
    }
    
    .bg-opacity-90 {
        --tw-bg-opacity: 0.9 !important;
    }

    /* Estilos personalizados para formularios */
    .form-input,
    .form-select,
    .form-textarea {
        @apply w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base;
        @apply border border-gray-200 rounded-lg;
        @apply bg-white;
        @apply transition-all duration-200;
        @apply focus:border-red-800 focus:ring-2 focus:ring-red-800/20;
        @apply placeholder-gray-400;
    }

    /* Contenedor de input con icono */
    .input-icon-container {
        @apply relative flex items-center;
    }

    .input-icon-container i {
        @apply absolute left-3 text-gray-400 pointer-events-none;
        @apply transition-colors duration-200;
    }

    .input-icon-container input,
    .input-icon-container select {
        @apply pl-9;
    }

    .input-icon-container:focus-within i {
        @apply text-red-800;
    }

    /* Estilos para inputs */
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="number"],
    select,
    textarea {
        @apply form-input;
    }

    /* Estilos para labels */
    label {
        @apply text-sm font-medium text-gray-700;
        @apply mb-1 block;
    }

    /* Estilos para botones */
    .btn {
        @apply px-4 sm:px-6 py-2.5 sm:py-3;
        @apply text-sm sm:text-base font-medium;
        @apply rounded-lg;
        @apply transition-all duration-300;
        @apply transform-gpu hover:-translate-y-0.5;
        @apply flex items-center justify-center gap-2;
    }

    .btn-primary {
        @apply bg-red-800 text-white;
        @apply hover:bg-red-900;
        @apply shadow-sm hover:shadow-md;
    }

    .btn-secondary {
        @apply bg-gray-600 text-white;
        @apply hover:bg-gray-700;
        @apply shadow-sm hover:shadow-md;
    }

    /* Mejoras para móvil */
    @media (max-width: 640px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        input, select, textarea {
            font-size: 16px !important;
        }

        .form-section {
            @apply p-4 rounded-lg border border-gray-100;
            @apply bg-white shadow-sm;
        }
    }

    /* Animaciones */
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        @apply transform-gpu scale-[1.01];
    }

    /* Estilos para campos requeridos */
    .required::after {
        content: '*';
        @apply text-red-500 ml-1;
    }

    /* Estilos para mensajes de error */
    .error-message {
        @apply text-xs text-red-500 mt-1;
    }

    /* Estilos para grupos de campos */
    .form-group {
        @apply mb-4 last:mb-0;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite;
    }
    
    /* Mejoras para el contenedor del título */
    .max-w-4xl.mx-auto.mb-6 .bg-white {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .max-w-4xl.mx-auto.mb-6 .bg-white:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(157, 36, 73, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    function finalizarTramite() {
        // Enviar finalización del trámite
        fetch('/tramites-solicitante/finalizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/tramites-solicitante';
            } else {
                alert('Error al finalizar el trámite: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al finalizar el trámite');
        });
    }
</script>
@endpush
@endsection
