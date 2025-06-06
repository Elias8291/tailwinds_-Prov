@extends('layouts.app')

@section('content')
<style>
    /* Smooth transitions for step content */
    [data-hs-stepper-content-item] {
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease-in-out;
        position: absolute;
        width: 100%;
        visibility: hidden;
        background-color: #ffffff;
    }

    [data-hs-stepper-content-item].active {
        opacity: 1;
        transform: translateY(0);
        position: relative;
        visibility: visible;
        background-color: #ffffff;
    }

    /* Ensure white background persists */
    .form-container {
        background-color: #ffffff !important;
    }
    
    .form-step {
        background-color: #ffffff !important;
    }

    .form-content {
        background-color: #ffffff !important;
        position: relative;
        z-index: 1;
    }

    /* Override any transparency */
    .bg-white-override {
        background-color: #ffffff !important;
    }
</style>

<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto bg-white-override rounded-2xl shadow-xl form-container">
            <!-- Header con gradiente -->
            <div class="bg-gradient-to-r from-red-800 to-red-600 rounded-t-2xl px-8 py-6">
                <h2 class="text-xl md:text-2xl font-bold text-white">Inscripción al Padrón de Proveedores</h2>
                <p class="text-red-100 text-sm mt-1">Complete todos los campos requeridos</p>
            </div>

            <div class="p-8 bg-white-override form-content">
                <!-- Progress Container -->
                <div class="max-w-3xl mx-auto mb-12 flex flex-col md:flex-row items-center gap-6">
                    <!-- Progress Info -->
                    <div class="flex flex-col items-center">
                        <span class="text-2xl md:text-3xl font-bold text-red-800" id="progress-percentage">0%</span>
                        <span class="text-xs uppercase text-gray-500 tracking-wide">Completado</span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full">
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                            <div class="h-full bg-gradient-to-r from-red-800 to-red-600 rounded-full transition-all duration-500" id="progress-bar" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Progress Tracker (Steps) -->
                <div class="relative max-w-3xl mx-auto mb-12">
                    <div class="absolute top-4 left-10 right-10 h-0.5 bg-gray-100"></div>
                    <div class="absolute top-4 left-10 h-0.5 bg-gradient-to-r from-red-800 to-red-600 transition-all duration-600" id="progress-line" style="width: 0%;"></div>
                    <div class="flex justify-between">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center relative z-10 w-24" data-step="1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-white-override border-2 border-gray-200 text-gray-500 font-semibold text-sm transition-all duration-300 step-circle shadow-md">
                                01
                            </div>
                            <span class="mt-2 text-xs text-center text-gray-500 font-medium transition-colors duration-300">Información Personal</span>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center relative z-10 w-24" data-step="2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-white-override border-2 border-gray-200 text-gray-500 font-semibold text-sm transition-all duration-300 step-circle shadow-md">
                                02
                            </div>
                            <span class="mt-2 text-xs text-center text-gray-500 font-medium transition-colors duration-300">Detalles del Trámite</span>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center relative z-10 w-24" data-step="3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-white-override border-2 border-gray-200 text-gray-500 font-semibold text-sm transition-all duration-300 step-circle shadow-md">
                                03
                            </div>
                            <span class="mt-2 text-xs text-center text-gray-500 font-medium transition-colors duration-300">Documentos</span>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="max-w-3xl mx-auto">
                    <form method="POST" action="{{ route('tramites.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Form Content Container -->
                        <div class="relative min-h-[400px] bg-white-override rounded-xl p-6 border border-gray-100 form-step">
                            <!-- Step 1: Información Personal -->
                            <div data-hs-stepper-content-item='{"index": 1}' class="active form-step">
                                <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                    <i class="fas fa-user text-red-800 mr-2"></i> Información Personal
                                </h4>
                                
                                <div class="space-y-6">
                                    <!-- Horizontal Group -->
                                    <div class="flex flex-col md:flex-row gap-6">
                                        <!-- Tipo de Persona -->
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-600 mb-2">Tipo de Persona</label>
                                            <select name="tipo_persona" 
                                                    id="tipo_persona" 
                                                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300">
                                                <option value="">Seleccione un tipo</option>
                                                <option value="Física">Física</option>
                                                <option value="Moral">Moral</option>
                                            </select>
                                        </div>

                                        <!-- RFC -->
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-600 mb-2">RFC</label>
                                            <input type="text" 
                                                   name="rfc" 
                                                   id="rfc" 
                                                   class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300"
                                                   placeholder="Ej. XAXX010101000" 
                                                   maxlength="13">
                                        </div>
                                    </div>

                                    <!-- Razón Social -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Razón Social</label>
                                        <input type="text" 
                                               id="razon_social" 
                                               name="razon_social" 
                                               class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300">
                                    </div>

                                    <!-- Objeto Social -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Objeto Social</label>
                                        <textarea id="objeto_social" 
                                                  name="objeto_social" 
                                                  class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300 resize-vertical"
                                                  rows="4"></textarea>
                                    </div>

                                    <!-- Sectores y Actividad -->
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 mb-2">Sector</label>
                                            <select name="sectores" 
                                                    id="sectores" 
                                                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300">
                                                <option value="">Seleccione un sector</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 mb-2">Actividad</label>
                                            <select name="actividad" 
                                                    id="actividad" 
                                                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300">
                                                <option value="">Seleccione una actividad</option>
                                            </select>
                                        </div>

                                        <!-- Actividades Seleccionadas -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 mb-2">Actividades Seleccionadas</label>
                                            <div class="flex flex-wrap gap-2 p-3 border border-gray-200 rounded-md bg-gray-50" id="actividades-seleccionadas">
                                                <span class="text-gray-500 text-sm italic">Sin actividades seleccionadas</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Detalles del Trámite -->
                            <div data-hs-stepper-content-item='{"index": 2}' class="form-step">
                                <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                    <i class="fas fa-file-alt text-red-800 mr-2"></i> Detalles del Trámite
                                </h4>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Descripción</label>
                                        <textarea name="descripcion"
                                                  rows="6"
                                                  class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-red-800 transition-all duration-300 resize-vertical"
                                                  placeholder="Describa los detalles de su trámite"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Documentos -->
                            <div data-hs-stepper-content-item='{"index": 3}' class="form-step">
                                <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
                                    <i class="fas fa-file-upload text-red-800 mr-2"></i> Documentos
                                </h4>
                                <div class="space-y-6">
                                    <div class="w-full">
                                        <label class="block text-sm font-medium text-gray-600 mb-2">Documentos Adjuntos</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-red-800 transition-all duration-300">
                                            <div class="space-y-2 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <div class="flex text-sm text-gray-600 justify-center">
                                                    <label class="relative cursor-pointer bg-white-override rounded-md font-medium text-red-800 hover:text-red-900 focus-within:outline-none">
                                                        <span>Cargar archivos</span>
                                                        <input type="file" name="documentos[]" class="sr-only" multiple>
                                                    </label>
                                                    <p class="pl-1">o arrastrar y soltar</p>
                                                </div>
                                                <p class="text-xs text-gray-500">PDF hasta 10MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="flex justify-between gap-4 mt-8">
                            <button type="button"
                                    class="w-full md:w-auto px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg"
                                    data-hs-stepper-back-btn>
                                <i class="fas fa-arrow-left mr-2"></i>Anterior
                            </button>
                            <button type="button"
                                    class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-red-800 to-red-600 text-white rounded-md hover:from-red-700 hover:to-red-500 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg"
                                    data-hs-stepper-next-btn>
                                Siguiente<i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <button type="submit"
                                    class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-red-800 to-red-600 text-white rounded-md hover:from-red-700 hover:to-red-500 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg"
                                    data-hs-stepper-finish-btn
                                    style="display: none;">
                                Enviar Trámite<i class="fas fa-check ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Add Stepper Script -->
<script src="{{ asset('js/stepper.js') }}"></script>

@endsection 