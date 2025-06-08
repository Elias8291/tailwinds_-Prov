@extends('layouts.auth')

@section('title', 'Registro - Padrón de Proveedores de Oaxaca')

@section('content')
<!-- Modal para mostrar datos del SAT (Fuera del formulario principal) -->
<div id="satDataModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
        <!-- Modal header -->
        <div class="px-6 py-4 bg-gradient-to-br from-primary to-primary-dark border-b border-primary/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/10 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Datos del SAT</h3>
                </div>
                <button onclick="closeSatModal()" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Modal body -->
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 150px);">
            <div id="satDataContent" class="space-y-6">
                <!-- Los datos del SAT se insertarán aquí -->
            </div>
        </div>
        <!-- Modal footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button onclick="closeSatModal()" 
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 font-medium rounded-lg border border-gray-300 transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    <!-- Header con Logo -->
    <div class="text-center mb-8">
        <div class="flex items-center justify-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center mr-3 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0L3 7v10c0 5.55 3.84 9.739 9 9.739s9-4.189 9-9.739V7L12 0z"/>
                </svg>
            </div>
            <div class="text-left">
                <span class="text-primary font-bold text-lg block">ADMINISTRACIÓN</span>
                <span class="text-gray-600 text-xs font-medium">Gobierno de Oaxaca</span>
            </div>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Registro de Proveedor</h1>
        <p class="text-gray-600 text-sm leading-relaxed max-w-xs mx-auto">
            Suba su Constancia de Situación Fiscal con QR
        </p>
    </div>

    <!-- Mensajes de error del servidor -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Área de subida de PDF -->
    <div id="uploadArea" class="transition-all duration-300 ease-in-out">
        <div class="mt-6">
            <label for="document" class="block text-sm md:text-base font-medium text-gray-700 mb-3">
                <span class="block md:inline">Constancia de Situación Fiscal</span>
                <span class="text-sm text-gray-500 block md:inline md:ml-1">(PDF o Imagen)</span>
            </label>
            <div class="relative">
                <input type="file" id="document" name="document" accept=".pdf,.png,.jpg,.jpeg" required
                       class="hidden">
                <label for="document" class="group flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-primary/20 hover:border-primary rounded-xl transition-all duration-300 cursor-pointer bg-primary-50/30 hover:bg-primary-50">
                    <div class="flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-4 px-4">
                        <!-- Icono -->
                        <div class="transform group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-primary/70 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <!-- Texto -->
                        <div class="text-center md:text-left">
                            <p class="text-primary/70 group-hover:text-primary font-medium mb-1 transition-colors duration-300">
                                Haga clic para seleccionar archivo
                            </p>
                            <p class="text-sm text-gray-500" id="fileName">
                                PDF o Imagen con QR (Máximo 5MB)
                            </p>
                        </div>
                    </div>
                </label>
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
    <div id="registrationForm" class="hidden space-y-4 transition-all duration-300 ease-in-out">
        <input type="hidden" id="qrUrl" name="qr_url">
        
        <!-- Botón Ver Datos del SAT -->
        <button type="button" 
                id="verDatosBtn"
                onclick="showSatModal()"
                class="hidden inline-flex items-center text-sm bg-white hover:bg-primary-50 text-primary font-medium py-2 px-3 rounded-lg transition-all duration-300 shadow-sm hover:shadow border border-primary/20 hover:border-primary/40 mb-2">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver Datos del SAT
        </button>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
            <input type="email" id="email" name="email" required 
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300"
                   placeholder="ejemplo@correo.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <div class="relative">
                <input type="password" id="password" name="password" required 
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300"
                       placeholder="••••••••">
                <button type="button" 
                        onclick="togglePassword('password')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password-toggle-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-300"
                       placeholder="••••••••">
                <button type="button" 
                        onclick="togglePassword('password_confirmation')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password_confirmation-toggle-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Canvas para renderizar PDF (oculto) -->
    <canvas id="pdfCanvas" class="hidden"></canvas>

    <!-- Botones de acción -->
    <div class="space-y-3 pt-4">
        <button type="submit" class="group w-full bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 shadow-button hover:shadow-button-hover transform hover:-translate-y-0.5 relative overflow-hidden">
            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>REGISTRARSE</span>
            </div>
        </button>

        <a href="{{ route('login') }}" class="group w-full bg-white hover:bg-primary-50 text-primary font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 border-2 border-primary/20 hover:border-primary/40 relative overflow-hidden inline-flex items-center justify-center">
            <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>VOLVER AL LOGIN</span>
            </div>
        </a>
    </div>
</form>

<!-- Scripts -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
<script type="module">
    import QRHandler from '/js/components/qr-handler.js';
    import QRReader from '/js/components/qr-reader.js';
    import SATValidator from '/js/validators/sat-validator.js';
    import SATScraper from '/js/scrapers/sat-scraper.js';
    
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            console.log('Inicializando QRHandler para registro...');
            
            // Crear e inicializar QRHandler
            const qrHandler = new QRHandler();
            await qrHandler.initialize(QRReader, SATValidator, SATScraper);

            // Configurar callbacks
            qrHandler.setOnDataScanned((data) => {
                console.log('Datos escaneados:', data);
                
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

                // Autocompletar email si está disponible
                if (data.details && data.details.email) {
                    const emailInput = document.getElementById('email');
                    if (emailInput) {
                        emailInput.value = data.details.email;
                    }
                }

                // Ocultar botón de enviar hasta que el formulario esté completo
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.classList.add('hidden');
                }

                // Agregar validación en tiempo real
                const form = document.querySelector('form');
                const inputs = form.querySelectorAll('input[required]');
                inputs.forEach(input => {
                    input.addEventListener('input', validateForm);
                });
            });

            qrHandler.setOnError((error) => {
                console.error('Error en QRHandler:', error);
                showError(error);
                resetUpload();
            });

            // Asignar a window para acceso global
            window.qrHandler = qrHandler;
            console.log('QRHandler inicializado correctamente');

        } catch (error) {
            console.error('Error durante la inicialización:', error);
            showError('Error al inicializar el lector QR');
        }
    });

    // Función para validar el formulario
    function validateForm() {
        const form = document.querySelector('form');
        const submitButton = document.querySelector('button[type="submit"]');
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
        const passwordsMatch = password.value === passwordConfirmation.value;

        if (submitButton) {
            if (allFilled && passwordsMatch) {
                submitButton.classList.remove('hidden');
            } else {
                submitButton.classList.add('hidden');
            }
        }
    }

    // Función para mostrar el modal con datos del SAT
    window.showSatModal = function() {
        const modal = document.getElementById('satDataModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            const result = window.qrHandler.showSatData();
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
            if (!window.qrHandler) {
                throw new Error('El lector QR no está inicializado');
            }

            await window.qrHandler.handleFile(file);

        } catch (error) {
            console.error('Error detallado:', error);
            showError(error.message || 'Error al procesar el documento');
            resetUpload();
        }
    });
</script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.4.120/build/pdf.worker.min.js';

    // Funciones de utilidad
    function showLoading(show = true) {
        const loadingIndicator = document.getElementById('loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.classList.toggle('hidden', !show);
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

        // Mostrar área de subida
        const uploadArea = document.getElementById('uploadArea');
        if (uploadArea) {
            uploadArea.classList.remove('hidden');
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

        if (window.qrHandler) {
            window.qrHandler.reset();
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