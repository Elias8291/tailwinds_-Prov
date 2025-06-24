/**
 * Validador para el formulario de Documentos
 * Valida la subida de archivos PDF y feedback visual
 */

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
function initDocumentosValidation() {
    console.log('Inicializando validación de documentos...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="documentosForm"]');
    if (!form) {
        console.log('Formulario de documentos no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupDocumentosValidationEvents();
    
    // Configurar intercepción del submit
    setupDocumentosSubmitInterception();
    
    console.log('Validación de documentos inicializada correctamente');
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
    console.log('Validando formulario de documentos completo...');
    
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

    console.log('Formulario de documentos válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDocumento(input, file) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50');
        container.classList.add('border-green-300', 'bg-green-50');
        
        // Remover mensaje de error si existe
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        
        // Mostrar información del archivo
        mostrarInfoArchivo(container, file);
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDocumento(input, mensaje) {
    const container = input.closest('.bg-white, .border-dashed');
    if (!container) return;
    
    container.classList.remove('border-green-300', 'bg-green-50');
    container.classList.add('border-red-300', 'bg-red-50');
    
    // Remover mensaje anterior
    let mensajeError = container.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-2 text-sm text-red-600 flex items-center';
    mensajeError.innerHTML = `
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>${mensaje}</span>
    `;
    
    // Insertar después del input o al final del container
    const inputContainer = input.closest('.relative') || container;
    inputContainer.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorDocumento(input) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
        
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        
        const fileInfo = container.querySelector('.file-info');
        if (fileInfo) {
            fileInfo.remove();
        }
    }
}

// Mostrar información del archivo
function mostrarInfoArchivo(container, file) {
    // Remover info anterior
    const infoAnterior = container.querySelector('.file-info');
    if (infoAnterior) {
        infoAnterior.remove();
    }
    
    const fileInfo = document.createElement('div');
    fileInfo.className = 'file-info mt-2 p-2 bg-green-100 border border-green-300 rounded-lg text-sm';
    
    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    fileInfo.innerHTML = `
        <div class="flex items-center text-green-700">
            <i class="fas fa-file-pdf mr-2"></i>
            <div>
                <div class="font-medium">${file.name}</div>
                <div class="text-xs text-green-600">${sizeMB} MB</div>
            </div>
            <i class="fas fa-check-circle ml-auto text-green-500"></i>
        </div>
    `;
    
    container.appendChild(fileInfo);
}

// Modal de errores específico para documentos
function mostrarModalErroresDocumentos(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-documentos');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-documentos';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoDocumento('${campo.nombre}', ${index})">
                    <i class="fas fa-file-upload mr-2 text-red-600"></i>
                    ${campo.nombre}
                    <i class="fas fa-external-link-alt ml-2 text-xs text-red-500"></i>
                </h4>
                <ul class="space-y-1">
                    ${campo.errores.map(error => `
                        <li class="text-red-700 text-sm flex items-start">
                            <i class="fas fa-circle mr-2 text-red-400 text-xs mt-1.5 flex-shrink-0"></i>
                            <span>${error}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-appear">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-3">
                                <i class="fas fa-file-upload text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Documentos
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los documentos
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresDocumentos()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los documentos:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un documento para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} documento${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErroresDocumentos()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Eventos de cierre
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalErroresDocumentos();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresDocumentos();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresDocumentos() {
    const modal = document.getElementById('modal-errores-documentos');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un documento específico desde el modal
function irACampoDocumento(nombreDocumento, index) {
    cerrarModalErroresDocumentos();
    
    setTimeout(() => {
        let elemento = null;
        
        // Buscar el documento por el nombre en el h4
        const documentos = document.querySelectorAll('h4');
        for (let h4 of documentos) {
            if (h4.textContent.includes(nombreDocumento)) {
                const input = h4.closest('div')?.querySelector('input[type="file"]');
                if (input) {
                    elemento = input;
                    break;
                }
            }
        }
        
        // Si no se encontró, buscar el primer input de archivo
        if (!elemento) {
            elemento = document.querySelector('input[type="file"]');
        }
        
        if (elemento) {
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            setTimeout(() => {
                elemento.focus();
                const container = elemento.closest('.bg-white, .border-dashed');
                if (container) {
                    container.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                    setTimeout(() => {
                        container.style.boxShadow = '';
                    }, 2000);
                }
            }, 500);
        }
    }, 350);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que Alpine.js esté inicializado
    setTimeout(initDocumentosValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('documentosValidation', () => ({
        init() {
            initDocumentosValidation();
        }
    }));
} 
 * Validador para el formulario de Documentos
 * Valida la subida de archivos PDF y feedback visual
 */

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
function initDocumentosValidation() {
    console.log('Inicializando validación de documentos...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="documentosForm"]');
    if (!form) {
        console.log('Formulario de documentos no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupDocumentosValidationEvents();
    
    // Configurar intercepción del submit
    setupDocumentosSubmitInterception();
    
    console.log('Validación de documentos inicializada correctamente');
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
    console.log('Validando formulario de documentos completo...');
    
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

    console.log('Formulario de documentos válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDocumento(input, file) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50');
        container.classList.add('border-green-300', 'bg-green-50');
        
        // Remover mensaje de error si existe
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        
        // Mostrar información del archivo
        mostrarInfoArchivo(container, file);
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDocumento(input, mensaje) {
    const container = input.closest('.bg-white, .border-dashed');
    if (!container) return;
    
    container.classList.remove('border-green-300', 'bg-green-50');
    container.classList.add('border-red-300', 'bg-red-50');
    
    // Remover mensaje anterior
    let mensajeError = container.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-2 text-sm text-red-600 flex items-center';
    mensajeError.innerHTML = `
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>${mensaje}</span>
    `;
    
    // Insertar después del input o al final del container
    const inputContainer = input.closest('.relative') || container;
    inputContainer.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorDocumento(input) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
        
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        
        const fileInfo = container.querySelector('.file-info');
        if (fileInfo) {
            fileInfo.remove();
        }
    }
}

// Mostrar información del archivo
function mostrarInfoArchivo(container, file) {
    // Remover info anterior
    const infoAnterior = container.querySelector('.file-info');
    if (infoAnterior) {
        infoAnterior.remove();
    }
    
    const fileInfo = document.createElement('div');
    fileInfo.className = 'file-info mt-2 p-2 bg-green-100 border border-green-300 rounded-lg text-sm';
    
    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    fileInfo.innerHTML = `
        <div class="flex items-center text-green-700">
            <i class="fas fa-file-pdf mr-2"></i>
            <div>
                <div class="font-medium">${file.name}</div>
                <div class="text-xs text-green-600">${sizeMB} MB</div>
            </div>
            <i class="fas fa-check-circle ml-auto text-green-500"></i>
        </div>
    `;
    
    container.appendChild(fileInfo);
}

// Modal de errores específico para documentos
function mostrarModalErroresDocumentos(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-documentos');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-documentos';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoDocumento('${campo.nombre}', ${index})">
                    <i class="fas fa-file-upload mr-2 text-red-600"></i>
                    ${campo.nombre}
                    <i class="fas fa-external-link-alt ml-2 text-xs text-red-500"></i>
                </h4>
                <ul class="space-y-1">
                    ${campo.errores.map(error => `
                        <li class="text-red-700 text-sm flex items-start">
                            <i class="fas fa-circle mr-2 text-red-400 text-xs mt-1.5 flex-shrink-0"></i>
                            <span>${error}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-appear">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-3">
                                <i class="fas fa-file-upload text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Documentos
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los documentos
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresDocumentos()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los documentos:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un documento para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} documento${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErroresDocumentos()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Eventos de cierre
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalErroresDocumentos();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresDocumentos();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresDocumentos() {
    const modal = document.getElementById('modal-errores-documentos');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un documento específico desde el modal
function irACampoDocumento(nombreDocumento, index) {
    cerrarModalErroresDocumentos();
    
    setTimeout(() => {
        let elemento = null;
        
        // Buscar el documento por el nombre en el h4
        const documentos = document.querySelectorAll('h4');
        for (let h4 of documentos) {
            if (h4.textContent.includes(nombreDocumento)) {
                const input = h4.closest('div')?.querySelector('input[type="file"]');
                if (input) {
                    elemento = input;
                    break;
                }
            }
        }
        
        // Si no se encontró, buscar el primer input de archivo
        if (!elemento) {
            elemento = document.querySelector('input[type="file"]');
        }
        
        if (elemento) {
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            setTimeout(() => {
                elemento.focus();
                const container = elemento.closest('.bg-white, .border-dashed');
                if (container) {
                    container.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                    setTimeout(() => {
                        container.style.boxShadow = '';
                    }, 2000);
                }
            }, 500);
        }
    }, 350);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que Alpine.js esté inicializado
    setTimeout(initDocumentosValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('documentosValidation', () => ({
        init() {
            initDocumentosValidation();
        }
    }));
} 
 * Validador para el formulario de Documentos
 * Valida la subida de archivos PDF y feedback visual
 */

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
function initDocumentosValidation() {
    console.log('Inicializando validación de documentos...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="documentosForm"]');
    if (!form) {
        console.log('Formulario de documentos no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupDocumentosValidationEvents();
    
    // Configurar intercepción del submit
    setupDocumentosSubmitInterception();
    
    console.log('Validación de documentos inicializada correctamente');
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
    console.log('Validando formulario de documentos completo...');
    
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

    console.log('Formulario de documentos válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDocumento(input, file) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50');
        container.classList.add('border-green-300', 'bg-green-50');
        
        // Remover mensaje de error si existe
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        
        // Mostrar información del archivo
        mostrarInfoArchivo(container, file);
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDocumento(input, mensaje) {
    const container = input.closest('.bg-white, .border-dashed');
    if (!container) return;
    
    container.classList.remove('border-green-300', 'bg-green-50');
    container.classList.add('border-red-300', 'bg-red-50');
    
    // Remover mensaje anterior
    let mensajeError = container.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-2 text-sm text-red-600 flex items-center';
    mensajeError.innerHTML = `
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>${mensaje}</span>
    `;
    
    // Insertar después del input o al final del container
    const inputContainer = input.closest('.relative') || container;
    inputContainer.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorDocumento(input) {
    const container = input.closest('.bg-white, .border-dashed');
    if (container) {
        container.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
        
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        
        const fileInfo = container.querySelector('.file-info');
        if (fileInfo) {
            fileInfo.remove();
        }
    }
}

// Mostrar información del archivo
function mostrarInfoArchivo(container, file) {
    // Remover info anterior
    const infoAnterior = container.querySelector('.file-info');
    if (infoAnterior) {
        infoAnterior.remove();
    }
    
    const fileInfo = document.createElement('div');
    fileInfo.className = 'file-info mt-2 p-2 bg-green-100 border border-green-300 rounded-lg text-sm';
    
    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    fileInfo.innerHTML = `
        <div class="flex items-center text-green-700">
            <i class="fas fa-file-pdf mr-2"></i>
            <div>
                <div class="font-medium">${file.name}</div>
                <div class="text-xs text-green-600">${sizeMB} MB</div>
            </div>
            <i class="fas fa-check-circle ml-auto text-green-500"></i>
        </div>
    `;
    
    container.appendChild(fileInfo);
}

// Modal de errores específico para documentos
function mostrarModalErroresDocumentos(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-documentos');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-documentos';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoDocumento('${campo.nombre}', ${index})">
                    <i class="fas fa-file-upload mr-2 text-red-600"></i>
                    ${campo.nombre}
                    <i class="fas fa-external-link-alt ml-2 text-xs text-red-500"></i>
                </h4>
                <ul class="space-y-1">
                    ${campo.errores.map(error => `
                        <li class="text-red-700 text-sm flex items-start">
                            <i class="fas fa-circle mr-2 text-red-400 text-xs mt-1.5 flex-shrink-0"></i>
                            <span>${error}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    });
    
    modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-modal-appear">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-3">
                                <i class="fas fa-file-upload text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Documentos
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los documentos
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresDocumentos()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los documentos:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un documento para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} documento${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErroresDocumentos()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Eventos de cierre
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalErroresDocumentos();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresDocumentos();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresDocumentos() {
    const modal = document.getElementById('modal-errores-documentos');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un documento específico desde el modal
function irACampoDocumento(nombreDocumento, index) {
    cerrarModalErroresDocumentos();
    
    setTimeout(() => {
        let elemento = null;
        
        // Buscar el documento por el nombre en el h4
        const documentos = document.querySelectorAll('h4');
        for (let h4 of documentos) {
            if (h4.textContent.includes(nombreDocumento)) {
                const input = h4.closest('div')?.querySelector('input[type="file"]');
                if (input) {
                    elemento = input;
                    break;
                }
            }
        }
        
        // Si no se encontró, buscar el primer input de archivo
        if (!elemento) {
            elemento = document.querySelector('input[type="file"]');
        }
        
        if (elemento) {
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            setTimeout(() => {
                elemento.focus();
                const container = elemento.closest('.bg-white, .border-dashed');
                if (container) {
                    container.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                    setTimeout(() => {
                        container.style.boxShadow = '';
                    }, 2000);
                }
            }, 500);
        }
    }, 350);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que Alpine.js esté inicializado
    setTimeout(initDocumentosValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('documentosValidation', () => ({
        init() {
            initDocumentosValidation();
        }
    }));
} 