@extends('layouts.auth')

@section('title', 'Registro - Padrón de Proveedores de Oaxaca')

@push('styles')
<style>
    @keyframes loading-progress {
        0% { width: 20%; }
        50% { width: 75%; }
        100% { width: 95%; }
    }
    
    .animate-loading-progress {
        animation: loading-progress 2s ease-in-out infinite alternate;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fadeInUp {
        animation: fadeInUp 0.5s ease-out;
    }
    
    .border-3 {
        border-width: 3px;
    }
    
    /* Animaciones para el progreso de registro */
    @keyframes progressPulse {
        0%, 100% { 
            transform: scale(1);
            opacity: 1;
        }
        50% { 
            transform: scale(1.05);
            opacity: 0.8;
        }
    }
    
    .progress-pulse {
        animation: progressPulse 2s ease-in-out infinite;
    }
    
    /* Animación para la barra de progreso */
    @keyframes progressGlow {
        0% { box-shadow: 0 0 5px rgba(139, 69, 19, 0.3); }
        50% { box-shadow: 0 0 20px rgba(139, 69, 19, 0.6); }
        100% { box-shadow: 0 0 5px rgba(139, 69, 19, 0.3); }
    }
    
    .progress-glow {
        animation: progressGlow 2s ease-in-out infinite;
    }
    
    /* Suavizar transiciones del formulario */
    .form-disabled {
        transition: all 0.3s ease-out;
        filter: grayscale(0.3);
    }
</style>
@endpush

@section('content')
<!-- Modal para mostrar datos del SAT (Fuera del formulario principal) -->
@include('components.modal-sat-datos')

<form method="POST" action="{{ route('register') }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    <!-- Header con Logo -->
    <div class="text-center mb-3">
        <div class="flex flex-col items-center justify-center mb-2">
            <div class="w-14 h-14 flex items-center justify-center mb-2 bg-gradient-to-br from-primary/10 to-primary-dark/10 rounded-full p-2">
                <img src="{{ asset('images/logoprin.jpg') }}" alt="Logo Estado de Oaxaca" class="w-full h-full object-contain rounded-full">
            </div>
            <div class="text-center space-y-1">
                <span class="text-primary font-bold text-sm block tracking-wide">ADMINISTRACIÓN</span>
                <span class="text-gray-500 text-xs font-medium uppercase tracking-wider">Gobierno del Estado de Oaxaca</span>
            </div>
        </div>
        
        <div class="space-y-1 mb-2">
            <h1 class="text-lg font-bold text-gray-800 leading-tight">Registro de Proveedor</h1>
            <p class="text-gray-600 text-xs leading-tight max-w-xs mx-auto">
                Suba su Constancia de Situación Fiscal con QR
            </p>
        </div>
    </div>

    <!-- Mensajes de error del servidor -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-2 py-1.5 rounded-lg mb-2">
            <ul class="list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Área de subida de PDF -->
    <div id="uploadArea" class="transition-all duration-300 ease-in-out min-h-[80px]">
        <div class="mt-1">
            <label for="document" class="block text-xs font-medium text-gray-700 mb-0.5">
                <span class="block md:inline">Constancia de Situación Fiscal</span>
                <span class="text-xs text-gray-500 block md:inline md:ml-1">(PDF o Imagen)</span>
            </label>
            <div class="relative">
                <input type="file" id="document" name="document" accept=".pdf,.png,.jpg,.jpeg" required
                       class="hidden">
                <label for="document" class="group flex flex-col items-center justify-center w-full h-16 border-2 border-dashed border-primary/20 hover:border-primary rounded-lg transition-all duration-300 cursor-pointer bg-primary-50/30 hover:bg-primary-50">
                    <div class="flex flex-col md:flex-row items-center space-y-0.5 md:space-y-0 md:space-x-2 px-3">
                        <!-- Icono -->
                        <div class="transform group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-4 h-4 text-primary/70 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <!-- Texto -->
                        <div class="text-center md:text-left">
                            <p class="text-primary/70 group-hover:text-primary font-medium text-xs mb-0">
                                Haga clic para seleccionar archivo
                            </p>
                            <p class="text-xs text-gray-500" id="fileName">
                                PDF o Imagen con QR (Máximo 5MB)
                            </p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Indicador de Carga -->
        <div id="loading-indicator" class="hidden">
            <div class="mt-2 p-3 bg-gradient-to-r from-primary-50 to-blue-50 rounded-lg border border-primary/20">
                <div class="flex items-center space-x-3">
                    <!-- Spinner animado más pequeño -->
                    <div class="relative flex-shrink-0">
                        <div class="w-8 h-8 border-3 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Texto de carga compacto -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <h3 class="text-sm font-semibold text-primary">Procesando Constancia</h3>
                            <div class="flex space-x-1">
                                <div class="w-1 h-1 bg-primary rounded-full animate-pulse"></div>
                                <div class="w-1 h-1 bg-primary rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                <div class="w-1 h-1 bg-primary rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-600 mb-2">
                            Validando documento con el SAT...
                        </p>
                        
                        <!-- Barra de progreso compacta -->
                        <div class="w-full">
                            <div class="bg-gray-200 rounded-full h-1 overflow-hidden">
                                <div class="bg-gradient-to-r from-primary to-blue-500 h-full rounded-full animate-loading-progress"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de previsualización (permanentemente oculta) -->
        <div id="previewArea" class="hidden">
            <div class="hidden">
                <div id="qrResult"></div>
                <canvas id="pdfCanvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Formulario de registro (inicialmente oculto) -->
    <div id="registrationForm" class="hidden space-y-2 transition-all duration-300 ease-in-out">
        <input type="hidden" id="qrUrl" name="qr_url">
        
        <!-- Campos ocultos para datos del SAT -->
        <input type="hidden" id="satRfc" name="sat_rfc">
        <input type="hidden" id="satNombre" name="sat_nombre">
        <input type="hidden" id="satTipoPersona" name="sat_tipo_persona">
        <input type="hidden" id="satCurp" name="sat_curp">
        <input type="hidden" id="satCp" name="sat_cp">
        <input type="hidden" id="satColonia" name="sat_colonia">
        <input type="hidden" id="satNombreVialidad" name="sat_nombre_vialidad">
        <input type="hidden" id="satNumeroExterior" name="sat_numero_exterior">
        <input type="hidden" id="satNumeroInterior" name="sat_numero_interior">
        
        <!-- Botón Ver Datos del SAT -->
        <button type="button" 
                id="verDatosBtn"
                onclick="showSatModal()"
                class="hidden inline-flex items-center text-xs bg-white hover:bg-primary-50 text-primary font-medium py-1 px-2 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-primary/20 hover:border-primary/40">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver Datos del SAT
        </button>

        <!-- Indicador de validación del RFC del SAT -->
        <div id="rfcSatIndicator" class="hidden">
            <div class="flex items-center justify-between p-2.5 rounded-lg border-l-4 transition-all duration-300" id="rfcSatStatus">
                <div class="flex items-center space-x-2">
                    <div id="rfcSatIcon" class="flex-shrink-0"></div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-700">RFC del SAT</p>
                        <p class="text-xs font-mono text-gray-900" id="rfcSatValue"></p>
                    </div>
                </div>
                <div id="rfcSatMessage" class="text-xs font-medium"></div>
            </div>
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-gray-700 mb-0.5">Correo Electrónico</label>
            <div class="relative">
                <input type="email" id="email" name="email" required 
                       class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300 text-sm"
                       placeholder="ejemplo@correo.com">
                <div id="emailValidationIcon" class="absolute right-2 top-1/2 -translate-y-1/2 hidden">
                    <!-- Icono de validación se mostrará aquí -->
                </div>
            </div>
            <!-- Área fija para validación -->
            <div class="h-6 relative">
                <div id="emailValidation" class="absolute top-0 left-0 w-full opacity-0 transform translate-y-1 transition-all duration-200"></div>
            </div>
        </div>

        <!-- Contraseñas en 2 columnas para escritorio -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-3">
            <div>
                <label for="password" class="block text-xs font-medium text-gray-700 mb-0.5">Contraseña</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required 
                           class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300 text-sm"
                           placeholder="••••••••">
                    <button type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password-toggle-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-gray-700 mb-0.5">Confirmar Contraseña</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300 text-sm"
                           placeholder="••••••••">
                    <div class="absolute right-8 top-1/2 -translate-y-1/2">
                        <div id="passwordMatchIcon" class="hidden"></div>
                    </div>
                    <button type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password_confirmation-toggle-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Área fija para validación de contraseñas -->
        <div class="h-6 relative">
            <div id="passwordMatchValidation" class="absolute top-0 left-0 w-full opacity-0 transform translate-y-1 transition-all duration-200"></div>
        </div>
    </div>

    <!-- Canvas para renderizar PDF (oculto) -->
    <canvas id="pdfCanvas" class="hidden"></canvas>

    <!-- Botones de acción -->
    <div class="space-y-2 pt-3">
        <button type="button" id="actionButton" onclick="handleActionButton()" class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 relative overflow-hidden text-sm">
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <!-- Spinner de carga (oculto por defecto) -->
                <div id="loadingSpinner" class="hidden">
                    <div class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin shadow-sm"></div>
                </div>
                <!-- Icono normal -->
                <svg id="actionIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span id="actionText">Siguiente</span>
                <div class="absolute -right-2 w-2 h-2 bg-white/30 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping"></div>
            </div>
        </button>
        
        <!-- Indicador de progreso de registro (oculto por defecto) -->
        <div id="registrationProgress" class="hidden">
            <div class="p-4 bg-gradient-to-r from-primary-50 to-blue-50 rounded-lg border border-primary/20">
                <div class="flex items-center space-x-3">
                                         <!-- Spinner de registro -->
                     <div class="relative flex-shrink-0 progress-pulse">
                         <div class="w-8 h-8 border-3 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                         <div class="absolute inset-0 flex items-center justify-center">
                             <svg class="w-4 h-4 text-primary animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                             </svg>
                         </div>
                     </div>
                    
                    <!-- Texto de progreso -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <h3 class="text-sm font-semibold text-primary">Registrando Usuario</h3>
                            <div class="flex space-x-1">
                                <div class="w-1 h-1 bg-primary rounded-full animate-pulse"></div>
                                <div class="w-1 h-1 bg-primary rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                <div class="w-1 h-1 bg-primary rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-600 mb-2">
                            <span id="progressText">Creando cuenta y enviando correo de verificación...</span>
                        </p>
                        
                        <!-- Barra de progreso -->
                        <div class="w-full">
                            <div class="bg-gray-200 rounded-full h-2 overflow-hidden shadow-inner">
                                <div id="progressBar" class="bg-gradient-to-r from-primary to-blue-500 h-full rounded-full transition-all duration-1000 ease-out progress-glow" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <!-- Mensaje adicional -->
                        <p class="text-xs text-gray-500 mt-2">
                            <strong>Por favor, no cierre esta ventana.</strong> El proceso puede tardar unos momentos.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ url('/') }}" class="group w-full bg-white hover:bg-gray-50 text-primary hover:text-primary-dark font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border-2 border-primary/20 hover:border-primary/40 relative overflow-hidden inline-flex items-center justify-center text-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-primary-dark/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Volver al Inicio</span>
            </div>
        </a>
    </div>

    <!-- Enlace de recuperación de contraseña -->
    <div class="text-center mt-3 relative z-10">
        <a 
            href="{{ route('password.request') }}" 
            class="text-gray-500 hover:text-primary text-xs font-medium transition-all duration-200 flex items-center justify-center space-x-2 hover:bg-gray-50 py-1.5 px-3 rounded-lg"
        >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/>
            </svg>
            <span>¿Olvidaste tu contraseña?</span>
        </a>
    </div>
</form>

<!-- Scripts -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script src="/js/scrapers/sat-scraper.js"></script>
<script src="/js/validators/sat-validator.js"></script>
<script src="/js/components/qr-reader.js"></script>
<script src="/js/components/qr-handler.js"></script>

<script>
    // Configurar PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';
    
    // Variables globales
    let documentProcessed = false;
    let qrHandler = null;

    // Configuración específica para register.blade.php
    const config = {
        fileNameElement: 'fileName',
        previewAreaElement: 'previewArea',
        uploadAreaElement: 'uploadArea',
        registrationFormElement: 'registrationForm',
        verDatosBtnElement: 'verDatosBtn',
        qrUrlElement: 'qrUrl'
    };

    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            console.log('Inicializando procesador para registro...');
            
            // Verificar que las clases estén disponibles
            if (typeof QRHandler === 'undefined' || typeof QRReader === 'undefined' || 
                typeof SATValidator === 'undefined' || typeof SATScraper === 'undefined') {
                throw new Error('Las clases necesarias no están disponibles');
            }
            
            // Crear e inicializar QRHandler con configuración
            qrHandler = new QRHandler(config);
            const initialized = await qrHandler.initialize(QRReader, SATValidator, SATScraper);
            
            if (!initialized) {
                throw new Error('No se pudo inicializar el QRHandler');
            }

            // Configurar callbacks
            qrHandler.setOnDataScanned((data) => {
                console.log('Datos escaneados:', data);
                
                // Ocultar indicador de carga al procesar exitosamente
                showLoading(false);
                
                // Llenar campos ocultos con datos del SAT
                const fillSatData = (data) => {
                    if (data.details) {
                        document.getElementById('satRfc').value = data.details.rfc || '';
                        document.getElementById('satNombre').value = data.details.razonSocial || data.details.nombreCompleto || '';
                        document.getElementById('satTipoPersona').value = data.details.tipoPersona || '';
                        document.getElementById('satCurp').value = data.details.curp || '';
                        document.getElementById('satCp').value = data.details.cp || '';
                        document.getElementById('satColonia').value = data.details.colonia || '';
                        document.getElementById('satNombreVialidad').value = data.details.nombreVialidad || '';
                        document.getElementById('satNumeroExterior').value = data.details.numeroExterior || '';
                        document.getElementById('satNumeroInterior').value = data.details.numeroInterior || '';
                        
                        console.log('Datos del SAT guardados en campos ocultos');
                    }
                };
                
                fillSatData(data);
                
                // Ocultar área de subida
                const uploadArea = document.getElementById('uploadArea');
                if (uploadArea) {
                    uploadArea.classList.add('hidden');
                }

                // Mostrar formulario de registro
                const registrationForm = document.getElementById('registrationForm');
                if (registrationForm) {
                    registrationForm.classList.remove('hidden');
                }

                // Mostrar botón de ver datos
                const verDatosBtn = document.getElementById('verDatosBtn');
                if (verDatosBtn) {
                    verDatosBtn.classList.remove('hidden');
                }

                // Validar RFC internamente y mostrar indicador
                if (data.details && data.details.rfc) {
                    // Mostrar indicador del RFC del SAT
                    showRfcSatIndicator(data.details.rfc);
                    
                    // Validar RFC automáticamente
                    validateRfc(data.details.rfc);
                }

                // Autocompletar email si está disponible
                if (data.details && data.details.email) {
                    const emailInput = document.getElementById('email');
                    if (emailInput) {
                        emailInput.value = data.details.email;
                        // Validar email automáticamente
                        validateEmail(data.details.email);
                    }
                }

                // Cambiar botón a "REGISTRARSE"
                changeButtonToRegister();
                
                // Validar formulario
                validateForm();
            });

            qrHandler.setOnError((error) => {
                console.error('Error en QRHandler:', error);
                showError(error);
                resetUpload();
            });

            // Asignar a window para acceso global
            window.qrHandler = qrHandler;
            console.log('Procesador inicializado correctamente');

        } catch (error) {
            console.error('Error durante la inicialización:', error);
            showError('Error al inicializar el procesador: ' + error.message);
        }
    });

    // Variable para prevenir doble envío
    let isRegistering = false;

    // Función para manejar el botón de acción
    window.handleActionButton = function() {
        // Prevenir doble clic durante el registro
        if (isRegistering) {
            console.log('Registro ya en proceso, ignorando clic...');
            return;
        }

        if (!documentProcessed) {
            // Primera fase: procesar documento
            const fileInput = document.getElementById('document');
            if (!fileInput.files[0]) {
                showError('Por favor, seleccione un documento antes de continuar.');
                return;
            }
            
            showError('Por favor, seleccione y procese su Constancia de Situación Fiscal primero.');
        } else {
            // Segunda fase: enviar formulario
            const form = document.querySelector('form');
            if (validateForm()) {
                isRegistering = true;
                form.submit(); // Enviar el formulario directamente
            } else {
                showError('Por favor, complete todos los campos requeridos.');
            }
        }
    };

    // Función para cambiar el botón a modo "REGISTRARSE"
    function changeButtonToRegister() {
        const actionButton = document.getElementById('actionButton');
        const actionText = document.getElementById('actionText');
        const actionIcon = document.getElementById('actionIcon');
        
        if (actionButton && actionText && actionIcon) {
            actionText.textContent = 'REGISTRARSE';
            actionIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            `;
            documentProcessed = true;
        }
    }

    // Función para validar el formulario
    function validateForm() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required]');
        
        let allFilled = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                allFilled = false;
            }
        });

        // Validar que las contraseñas coincidan
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordsMatch = password && passwordConfirmation ? 
            password.value === passwordConfirmation.value : true;

        // Validar que los datos del SAT estén presentes
        const satDataValid = documentProcessed && 
            document.getElementById('satRfc').value && 
            document.getElementById('satNombre').value;

        return allFilled && passwordsMatch && satDataValid;
    }

    // Función para mostrar el modal con datos del SAT
    window.showSatModal = function() {
        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            const result = qrHandler.showSatData();
            if (result.success) {
                const satDataContent = document.getElementById('satDataContent');
                if (satDataContent) {
                    satDataContent.innerHTML = result.content;
                }
            } else {
                showError('Error al mostrar los datos: ' + result.error);
            }
        }
    };

    // Función para cerrar el modal
    window.closeSatModal = function() {
        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Cerrar modal al hacer clic fuera de él
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('satDataModal');
        const modalContent = modal?.querySelector('.bg-white');
        if (modal && event.target === modal && modalContent && !modalContent.contains(event.target)) {
            closeSatModal();
        }
    });

    // Evento change del input file
    document.getElementById('document').addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        try {
            // Validar el tipo de archivo
            const isPDF = file.type === 'application/pdf';
            const isImage = file.type.startsWith('image/');
            
            if (!isPDF && !isImage) {
                throw new Error('El archivo debe ser un PDF o una imagen (JPG, PNG).');
            }

            // Validar el tamaño del archivo (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                throw new Error('El archivo no debe exceder los 5MB.');
            }

            // Actualizar la etiqueta con el nombre del archivo
            const fileName = document.getElementById('fileName');
            if (fileName) {
                fileName.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            }

            showLoading(true);
            console.log('Iniciando procesamiento del archivo:', file.name);

            // Procesar el archivo con QRHandler
            if (!qrHandler) {
                throw new Error('El procesador no está inicializado');
            }

            await qrHandler.handleFile(file);

        } catch (error) {
            console.error('Error detallado:', error);
            showError(error.message || 'Error al procesar el documento');
            resetUpload();
        }
    });

    // Agregar validación en tiempo real para email y password
    document.addEventListener('input', function(e) {
        if (e.target.id === 'email') {
            // Validar email en tiempo real
            const email = e.target.value.trim();
            if (email.length > 3) {
                clearTimeout(window.emailValidationTimeout);
                window.emailValidationTimeout = setTimeout(() => {
                    validateEmail(email);
                }, 300);
            } else {
                clearValidationMessage('email');
            }
            
            // Actualizar estado del formulario
            if (documentProcessed) {
                updateFormValidation();
            }
        } else if (e.target.id === 'password' || e.target.id === 'password_confirmation') {
            // Validar coincidencia de contraseñas en tiempo real
            validatePasswordMatch();
            
            // Solo actualizar el estado visual si ya se procesó el documento
            if (documentProcessed) {
                updateFormValidation();
            }
        }
    });

    // ===== FUNCIONES DE REGISTRO =====

    // Función para iniciar el proceso de registro
    function startRegistrationProcess() {
        console.log('Iniciando proceso de registro...');
        
        // Mostrar estado de carga
        showRegistrationLoading(true);
        
        // Deshabilitar toda la interfaz
        disableForm(true);
        
        // Simular progreso de registro
        simulateRegistrationProgress();
    }

    // Función para mostrar/ocultar el estado de carga del registro
    function showRegistrationLoading(show = true) {
        const actionButton = document.getElementById('actionButton');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const actionIcon = document.getElementById('actionIcon');
        const actionText = document.getElementById('actionText');
        const registrationProgress = document.getElementById('registrationProgress');
        
        if (show) {
            // Actualizar botón
            if (actionButton) {
                actionButton.classList.add('opacity-75', 'cursor-not-allowed');
                actionButton.disabled = true;
            }
            
            // Mostrar spinner en el botón
            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }
            if (actionIcon) {
                actionIcon.classList.add('hidden');
            }
            if (actionText) {
                actionText.textContent = 'Registrando...';
            }
            
            // Mostrar indicador de progreso
            if (registrationProgress) {
                registrationProgress.classList.remove('hidden');
                setTimeout(() => {
                    registrationProgress.style.opacity = '0';
                    registrationProgress.style.transform = 'translateY(-10px)';
                    registrationProgress.style.transition = 'all 0.3s ease-out';
                    
                    setTimeout(() => {
                        registrationProgress.style.opacity = '1';
                        registrationProgress.style.transform = 'translateY(0)';
                    }, 10);
                }, 100);
            }
        } else {
            // Restaurar estado normal
            if (actionButton) {
                actionButton.classList.remove('opacity-75', 'cursor-not-allowed');
                actionButton.disabled = false;
            }
            
            if (loadingSpinner) {
                loadingSpinner.classList.add('hidden');
            }
            if (actionIcon) {
                actionIcon.classList.remove('hidden');
            }
            if (actionText) {
                actionText.textContent = 'REGISTRARSE';
            }
            
            if (registrationProgress) {
                registrationProgress.classList.add('hidden');
            }
        }
    }

    // Función para deshabilitar/habilitar el formulario
    function disableForm(disable = true) {
        const form = document.querySelector('form');
        if (!form) return;
        
        if (disable) {
            form.classList.add('form-disabled');
        } else {
            form.classList.remove('form-disabled');
        }
        
        const inputs = form.querySelectorAll('input:not(#document), button:not(#actionButton), select, textarea');
        inputs.forEach(input => {
            if (disable) {
                input.disabled = true;
                input.style.opacity = '0.5';
                input.style.pointerEvents = 'none';
                input.style.cursor = 'not-allowed';
            } else {
                input.disabled = false;
                input.style.opacity = '1';
                input.style.pointerEvents = 'auto';
                input.style.cursor = 'auto';
            }
        });
        
        // Deshabilitar enlaces también
        const links = form.querySelectorAll('a');
        links.forEach(link => {
            if (disable) {
                link.style.pointerEvents = 'none';
                link.style.opacity = '0.5';
            } else {
                link.style.pointerEvents = 'auto';
                link.style.opacity = '1';
            }
        });
    }

    // Función para simular el progreso de registro
    function simulateRegistrationProgress() {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        
        if (!progressBar || !progressText) return;
        
        const steps = [
            { progress: 20, text: 'Validando información del SAT...', delay: 500 },
            { progress: 40, text: 'Verificando datos de usuario...', delay: 800 },
            { progress: 60, text: 'Creando cuenta de usuario...', delay: 1000 },
            { progress: 80, text: 'Configurando permisos...', delay: 1200 },
            { progress: 95, text: 'Enviando correo de verificación...', delay: 1500 },
            { progress: 100, text: 'Registro completado exitosamente', delay: 2000 }
        ];
        
        let currentStep = 0;
        
        function updateProgress() {
            if (currentStep < steps.length) {
                const step = steps[currentStep];
                
                // Actualizar barra de progreso
                progressBar.style.width = step.progress + '%';
                
                // Actualizar texto
                progressText.textContent = step.text;
                
                // Programar siguiente paso
                setTimeout(() => {
                    currentStep++;
                    updateProgress();
                }, step.delay);
            }
        }
        
        // Iniciar progreso
        updateProgress();
    }

    // Interceptar el envío del formulario para manejar errores
    document.querySelector('form').addEventListener('submit', function(e) {
        // No prevenir el envío por defecto
        // Solo actualizar la UI para mostrar el progreso
        startRegistrationProcess();
    });

    // Remover el evento beforeunload que causa problemas
    // window.addEventListener('beforeunload', function(e) { ... });

    // Función para iniciar el proceso de registro
    function startRegistrationProcess() {
        console.log('Iniciando proceso de registro...');
        
        // Mostrar estado de carga
        showRegistrationLoading(true);
        
        // Deshabilitar toda la interfaz
        disableForm(true);
        
        // Simular progreso de registro
        simulateRegistrationProgress();
    }

    // ===== FUNCIONES DE VALIDACIÓN EN TIEMPO REAL =====
    
    // Variables para control de validaciones
    window.validationState = {
        email: { valid: null, checking: false },
        rfc: { valid: null, checking: false },
        passwordMatch: { valid: null, strongPassword: null }
    };

    // Función para validar email
    async function validateEmail(email) {
        if (!email || email.length < 5) {
            clearValidationMessage('email');
            return;
        }

        // Marcar como validando
        window.validationState.email.checking = true;
        showValidationLoading('email', true);

        try {
            const response = await fetch(`/api/validate/email?email=${encodeURIComponent(email)}`);
            const data = await response.json();
            
            window.validationState.email.valid = data.valid;
            window.validationState.email.checking = false;
            
            showValidationMessage('email', data.message, data.valid);
            showValidationLoading('email', false);
            
            updateFormValidation();
        } catch (error) {
            console.error('Error validating email:', error);
            window.validationState.email.checking = false;
            showValidationLoading('email', false);
            showValidationMessage('email', 'Error de conexión', false);
        }
    }

    // Función para validar RFC (validación interna con indicador visual)
    async function validateRfc(rfc) {
        if (!rfc || rfc.length < 10) {
            window.validationState.rfc.valid = null;
            return;
        }

        // Marcar como validando y mostrar loading en el indicador
        window.validationState.rfc.checking = true;
        updateRfcSatIndicator(rfc, null, true);

        try {
            const response = await fetch(`/api/validate/rfc?rfc=${encodeURIComponent(rfc)}`);
            const data = await response.json();
            
            window.validationState.rfc.valid = data.valid;
            window.validationState.rfc.checking = false;
            
            // Actualizar indicador con resultado
            updateRfcSatIndicator(rfc, data.valid, false, data.message);
            
            updateFormValidation();
        } catch (error) {
            console.error('Error validating RFC:', error);
            window.validationState.rfc.checking = false;
            window.validationState.rfc.valid = false;
            
            // Mostrar error en el indicador
            updateRfcSatIndicator(rfc, false, false, 'Error de conexión');
        }
    }

    // Función para mostrar el indicador del RFC del SAT
    function showRfcSatIndicator(rfc) {
        const indicator = document.getElementById('rfcSatIndicator');
        const valueElement = document.getElementById('rfcSatValue');
        
        if (indicator && valueElement) {
            valueElement.textContent = rfc;
            indicator.classList.remove('hidden');
        }
    }

    // Función para actualizar el estado del indicador del RFC
    function updateRfcSatIndicator(rfc, isValid, isLoading, message = '') {
        const statusContainer = document.getElementById('rfcSatStatus');
        const iconElement = document.getElementById('rfcSatIcon');
        const messageElement = document.getElementById('rfcSatMessage');
        const valueElement = document.getElementById('rfcSatValue');
        
        if (!statusContainer || !iconElement || !messageElement || !valueElement) return;

        valueElement.textContent = rfc;

        if (isLoading) {
            // Estado de carga
            statusContainer.className = 'flex items-center justify-between p-2.5 rounded-lg border-l-4 transition-all duration-300 bg-blue-50 border-blue-500';
            iconElement.innerHTML = `
                <div class="w-4 h-4 border-2 border-blue-300 border-t-blue-600 rounded-full animate-spin"></div>
            `;
            messageElement.textContent = 'Validando...';
            messageElement.className = 'text-xs font-medium text-blue-600';
        } else if (isValid === true) {
            // RFC disponible
            statusContainer.className = 'flex items-center justify-between p-2.5 rounded-lg border-l-4 transition-all duration-300 bg-green-50 border-green-500';
            iconElement.innerHTML = `
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            `;
            messageElement.textContent = message || 'Disponible';
            messageElement.className = 'text-xs font-medium text-green-600';
        } else if (isValid === false) {
            // RFC ya existe o error
            statusContainer.className = 'flex items-center justify-between p-2.5 rounded-lg border-l-4 transition-all duration-300 bg-red-50 border-red-500';
            iconElement.innerHTML = `
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            `;
            messageElement.textContent = message || 'Ya registrado';
            messageElement.className = 'text-xs font-medium text-red-600';
        }
    }

    // Función para mostrar mensaje de validación (solo para email)
    function showValidationMessage(field, message, isValid) {
        // Solo procesar para email, RFC se valida internamente
        if (field !== 'email') return;
        
        const validationDiv = document.getElementById(`${field}Validation`);
        const iconDiv = document.getElementById(`${field}ValidationIcon`);
        const inputField = document.getElementById(field);
        
        if (!validationDiv) return;

        validationDiv.innerHTML = `
            <div class="flex items-center space-x-1 text-xs">
                <svg class="w-3 h-3 ${isValid ? 'text-green-500' : 'text-red-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${isValid 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                    }
                </svg>
                <span class="${isValid ? 'text-green-600' : 'text-red-600'}">${message}</span>
            </div>
        `;
        
        // Mostrar con animación suave
        validationDiv.style.opacity = '1';
        validationDiv.style.transform = 'translateY(0)';

        // Actualizar icono en el input
        if (iconDiv) {
            iconDiv.innerHTML = `
                <svg class="w-4 h-4 ${isValid ? 'text-green-500' : 'text-red-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${isValid 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                    }
                </svg>
            `;
            iconDiv.classList.remove('hidden');
        }

        // Actualizar borde del input
        if (inputField) {
            inputField.classList.remove('border-red-300', 'border-green-300');
            inputField.classList.add(isValid ? 'border-green-300' : 'border-red-300');
        }
    }

    // Función para mostrar indicador de carga en validación (solo para email)
    function showValidationLoading(field, show) {
        // Solo procesar para email, RFC se valida internamente
        if (field !== 'email') return;
        
        const iconDiv = document.getElementById(`${field}ValidationIcon`);
        
        if (!iconDiv) return;

        if (show) {
            iconDiv.innerHTML = `
                <div class="w-4 h-4 border-2 border-gray-300 border-t-primary rounded-full animate-spin"></div>
            `;
            iconDiv.classList.remove('hidden');
        }
    }

    // Función para limpiar mensaje de validación (solo para email)
    function clearValidationMessage(field) {
        // Solo procesar para email, RFC se valida internamente
        if (field !== 'email') {
            // Para RFC, solo limpiar el estado
            if (field === 'rfc' && window.validationState[field]) {
                window.validationState[field].valid = null;
            }
            return;
        }
        
        const validationDiv = document.getElementById(`${field}Validation`);
        const iconDiv = document.getElementById(`${field}ValidationIcon`);
        const inputField = document.getElementById(field);
        
        if (validationDiv) {
            // Ocultar con animación suave
            validationDiv.style.opacity = '0';
            validationDiv.style.transform = 'translateY(4px)';
        }
        
        if (iconDiv) {
            iconDiv.classList.add('hidden');
        }
        
        if (inputField) {
            inputField.classList.remove('border-red-300', 'border-green-300');
        }
        
        window.validationState[field].valid = null;
    }

    // Función para actualizar el estado general del formulario
    function updateFormValidation() {
        const isValid = validateForm();
        const actionButton = document.getElementById('actionButton');
        
        if (actionButton && documentProcessed) {
            if (isValid) {
                actionButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                actionButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    // Función para validar coincidencia de contraseñas
    function validatePasswordMatch() {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        if (!password || !passwordConfirmation) return;

        const passwordValue = password.value;
        const confirmationValue = passwordConfirmation.value;
        
        // Solo validar si ambos campos tienen contenido
        if (passwordValue.length === 0 && confirmationValue.length === 0) {
            clearPasswordMatchValidation();
            return;
        }
        
        // Si solo uno tiene contenido, no mostrar validación aún
        if (passwordValue.length === 0 || confirmationValue.length === 0) {
            clearPasswordMatchValidation();
            return;
        }
        
        const passwordsMatch = passwordValue === confirmationValue;
        const passwordStrong = validatePasswordStrength(passwordValue);
        
        showPasswordMatchValidation(passwordsMatch, passwordStrong);
    }

    // Función para validar fortaleza de contraseña
    function validatePasswordStrength(password) {
        if (password.length < 8) return { valid: false, message: 'Mínimo 8 caracteres' };
        
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        
        const criteriaMet = [hasUpperCase, hasLowerCase, hasNumbers, hasSpecialChar].filter(Boolean).length;
        
        if (criteriaMet >= 3) {
            return { valid: true, message: 'Contraseña segura' };
        } else if (criteriaMet >= 2) {
            return { valid: true, message: 'Contraseña aceptable' };
        } else {
            return { valid: false, message: 'Contraseña débil' };
        }
    }

    // Función para mostrar validación de contraseñas
    function showPasswordMatchValidation(passwordsMatch, passwordStrength) {
        const validationDiv = document.getElementById('passwordMatchValidation');
        const iconDiv = document.getElementById('passwordMatchIcon');
        const passwordInput = document.getElementById('password');
        const confirmationInput = document.getElementById('password_confirmation');
        
        if (!validationDiv || !iconDiv) return;

        let message = '';
        let isValid = false;
        let iconColor = '';
        let borderColor = '';

        if (passwordsMatch && passwordStrength.valid) {
            message = `✅ Las contraseñas coinciden - ${passwordStrength.message}`;
            isValid = true;
            iconColor = 'text-green-500';
            borderColor = 'border-green-300';
        } else if (passwordsMatch && !passwordStrength.valid) {
            message = `⚠️ Las contraseñas coinciden - ${passwordStrength.message}`;
            isValid = false;
            iconColor = 'text-yellow-500';
            borderColor = 'border-yellow-300';
        } else {
            message = '❌ Las contraseñas no coinciden';
            isValid = false;
            iconColor = 'text-red-500';
            borderColor = 'border-red-300';
        }

        // Mostrar mensaje
        validationDiv.innerHTML = `
            <div class="flex items-center space-x-1 text-xs">
                <span class="${isValid && passwordsMatch ? 'text-green-600' : 'text-red-600'}">${message}</span>
            </div>
        `;
        
        validationDiv.style.opacity = '1';
        validationDiv.style.transform = 'translateY(0)';

        // Mostrar icono
        iconDiv.innerHTML = `
            <svg class="w-4 h-4 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${passwordsMatch 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                }
            </svg>
        `;
        iconDiv.classList.remove('hidden');

        // Actualizar bordes
        if (passwordInput && confirmationInput) {
            [passwordInput, confirmationInput].forEach(input => {
                input.classList.remove('border-red-300', 'border-green-300', 'border-yellow-300');
                input.classList.add(borderColor);
            });
        }

        // Actualizar estado global
        window.validationState.passwordMatch = { 
            valid: passwordsMatch && passwordStrength.valid,
            strongPassword: passwordStrength.valid 
        };
    }

    // Función para limpiar validación de contraseñas
    function clearPasswordMatchValidation() {
        const validationDiv = document.getElementById('passwordMatchValidation');
        const iconDiv = document.getElementById('passwordMatchIcon');
        const passwordInput = document.getElementById('password');
        const confirmationInput = document.getElementById('password_confirmation');
        
        if (validationDiv) {
            validationDiv.style.opacity = '0';
            validationDiv.style.transform = 'translateY(4px)';
        }
        
        if (iconDiv) {
            iconDiv.classList.add('hidden');
        }
        
        if (passwordInput && confirmationInput) {
            [passwordInput, confirmationInput].forEach(input => {
                input.classList.remove('border-red-300', 'border-green-300', 'border-yellow-300');
            });
        }
        
        window.validationState.passwordMatch = { valid: null, strongPassword: null };
    }

    // Modificar la función validateForm existente para incluir validaciones en tiempo real
    function validateForm() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required]');
        
        let allFilled = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                allFilled = false;
            }
        });

        // Validar que las contraseñas coincidan
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordsMatch = password && passwordConfirmation ? 
            password.value === passwordConfirmation.value && password.value.length >= 8 : true;

        // Validar que los datos del SAT estén presentes
        const satDataValid = documentProcessed && 
            document.getElementById('satRfc').value && 
            document.getElementById('satNombre').value;

        // Validar en tiempo real (solo si ya se han validado)
        const emailValid = window.validationState.email.valid !== false;
        const rfcValid = window.validationState.rfc.valid !== false;
        const passwordMatchValid = window.validationState.passwordMatch ? 
            window.validationState.passwordMatch.valid !== false : passwordsMatch;

        return allFilled && passwordsMatch && satDataValid && emailValid && rfcValid && passwordMatchValid;
    }
</script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';

    // Funciones de utilidad
    function showLoading(show = true) {
        const loadingIndicator = document.getElementById('loading-indicator');
        const uploadArea = document.getElementById('uploadArea');
        
        if (loadingIndicator) {
            if (show) {
                // Mostrar el indicador de carga de forma suave
                loadingIndicator.style.opacity = '0';
                loadingIndicator.style.transform = 'translateY(-10px)';
                loadingIndicator.classList.remove('hidden');
                
                // Transición suave
                setTimeout(() => {
                    loadingIndicator.style.transition = 'all 0.3s ease-out';
                    loadingIndicator.style.opacity = '1';
                    loadingIndicator.style.transform = 'translateY(0)';
                }, 10);
            } else {
                // Ocultar de forma suave
                loadingIndicator.style.transition = 'all 0.3s ease-out';
                loadingIndicator.style.opacity = '0';
                loadingIndicator.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    loadingIndicator.classList.add('hidden');
                    loadingIndicator.style.transition = '';
                    loadingIndicator.style.transform = '';
                }, 300);
            }
        }
        
        // Ocultar/mostrar el área de subida cuando se está cargando
        if (uploadArea) {
            if (show) {
                uploadArea.style.opacity = '0.7';
                uploadArea.style.pointerEvents = 'none';
                uploadArea.style.filter = 'blur(1px)';
            } else {
                uploadArea.style.opacity = '1';
                uploadArea.style.pointerEvents = 'auto';
                uploadArea.style.filter = 'none';
            }
        }
    }

    function showError(message) {
        showLoading(false);
        // Crear notificación de error
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm notification-slide-in';
        notification.innerHTML = `
            <div class="card-custom rounded-lg shadow-lg border-l-4 border-red-500 p-4 bg-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            ${message}
                        </p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(notification);

        // Remover la notificación después de 3 segundos
        setTimeout(() => {
            notification.classList.replace('notification-slide-in', 'notification-slide-out');
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    function resetUpload() {
        const fileInput = document.getElementById('document');
        if (fileInput) {
            fileInput.value = '';
        }

        const fileName = document.getElementById('fileName');
        if (fileName) {
            fileName.textContent = 'PDF o Imagen con QR (Máximo 5MB)';
        }

        // Mostrar área de subida y restaurar estado normal
        const uploadArea = document.getElementById('uploadArea');
        if (uploadArea) {
            uploadArea.classList.remove('hidden');
            uploadArea.style.opacity = '1';
            uploadArea.style.pointerEvents = 'auto';
        }
        
        // Ocultar indicador de carga
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
        
        // Ocultar formulario de registro
        const registrationForm = document.getElementById('registrationForm');
        if (registrationForm) {
            registrationForm.classList.add('hidden');
        }
        
        // Ocultar botón de ver datos
        const verDatosBtn = document.getElementById('verDatosBtn');
        if (verDatosBtn) {
            verDatosBtn.classList.add('hidden');
        }
        
        // Ocultar indicador del RFC del SAT
        const rfcSatIndicator = document.getElementById('rfcSatIndicator');
        if (rfcSatIndicator) {
            rfcSatIndicator.classList.add('hidden');
        }
        
        // Ocultar botón de enviar
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.classList.add('hidden');
        }
        
        // Limpiar campos
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.value = '';
        }
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.value = '';
        }
        const passwordConfirmInput = document.getElementById('password_confirmation');
        if (passwordConfirmInput) {
            passwordConfirmInput.value = '';
        }
        
        // Limpiar validación de contraseñas
        clearPasswordMatchValidation();
        
        // Limpiar campos ocultos del SAT
        document.getElementById('qrUrl').value = '';
        document.getElementById('satRfc').value = '';
        document.getElementById('satNombre').value = '';
        document.getElementById('satTipoPersona').value = '';
        document.getElementById('satCurp').value = '';
        document.getElementById('satCp').value = '';
        document.getElementById('satColonia').value = '';
        document.getElementById('satNombreVialidad').value = '';
        document.getElementById('satNumeroExterior').value = '';
        document.getElementById('satNumeroInterior').value = '';

        if (qrHandler) {
            qrHandler.reset();
        }

        // Resetear el estado del botón
        const actionButton = document.getElementById('actionButton');
        const actionText = document.getElementById('actionText');
        const actionIcon = document.getElementById('actionIcon');
        
        if (actionButton && actionText && actionIcon) {
            actionText.textContent = 'SIGUIENTE';
            actionIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            `;
            documentProcessed = false;
        }

        showLoading(false);
    }
</script>
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-toggle-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}
</script>
@endsection 