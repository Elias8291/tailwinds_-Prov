@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 sm:px-4 py-4 sm:py-8">
    <!-- Form Container -->
    <div class="max-w-4xl mx-auto mt-4 sm:mt-8 md:mt-16 bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-8 relative z-10"
         x-data="{ 
            currentStep: 1,
            totalSteps: 6,
            formData: {},
            init() {
                this.$nextTick(() => {
                    this.$el.classList.remove('invisible');
                });
            }
         }"
         class="invisible">
        <!-- Mobile Progress Indicator -->
        <div class="md:hidden mb-4 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-800 text-white shadow-lg">
                <span class="text-xl font-bold" x-text="currentStep">1</span>
                <span class="text-xs">/</span>
                <span class="text-sm" x-text="totalSteps">6</span>
            </div>
            <div class="mt-1 text-xs text-gray-600 font-medium" x-text="[
                'Datos Generales',
                'Domicilio',
                'Datos de Constitución',
                'Accionistas',
                'Apoderado Legal',
                'Documentos'
            ][currentStep - 1]"></div>
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
                    <template x-for="(step, index) in [
                        { number: '01', label: 'Datos Generales' },
                        { number: '02', label: 'Domicilio' },
                        { number: '03', label: 'Constitución' },
                        { number: '04', label: 'Accionistas' },
                        { number: '05', label: 'Apoderado Legal' },
                        { number: '06', label: 'Documentos' }
                    ]" :key="index">
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
            <form @submit.prevent="submitForm" class="max-w-3xl mx-auto">
                <!-- Datos Generales -->
                <div x-show="currentStep === 1" x-cloak>
                    @include('components.formularios.seccion-datos-generales')
                </div>

                <!-- Domicilio -->
                <div x-show="currentStep === 2" x-cloak>
                    @include('components.formularios.seccion-domicilio')
                </div>

                <!-- Constitución -->
                <div x-show="currentStep === 3" x-cloak>
                    @include('components.formularios.seccion-constitucion')
                </div>

                <!-- Accionistas -->
                <div x-show="currentStep === 4" x-cloak>
                    @include('components.formularios.seccion-accionistas')
                </div>

                <!-- Apoderado Legal -->
                <div x-show="currentStep === 5" x-cloak>
                    @include('components.formularios.seccion-apoderado')
                </div>

                <!-- Documentos -->
                <div x-show="currentStep === 6" x-cloak>
                    @include('components.formularios.seccion-documentos')
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
                    <button type="submit" 
                            x-show="currentStep === totalSteps"
                            x-cloak
                            class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-red-800 text-white text-sm sm:text-base rounded-lg hover:bg-red-900 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        Finalizar <i class="fas fa-check ml-1"></i>
                    </button>
                </div>
            </form>
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formData', () => ({
            currentStep: 1,
            totalSteps: 6,
            formData: {},
            
            nextStep() {
                if (this.currentStep < this.totalSteps) {
                    this.currentStep++
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
            
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
            
            submitForm() {
                console.log('Formulario enviado', this.formData)
            }
        }))
    })
</script>
@endpush
@endsection
