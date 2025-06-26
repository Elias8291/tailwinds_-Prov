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
                    
                    this.currentStep = data.paso_inicial || data.progreso_tramite || 1;
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
    
                }
                
                this.$nextTick(() => {
                    this.$el.classList.remove('invisible');
                });
            }
         }"
         class="invisible">
         
        <!-- Contador de Tiempo Límite - Dentro del contenedor -->
        @if(isset($tiempoLimite))
        <div class="mb-6"
             x-data="{
                vencido: {{ $tiempoLimite['vencido'] ? 'true' : 'false' }},
                timestampLimite: {{ $tiempoLimite['timestamp_limite'] ?? 0 }},
                horasRestantes: {{ $tiempoLimite['horas_restantes'] ?? 0 }},
                minutosRestantes: {{ $tiempoLimite['minutos_restantes'] ?? 0 }},
                segundosRestantes: {{ $tiempoLimite['segundos_restantes'] ?? 0 }},
                color: '{{ $tiempoLimite['color'] ?? 'green' }}',
                
                init() {
                    if (!this.vencido && this.timestampLimite > 0) {
                        this.actualizarContador();
                        setInterval(() => {
                            this.actualizarContador();
                        }, 1000);
                    }
                },
                
                actualizarContador() {
                    const ahora = Math.floor(Date.now() / 1000);
                    const diferencia = this.timestampLimite - ahora;
                    
                    if (diferencia <= 0) {
                        this.vencido = true;
                        this.horasRestantes = 0;
                        this.minutosRestantes = 0;
                        this.segundosRestantes = 0;
                        this.color = 'red';
                        return;
                    }
                    
                    this.horasRestantes = Math.floor(diferencia / 3600);
                    this.minutosRestantes = Math.floor((diferencia % 3600) / 60);
                    this.segundosRestantes = diferencia % 60;
                    
                    // Actualizar color según tiempo restante
                    if (this.horasRestantes <= 6) {
                        this.color = 'red';
                    } else if (this.horasRestantes <= 12) {
                        this.color = 'yellow';
                    } else {
                        this.color = 'green';
                    }
                }
             }">
            <div class="rounded-lg p-3 tiempo-limite-container-mini {{ $tiempoLimite['vencido'] ? 'tiempo-vencido' : 'tiempo-color-' . $tiempoLimite['color'] }}">
                <div class="flex items-center justify-between">
                    <!-- Información compacta -->
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg flex-shrink-0" 
                             :class="{
                                'bg-red-500': vencido || color === 'red',
                                'bg-yellow-500': !vencido && color === 'yellow',
                                'bg-blue-500': !vencido && (color === 'blue' || color === 'green')
                             }">
                            <div class="w-4 h-4 text-white relative">
                                <!-- Emoji como icono -->
                                <span class="text-sm" x-show="vencido">⚠️</span>
                                <span class="text-sm" x-show="!vencido && color === 'red'">🔥</span>
                                <span class="text-sm" x-show="!vencido && color === 'yellow'">⚡</span>
                                <span class="text-sm" x-show="!vencido && (color === 'blue' || color === 'green')">⏰</span>
                            </div>
                        </div>
                        
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold truncate" 
                                    :class="{
                                        'text-red-800': vencido || color === 'red',
                                        'text-yellow-800': !vencido && color === 'yellow',
                                        'text-blue-800': !vencido && (color === 'blue' || color === 'green')
                                    }">
                                    <span x-show="vencido">¡Tiempo Vencido!</span>
                                    <span x-show="!vencido && color === 'red'">¡Tiempo Crítico!</span>
                                    <span x-show="!vencido && color === 'yellow'">¡Poco Tiempo!</span>
                                    <span x-show="!vencido && (color === 'blue' || color === 'green')">Tiempo Límite</span>
                                </h4>
                                
                                <!-- Fechas mini -->
                                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded-full">
                                        📅 {{ $tiempoLimite['fecha_limite_corta'] }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-xs mt-0.5" 
                               :class="{
                                    'text-red-600': vencido || color === 'red',
                                    'text-yellow-600': !vencido && color === 'yellow',
                                    'text-blue-600': !vencido && (color === 'blue' || color === 'green')
                                }">
                                <span x-show="vencido">El plazo de 48h ha expirado</span>
                                <span x-show="!vencido">48 horas para completar</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Contador compacto -->
                    <div class="text-right flex-shrink-0">
                        <div x-show="!vencido" class="relative">
                            <!-- Contador principal -->
                            <div class="text-lg sm:text-xl font-bold font-mono tiempo-contador-mini relative" 
                                 :class="{
                                    'text-red-700': color === 'red',
                                    'text-yellow-700': color === 'yellow',
                                    'text-blue-700': color === 'blue' || color === 'green'
                                 }">
                                <div class="flex items-center gap-1">
                                    <div class="text-center">
                                        <div x-text="String(horasRestantes).padStart(2, '0')" class="leading-tight">{{ str_pad($tiempoLimite['horas_restantes'], 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs opacity-70">h</div>
                                    </div>
                                    <div class="text-sm">:</div>
                                    <div class="text-center">
                                        <div x-text="String(minutosRestantes).padStart(2, '0')" class="leading-tight">{{ str_pad($tiempoLimite['minutos_restantes'], 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs opacity-70">m</div>
                                    </div>
                                    <div class="text-sm">:</div>
                                    <div class="text-center">
                                        <div x-text="String(segundosRestantes).padStart(2, '0')" class="leading-tight">{{ str_pad($tiempoLimite['segundos_restantes'], 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-xs opacity-70">s</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Barra de progreso mini -->
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-1 overflow-hidden">
                                <div class="h-full transition-all duration-1000 rounded-full"
                                     :class="{
                                        'bg-red-500': color === 'red',
                                        'bg-yellow-500': color === 'yellow',
                                        'bg-blue-500': color === 'blue' || color === 'green'
                                     }"
                                     :style="'width: ' + (100 - ((horasRestantes * 3600 + minutosRestantes * 60 + segundosRestantes) / (48 * 3600) * 100)) + '%'">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mensaje de vencido compacto -->
                        <div x-show="vencido" class="text-center">
                            <div class="text-lg font-bold text-red-700">
                                💀 VENCIDO
                            </div>
                            <div class="text-xs text-red-500 mt-1">
                                00:00:00
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Mobile Progress Indicator -->
        <div class="md:hidden mb-4 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-800 text-white shadow-lg">
                <span class="text-xl font-bold" x-text="currentStep - 1">0</span>
                <span class="text-xs">/</span>
                <span class="text-sm" x-text="totalSteps - 1"></span>
            </div>
            <div class="mt-1 text-xs text-gray-600 font-medium" x-text="steps[currentStep - 1]?.label || ''"></div>
            <div class="mt-2 text-xs text-gray-500">
                <span x-text="Math.round(((currentStep - 1) / (totalSteps - 1)) * 100) + '%'">0%</span> Completado
            </div>
        </div>

        <!-- Desktop Progress Container -->
        <div class="hidden md:block">
            <div class="max-w-3xl mx-auto mb-10 h-[100px] flex flex-col md:flex-row items-center gap-6">
                <!-- Progress Info -->
                <div class="flex flex-col items-center min-w-[80px]">
                    <span class="text-2xl md:text-3xl font-bold text-red-800 h-[36px] flex items-center" x-text="Math.round(((currentStep - 1) / (totalSteps - 1)) * 100) + '%'">0%</span>
                    <span class="text-xs uppercase text-gray-500 tracking-wide">Completado</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-2 relative">
                    <div class="h-2 bg-gray-200 rounded-full absolute inset-0">
                        <div class="h-full bg-red-800 rounded-full transition-all duration-500 transform-gpu" x-bind:style="'width: ' + ((currentStep - 1) / (totalSteps - 1) * 100) + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- Progress Tracker (Steps) - Only visible on desktop -->
            <div class="relative max-w-3xl mx-auto mb-12 h-[80px]">
                <div class="absolute top-4 left-10 right-10 h-0.5 bg-gray-200"></div>
                <div class="absolute top-4 left-10 h-0.5 bg-red-800 transition-all duration-600 transform-gpu" x-bind:style="'width: ' + ((currentStep - 1) / (totalSteps - 1) * 100) + '%'"></div>
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
                            'giro' => $solicitante->giro ?? $datosTramite['giro'] ?? ''
                        ] : [
                            'rfc' => $datosTramite['rfc'] ?? '',
                            'curp' => $datosTramite['curp'] ?? '',
                            'tipo_persona' => $datosTramite['tipo_persona'] ?? 'Física',
                            'nombre_completo' => $datosTramite['nombre_completo'] ?? '',
                            'razon_social' => $datosTramite['razon_social'] ?? '',
                            'giro' => $datosTramite['giro'] ?? ''
                        ]
                    ])
                </div>

                <!-- Domicilio -->
                <div x-show="currentStep === 2" x-cloak @next-step="currentStep++">
                    @include('components.formularios.seccion-domicilio', [
                        'tramite' => $tramite,
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
                <div x-show="(isPersonaFisica === true && currentStep === 3) || (isPersonaFisica === false && currentStep === 6)" x-cloak @previous-step="currentStep--">
                    @include('components.formularios.seccion-documentos', [
                        'tramite' => $tramite
                    ])
                </div>

                <!-- Accionistas - Solo para Persona Moral -->
                <div x-show="isPersonaFisica === false && currentStep === 4" x-cloak @next-step="currentStep++" @previous-step="currentStep--">
                    @include('components.formularios.seccion-accionistas', [
                        'tramite' => $tramite,
                        'datosAccionistas' => isset($datosAccionistas) ? $datosAccionistas : []
                    ])
                </div>

                <!-- Apoderado Legal - Solo para Persona Moral -->
                <div x-show="isPersonaFisica === false && currentStep === 5" x-cloak @next-step="currentStep++" @previous-step="currentStep--">
                    @include('components.formularios.seccion-apoderado', [
                        'tramite' => $tramite,
                        'datosApoderado' => isset($datosApoderado) ? $datosApoderado : []
                    ])
                </div>

                <!-- Navigation Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-3 mt-6">
                    
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

    /* ✨ NUEVAS ANIMACIONES PARA MODALES ✨ */
    @keyframes fade-in {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes fade-in-delay {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes progress-bar {
        from { 
            width: 0%; 
        }
        to { 
            width: 100%; 
        }
    }

    @keyframes modal-scale-in {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1.2s ease-out forwards;
    }

    .animate-fade-in-delay {
        animation: fade-in-delay 1.2s ease-out 0.6s forwards;
        opacity: 0;
    }

    .animate-progress-bar {
        animation: progress-bar 2.5s ease-out forwards;
    }

    .animate-modal-scale {
        animation: modal-scale-in 0.3s ease-out forwards;
    }

    .animate-celebration {
        animation: celebration-bounce 1.5s ease-in-out infinite;
    }

    /* Efectos de hover para botones del modal */
    .modal-button-hover:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(157, 36, 73, 0.3);
    }

    /* Animación de pulso personalizada */
    @keyframes custom-pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.05);
        }
    }

    .animate-custom-pulse {
        animation: custom-pulse 2s ease-in-out infinite;
    }

    /* Animación de rotación suave */
    @keyframes gentle-spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-gentle-spin {
        animation: gentle-spin 2s linear infinite;
    }

    /* Backdrop blur mejorado */
    .backdrop-blur-custom {
        backdrop-filter: blur(12px) saturate(180%);
        -webkit-backdrop-filter: blur(12px) saturate(180%);
    }

    /* Sombras personalizadas */
    .shadow-custom {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .shadow-custom-xl {
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
    }

    /* ✨ NUEVAS ANIMACIONES PARA PANTALLAS COMPLETAS ✨ */
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounce-in {
        0% {
            opacity: 0;
            transform: scale(0.3) translateY(-100px);
        }
        50% {
            opacity: 1;
            transform: scale(1.05) translateY(0);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes celebration-float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-30px) rotate(5deg);
        }
    }

    @keyframes twinkle {
        0%, 100% {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.2) rotate(180deg);
        }
    }

    @keyframes progress-wave {
        0% {
            width: 0%;
        }
        100% {
            width: 100%;
        }
    }

    @keyframes shimmer-wave {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.8s ease-out forwards;
    }

    .animate-bounce-in {
        animation: bounce-in 1s ease-out forwards;
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    .animate-celebration-float {
        animation: celebration-float 4s ease-in-out infinite;
    }

    .animate-twinkle {
        animation: twinkle 2s ease-in-out infinite;
    }

    .animate-progress-wave {
        animation: progress-wave 3s ease-out forwards;
    }

    .animate-shimmer-wave {
        animation: shimmer-wave 2s ease-in-out infinite;
    }

    /* Mejoras de responsive para pantallas completas */
    @media (max-width: 768px) {
        .text-5xl {
            font-size: 2.5rem;
        }
        .text-6xl {
            font-size: 3rem;
        }
        .text-7xl {
            font-size: 3.5rem;
        }
    }

    /* Efectos de glassmorphism */
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* ⏰ ESTILOS PARA CONTADOR DE TIEMPO LÍMITE - VERSIÓN MINI */
    .tiempo-limite-container-mini {
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }

    .tiempo-vencido {
        background: linear-gradient(135deg, #fee2e2, #fca5a5) !important;
        border-color: #ef4444 !important;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2) !important;
    }

    .tiempo-color-red {
        background: linear-gradient(135deg, #fee2e2, #fca5a5) !important;
        border-color: #ef4444 !important;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2) !important;
    }

    .tiempo-color-yellow {
        background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
        border-color: #f59e0b !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2) !important;
    }

    .tiempo-color-green, .tiempo-color-blue {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2) !important;
    }

    .tiempo-contador-mini {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        letter-spacing: 1px;
    }

    /* Animación de pulso suave para tiempo crítico */
    @keyframes pulso-suave {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.02);
        }
    }

    .tiempo-color-red .tiempo-contador-mini {
        animation: pulso-suave 2s infinite;
    }

    /* Animación de parpadeo para emojis */
    @keyframes parpadeo-emoji {
        0%, 50%, 100% {
            opacity: 1;
        }
        25%, 75% {
            opacity: 0.6;
        }
    }

    .tiempo-color-red .text-sm {
        animation: parpadeo-emoji 1.5s infinite;
    }

    /* Animación sutil para la barra de progreso */
    @keyframes progreso-glow {
        0%, 100% {
            box-shadow: 0 0 3px rgba(59, 130, 246, 0.3);
        }
        50% {
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.6);
        }
    }

    .tiempo-limite-container-mini .bg-blue-500 {
        animation: progreso-glow 3s infinite;
    }

    .tiempo-limite-container-mini .bg-yellow-500 {
        animation: progreso-glow 3s infinite;
        box-shadow: 0 0 3px rgba(245, 158, 11, 0.3);
    }

    .tiempo-limite-container-mini .bg-red-500 {
        animation: progreso-glow 2s infinite;
        box-shadow: 0 0 3px rgba(239, 68, 68, 0.3);
    }

    /* Responsive para móvil - versión mini */
    @media (max-width: 640px) {
        .tiempo-limite-container-mini {
            padding: 0.75rem !important;
        }
        
        .tiempo-limite-container-mini .flex {
            gap: 0.5rem;
        }
        
        .tiempo-contador-mini {
            font-size: 1.1rem !important;
        }
        
        .tiempo-limite-container-mini .hidden {
            display: none !important;
        }
    }

    /* Hover effects para hacer más interactivo */
    .tiempo-limite-container-mini:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Estilo especial para la fecha compacta */
    .tiempo-limite-container-mini .bg-gray-100 {
        background: rgba(243, 244, 246, 0.8) !important;
        backdrop-filter: blur(5px);
        transition: all 0.2s ease;
    }

    .tiempo-limite-container-mini .bg-gray-100:hover {
        background: rgba(229, 231, 235, 0.9) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function finalizarTramite() {
        // Crear modal de confirmación personalizado
        mostrarModalConfirmacion();
    }

    function mostrarModalConfirmacion() {
        // Crear el modal
        const modal = document.createElement('div');
        modal.id = 'confirmModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                <div class="p-8 text-center">
                    <!-- Icono -->
                    <div class="mx-auto mb-6 w-20 h-20 bg-gradient-to-br from-[#9d2449] to-[#8a203f] rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        ¿Finalizar Trámite?
                    </h3>
                    
                    <!-- Descripción -->
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Su trámite será enviado para revisión por nuestro equipo especializado. 
                        Una vez enviado, no podrá realizar cambios hasta que sea revisado.
                    </p>
                    
                    <!-- Botones -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="cerrarModal()" 
                                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button onclick="confirmarEnvio()" 
                                class="px-6 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Trámite
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Animar entrada
        setTimeout(() => {
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function cerrarModal() {
        const modal = document.getElementById('confirmModal');
        const content = document.getElementById('modalContent');
        
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        
        setTimeout(() => {
            if (modal) {
                document.body.removeChild(modal);
            }
        }, 300);
    }

    function confirmarEnvio() {
        // Cerrar modal de confirmación
        cerrarModal();
        
        // Mostrar modal de envío
        setTimeout(() => {
            mostrarModalEnvio();
        }, 300);
        
        // Enviar el trámite
        enviarTramite();
    }

    function mostrarModalEnvio() {
        const overlay = document.createElement('div');
        overlay.id = 'sendingOverlay';
        overlay.className = 'fixed inset-0 z-50';
        overlay.innerHTML = `
            <!-- Fondo animado con gradiente -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449] via-[#8a203f] to-[#7a1d37]">
                <!-- Partículas flotantes -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white bg-opacity-10 rounded-full blur-xl animate-pulse"></div>
                    <div class="absolute top-3/4 right-1/4 w-24 h-24 bg-white bg-opacity-5 rounded-full blur-lg animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-1/4 left-1/3 w-40 h-40 bg-white bg-opacity-5 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s"></div>
                </div>
                
                <!-- Patrón geométrico sutil -->
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="relative z-10 flex items-center justify-center min-h-screen p-6">
                <div class="text-center max-w-lg mx-auto">
                    
                    <!-- Logo/Imagen principal con efectos -->
                    <div class="relative mb-12">
                        <div class="relative">
                            <!-- Anillos concéntricos animados -->
                            <div class="absolute inset-0 rounded-full border-4 border-white border-opacity-20 animate-ping"></div>
                            <div class="absolute inset-4 rounded-full border-2 border-white border-opacity-30 animate-ping" style="animation-delay: 0.5s"></div>
                            <div class="absolute inset-8 rounded-full border border-white border-opacity-40 animate-ping" style="animation-delay: 1s"></div>
                            
                            <!-- Imagen principal -->
                            <div class="relative w-48 h-48 mx-auto bg-white bg-opacity-20 backdrop-blur-sm rounded-3xl shadow-2xl p-6 animate-float">
                                <img src="{{ asset('images/exito-elias.png') }}" 
                                     alt="Procesando" 
                                     class="w-full h-full object-contain rounded-2xl">
                                
                                <!-- Indicador de carga giratorio -->
                                <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-white rounded-full shadow-xl flex items-center justify-center animate-spin">
                                    <div class="w-8 h-8 border-4 border-[#9d2449] border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Título principal -->
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 animate-fade-in-up tracking-tight">
                        Procesando...
                    </h1>
                    
                    <!-- Subtítulo -->
                    <p class="text-xl md:text-2xl text-white text-opacity-90 mb-8 animate-fade-in-up font-light leading-relaxed" style="animation-delay: 0.3s">
                        Enviando su trámite para revisión
                    </p>
                    
                    <!-- Barra de progreso elegante -->
                    <div class="mb-12 animate-fade-in-up" style="animation-delay: 0.6s">
                        <div class="relative w-full max-w-md mx-auto">
                            <!-- Fondo de la barra -->
                            <div class="h-2 bg-white bg-opacity-20 rounded-full shadow-inner backdrop-blur-sm">
                                <!-- Barra de progreso con gradiente -->
                                <div class="h-full bg-gradient-to-r from-white via-blue-100 to-white rounded-full shadow-lg animate-progress-wave relative overflow-hidden">
                                    <!-- Efecto de brillo que se mueve -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white via-transparent opacity-60 animate-shimmer-wave"></div>
                                </div>
                            </div>
                            
                            <!-- Porcentaje -->
                            <div class="text-center mt-4">
                                <span class="text-white text-opacity-80 font-medium">Completando proceso...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicadores de estado -->
                    <div class="flex justify-center items-center space-x-8 mb-8 animate-fade-in-up" style="animation-delay: 0.9s">
                        <!-- Paso 1 -->
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 animate-pulse">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-white text-opacity-70 text-sm">Validando</span>
                        </div>
                        
                        <!-- Paso 2 -->
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 animate-pulse" style="animation-delay: 0.3s">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <span class="text-white text-opacity-70 text-sm">Enviando</span>
                        </div>
                        
                        <!-- Paso 3 -->
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2 animate-pulse" style="animation-delay: 0.6s">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-white text-opacity-70 text-sm">Confirmando</span>
                        </div>
                    </div>
                    
                    <!-- Mensaje de espera -->
                    <div class="text-center animate-fade-in-up" style="animation-delay: 1.2s">
                        <p class="text-white text-opacity-80 text-lg mb-2">Por favor, no cierre esta ventana</p>
                        <p class="text-white text-opacity-60">Este proceso puede tomar unos segundos</p>
                    </div>
                    
                    <!-- Puntos de carga decorativos -->
                    <div class="flex justify-center space-x-3 mt-8 animate-fade-in-up" style="animation-delay: 1.5s">
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce"></div>
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-3 h-3 bg-white bg-opacity-60 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
    }

    function mostrarModalExito(mensaje, redirectUrl) {
        // Remover overlay de envío
        const sendingOverlay = document.getElementById('sendingOverlay');
        if (sendingOverlay) {
            document.body.removeChild(sendingOverlay);
        }
        
        const overlay = document.createElement('div');
        overlay.id = 'successOverlay';
        overlay.className = 'fixed inset-0 z-50';
        overlay.innerHTML = `
            <!-- Fondo celebratorio con gradiente -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700">
                <!-- Confeti animado -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute top-10 left-10 w-4 h-4 bg-yellow-300 rounded-full animate-bounce opacity-80"></div>
                    <div class="absolute top-20 right-20 w-3 h-3 bg-pink-400 rounded-full animate-bounce opacity-70" style="animation-delay: 0.2s"></div>
                    <div class="absolute top-1/3 left-1/4 w-5 h-5 bg-blue-400 rounded-full animate-bounce opacity-60" style="animation-delay: 0.4s"></div>
                    <div class="absolute bottom-1/4 right-1/3 w-4 h-4 bg-purple-400 rounded-full animate-bounce opacity-75" style="animation-delay: 0.6s"></div>
                    <div class="absolute bottom-20 left-20 w-3 h-3 bg-orange-400 rounded-full animate-bounce opacity-80" style="animation-delay: 0.8s"></div>
                    <div class="absolute top-1/2 right-10 w-6 h-6 bg-red-400 rounded-full animate-bounce opacity-70" style="animation-delay: 1s"></div>
                </div>
                
                <!-- Patrón de celebración -->
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="celebration" width="60" height="60" patternUnits="userSpaceOnUse">
                                <circle cx="30" cy="30" r="2" fill="white" opacity="0.3"/>
                                <circle cx="10" cy="10" r="1" fill="white" opacity="0.2"/>
                                <circle cx="50" cy="50" r="1.5" fill="white" opacity="0.4"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#celebration)" />
                    </svg>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="relative z-10 flex items-center justify-center min-h-screen p-6">
                <div class="text-center max-w-2xl mx-auto">
                    
                    <!-- Imagen de éxito con efectos espectaculares -->
                    <div class="relative mb-12">
                        <div class="relative">
                            <!-- Anillos de celebración -->
                            <div class="absolute inset-0 rounded-full border-4 border-white border-opacity-30 animate-ping"></div>
                            <div class="absolute inset-8 rounded-full border-2 border-yellow-300 border-opacity-50 animate-ping" style="animation-delay: 0.3s"></div>
                            <div class="absolute inset-16 rounded-full border border-white border-opacity-40 animate-ping" style="animation-delay: 0.6s"></div>
                            
                            <!-- Imagen principal con marco elegante -->
                            <div class="relative w-56 h-56 mx-auto bg-white bg-opacity-20 backdrop-blur-sm rounded-full shadow-2xl p-8 animate-celebration-float">
                                <img src="{{ asset('images/exito-elias.png') }}" 
                                     alt="¡Éxito!" 
                                     class="w-full h-full object-contain rounded-full">
                                
                                <!-- Badge de éxito grande -->
                                <div class="absolute -top-6 -right-6 w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center shadow-2xl animate-pulse">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                
                                <!-- Estrellas decorativas -->
                                <div class="absolute -top-8 -left-8 text-yellow-300 text-3xl animate-twinkle">✨</div>
                                <div class="absolute -bottom-8 -right-8 text-yellow-300 text-2xl animate-twinkle" style="animation-delay: 0.5s">⭐</div>
                                <div class="absolute -bottom-4 -left-12 text-yellow-300 text-xl animate-twinkle" style="animation-delay: 1s">🌟</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Título de celebración -->
                    <h1 class="text-6xl md:text-7xl font-bold text-white mb-6 animate-bounce-in tracking-tight">
                        ¡Éxito!
                    </h1>
                    
                    <!-- Mensaje principal -->
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 animate-fade-in-up" style="animation-delay: 0.3s">
                        ¡Trámite Enviado!
                    </h2>
                    
                    <!-- Descripción -->
                    <p class="text-xl md:text-2xl text-white text-opacity-90 mb-8 animate-fade-in-up font-light leading-relaxed" style="animation-delay: 0.6s">
                        ${mensaje}
                    </p>
                    
                    <!-- Tarjeta informativa elegante -->
                    <div class="bg-white bg-opacity-20 backdrop-blur-md rounded-2xl p-6 mb-8 shadow-xl animate-fade-in-up" style="animation-delay: 0.9s">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 bg-opacity-80 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="text-white font-semibold text-lg">¿Qué sigue?</h3>
                                <p class="text-white text-opacity-80">Será redirigido al estado de su trámite</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contador regresivo elegante -->
                    <div class="text-center animate-fade-in-up" style="animation-delay: 1.2s">
                        <div class="inline-flex items-center bg-white bg-opacity-20 backdrop-blur-md rounded-full px-6 py-3 shadow-lg">
                            <div class="w-8 h-8 bg-white bg-opacity-30 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-white font-medium text-lg">
                                Redirigiendo en <span id="countdown" class="font-bold text-yellow-300">3</span> segundos
                            </span>
                        </div>
                    </div>
                    
                    <!-- Elementos decorativos flotantes -->
                    <div class="absolute top-20 left-10 text-4xl animate-float" style="animation-delay: 0.5s">🎉</div>
                    <div class="absolute bottom-20 right-10 text-3xl animate-float" style="animation-delay: 1s">🎊</div>
                    <div class="absolute top-1/2 left-5 text-2xl animate-float" style="animation-delay: 1.5s">🎈</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Contador regresivo
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = redirectUrl;
            }
        }, 1000);
    }

    function mostrarModalError(mensaje) {
        // Remover overlay de envío si existe
        const sendingOverlay = document.getElementById('sendingOverlay');
        if (sendingOverlay) {
            document.body.removeChild(sendingOverlay);
        }
        
        const modal = document.createElement('div');
        modal.id = 'errorModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300">
                <div class="p-8 text-center">
                    <!-- Icono de error -->
                    <div class="mx-auto mb-6 w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Error al Enviar
                    </h3>
                    
                    <!-- Mensaje -->
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        ${mensaje}
                    </p>
                    
                    <!-- Botón -->
                    <button onclick="cerrarModalError()" 
                            class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium">
                        <i class="fas fa-times mr-2"></i>Cerrar
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
    }

    function cerrarModalError() {
        const modal = document.getElementById('errorModal');
        if (modal) {
            document.body.removeChild(modal);
        }
    }

    function enviarTramite() {
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
                // Mostrar modal de éxito
                const redirectUrl = data.redirect || '/tramites-solicitante';
                mostrarModalExito(data.message, redirectUrl);
            } else {
                mostrarModalError('Error al finalizar el trámite: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarModalError('Error de conexión al finalizar el trámite');
        });
    }
</script>
@endpush
@endsection
