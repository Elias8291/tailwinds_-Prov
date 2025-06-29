/**
 * Validador para la sección de Documentos
 * Maneja la validación de documentos subidos
 */

// Variables globales
let documentosValidados = false;

// Configuración de validación para documentos
const documentosValidationConfig = {
    allowedExtensions: ['pdf'],
    maxFileSize: 10 * 1024 * 1024, // 10MB en bytes
    minRequiredDocuments: 1
};

// Mensajes de error específicos
const documentosErrorMessages = {
    file_required: 'Debe seleccionar un archivo',
    file_extension: 'Solo se permiten archivos PDF',
    file_size: 'El archivo debe ser menor a 10MB',
    min_documents: 'Debe subir al menos un documento',
    upload_error: 'Error al subir el archivo'
};

// Estado de validación de documentos
let documentosStates = {};

// Función principal de inicialización
function inicializarValidacionDocumentos() {
    const formulario = document.getElementById('form-documentos');
    
    if (!formulario) {
        return;
    }

    // Inicializar validaciones y eventos
    configurarEventosDocumentos(formulario);
}

// Configurar eventos de validación en tiempo real
function setupDocumentosValidationEvents() {
    // Usar un observer para detectar cambios en el contenedor de documentos
    const contenedor = document.querySelector('.space-y-6');
    if (contenedor) {
        // Configurar MutationObserver para detectar cambios en documentos
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    setupDocumentosFieldEvents();
                }
            });
        });
        
        observer.observe(contenedor, {
            childList: true,
            subtree: true
        });
        
        // Configurar eventos iniciales
        setupDocumentosFieldEvents();
    }
}

// Configurar eventos para campos de documentos
function setupDocumentosFieldEvents() {
    // Obtener todos los inputs de archivo
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        // Remover listeners previos
        input.removeEventListener('change', handleDocumentoValidation);
        
        // Agregar nuevos listeners
        input.addEventListener('change', handleDocumentoValidation);
    });
}

// Manejar validación de archivo
function handleDocumentoValidation(e) {
    const input = e.target;
    const file = input.files[0];
    
    if (file) {
        validarArchivoDocumento(input, file);
    } else {
        limpiarErrorDocumento(input);
    }
}

// Configurar intercepción del submit
function setupDocumentosSubmitInterception() {
    const form = document.querySelector('form[x-ref="documentosForm"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioDocumentosCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un archivo específico
function validarArchivoDocumento(input, file) {
    const documentoId = input.dataset.documentoId || 'unknown';
    
    // Validar extensión
    const extension = file.name.split('.').pop().toLowerCase();
    if (!documentosValidationConfig.allowedExtensions.includes(extension)) {
        mostrarEstadoInvalidoDocumento(input, documentosErrorMessages.file_extension);
        documentosStates[documentoId] = false;
        return false;
    }
    
    // Validar tamaño
    if (file.size > documentosValidationConfig.maxFileSize) {
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        mostrarEstadoInvalidoDocumento(input, `El archivo pesa ${sizeMB}MB. ${documentosErrorMessages.file_size}`);
        documentosStates[documentoId] = false;
        return false;
    }
    
    // Archivo válido
    mostrarEstadoValidoDocumento(input, file);
    documentosStates[documentoId] = true;
    return true;
}

// Validar formulario completo de documentos
function validarFormularioDocumentosCompleto() {
    let isValid = true;
    let camposConError = [];
    let documentosSubidos = 0;

    // Obtener todos los inputs de archivo
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        const file = input.files[0];
        const documentoNombre = input.closest('div')?.querySelector('h4')?.textContent || 'Documento';
        const isRequired = input.hasAttribute('required');
        
        if (isRequired && !file) {
            isValid = false;
            camposConError.push({
                nombre: documentoNombre,
                errores: [documentosErrorMessages.file_required]
            });
            mostrarEstadoInvalidoDocumento(input, documentosErrorMessages.file_required);
        } else if (file) {
            const fileValid = validarArchivoDocumento(input, file);
            if (!fileValid) {
                isValid = false;
                camposConError.push({
                    nombre: documentoNombre,
                    errores: [documentosErrorMessages.file_extension + ' o ' + documentosErrorMessages.file_size]
                });
            } else {
                documentosSubidos++;
            }
        }
    });

    // Verificar mínimo de documentos requeridos
    if (documentosSubidos < documentosValidationConfig.minRequiredDocuments) {
        isValid = false;
        camposConError.push({
            nombre: 'Documentos Requeridos',
            errores: [documentosErrorMessages.min_documents]
        });
    }

    if (!isValid) {
        mostrarModalErroresDocumentos(camposConError);
        return false;
    }

    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDocumento(input, file) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50');
        container.classList.add('border-green-300', 'bg-green-50');
        }
        
    // Mostrar info del archivo
        mostrarInfoArchivo(container, file);
    
    // Remover mensajes de error
    limpiarErrorDocumento(input);
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDocumento(input, mensaje) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
    container.classList.remove('border-green-300', 'bg-green-50');
    container.classList.add('border-red-300', 'bg-red-50');
    }
    
    // Mostrar mensaje de error
    let errorDiv = input.parentNode.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-600 text-sm mt-2';
        input.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = mensaje;
}

// Limpiar estado de error
function limpiarErrorDocumento(input) {
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Mostrar información del archivo
function mostrarInfoArchivo(container, file) {
    if (!container || !file) return;
    
    const existingInfo = container.querySelector('.file-info');
    if (existingInfo) {
        existingInfo.remove();
    }
    
    const fileInfo = document.createElement('div');
    fileInfo.className = 'file-info text-sm text-gray-600 mt-2';
    fileInfo.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
            <span>${file.name}</span>
            <span class="ml-2 text-gray-400">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
        </div>
    `;
    
    container.appendChild(fileInfo);
}

// Mostrar modal de errores
function mostrarModalErroresDocumentos(camposConError) {
    // Implementación del modal de errores
}

// Integración con Alpine.js
document.addEventListener('DOMContentLoaded', function() {
    inicializarValidacionDocumentos();
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('documentosValidation', () => ({
        init() {
            inicializarValidacionDocumentos();
        }
    }));
} 