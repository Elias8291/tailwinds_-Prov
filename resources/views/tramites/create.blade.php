@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Form Container -->
    <div class="max-w-4xl mx-auto mt-16 bg-white rounded-xl shadow-lg p-6 md:p-8 relative z-10"
         x-data="{ 
            currentStep: 1,
            totalSteps: 7,
            formData: {},
            init() {
                this.$nextTick(() => {
                    this.$el.classList.remove('invisible');
                });
            }
         }"
         class="invisible">
        <!-- Progress Container -->
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

        <!-- Progress Tracker (Steps) -->
        <div class="relative max-w-3xl mx-auto mb-12 h-[80px]">
            <div class="absolute top-4 left-10 right-10 h-0.5 bg-gray-200"></div>
            <div class="absolute top-4 left-10 h-0.5 bg-red-800 transition-all duration-600 transform-gpu" x-bind:style="'width: ' + (currentStep / totalSteps * 100) + '%'"></div>
            <div class="flex justify-between">
                <template x-for="(step, index) in [
                    { number: '01', label: 'Datos Generales' },
                    { number: '02', label: 'Domicilio' },
                    { number: '03', label: 'Constitución' },
                    { number: '04', label: 'Accionistas' },
                    { number: '05', label: 'Documentos' },
                    { number: '06', label: 'Confirmación' },
                    { number: '07', label: 'Finalización' }
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

        <!-- Form Sections Container - Fixed height to prevent layout shifts -->
        <div class="min-h-[500px]">
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

                <!-- Documentos -->
                <div x-show="currentStep === 5" x-cloak>
                    @include('components.formularios.seccion-documentos')
                </div>

                <!-- Confirmación -->
                <div x-show="currentStep === 6" x-cloak>
                    <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
                        <i class="fas fa-check-circle text-red-800 mr-2"></i> Confirmación
                    </h4>
                    <!-- Contenido de confirmación -->
                </div>

                <!-- Finalización -->
                <div x-show="currentStep === 7" x-cloak>
                    <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
                        <i class="fas fa-flag-checkered text-red-800 mr-2"></i> Finalización
                    </h4>
                    <!-- Contenido de finalización -->
                </div>

                <!-- Navigation Buttons - Fixed height container -->
                <div class="flex justify-between gap-4 mt-6 h-[60px]">
                    <button type="button" 
                            x-show="currentStep > 1"
                            x-cloak
                            @click="currentStep--"
                            class="w-full md:w-auto px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        Anterior
                    </button>
                    <button type="button" 
                            x-show="currentStep < totalSteps"
                            x-cloak
                            @click="currentStep++"
                            class="w-full md:w-auto px-6 py-3 bg-red-800 text-white rounded-md hover:bg-red-900 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        Siguiente
                    </button>
                    <button type="submit" 
                            x-show="currentStep === totalSteps"
                            x-cloak
                            class="w-full md:w-auto px-6 py-3 bg-red-800 text-white rounded-md hover:bg-red-900 transition-all duration-300 transform-gpu hover:-translate-y-0.5">
                        Finalizar
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
    
    /* Asegurar que el fondo sea blanco */
    .bg-white {
        background-color: #ffffff !important;
    }
    
    /* Asegurar que el contenedor principal tenga fondo blanco y opacidad correcta */
    .bg-opacity-90 {
        --tw-bg-opacity: 0.9 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formData', () => ({
            currentStep: 1,
            totalSteps: 7,
            formData: {},
            
            nextStep() {
                if (this.currentStep < this.totalSteps) {
                    this.currentStep++
                }
            },
            
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--
                }
            },
            
            submitForm() {
                // Aquí irá la lógica de envío del formulario
                console.log('Formulario enviado', this.formData)
            }
        }))
    })
</script>
@endpush
@endsection
