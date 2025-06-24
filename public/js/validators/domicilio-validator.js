/**
 * Validador para el formulario de Domicilio
 * Valida campos específicos con patrones regex y feedback visual
 */

// Patrones de validación para domicilio
const domicilioValidationPatterns = {
    codigo_postal: /^[0-9]{4,5}$/,
    estado: /^.{2,50}$/,
    municipio: /^.{2,50}$/,
    calle: /^.{3,100}$/,
    numero_exterior: /^[a-zA-Z0-9\s\-\.]{1,10}$/,
    numero_interior: /^[a-zA-Z0-9\s\-\.]{0,10}$/,
    entre_calle_1: /^.{0,100}$/,
    entre_calle_2: /^.{0,100}$/
};

// Mensajes de error específicos
const domicilioErrorMessages = {
    codigo_postal: 'El código postal debe tener 4 o 5 dígitos',
    estado: 'El estado debe tener entre 2 y 50 caracteres',
    municipio: 'El municipio debe tener entre 2 y 50 caracteres',
    colonia: 'Debe seleccionar un asentamiento',
    calle: 'La calle debe tener entre 3 y 100 caracteres',
    numero_exterior: 'El número exterior debe tener máximo 10 caracteres',
    numero_interior: 'El número interior debe tener máximo 10 caracteres (opcional)',
    entre_calle_1: 'Máximo 100 caracteres',
    entre_calle_2: 'Máximo 100 caracteres'
};

// Estado de validación de campos
let domicilioFieldStates = {};

// Función principal de inicialización
function initDomicilioValidation() {
    console.log('Inicializando validación de domicilio...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[data-validate="true"]');
    if (!form) {
        console.log('Formulario de domicilio no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupDomicilioValidationEvents();
    
    // Configurar intercepción del submit
    setupDomicilioSubmitInterception();
    
    console.log('Validación de domicilio inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupDomicilioValidationEvents() {
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            // Evento al escribir (keyup)
            input.addEventListener('keyup', () => {
                validarCampoDomicilio(fieldName);
                limpiarErrorDomicilio(input);
            });
            
            // Evento al perder el foco (blur)
            input.addEventListener('blur', () => {
                validarCampoDomicilio(fieldName);
            });
            
            // Limpiar errores al empezar a escribir
            input.addEventListener('input', () => {
                limpiarErrorDomicilio(input);
            });
        }
    });

    // Validación especial para colonia (select)
    const coloniaSelect = document.querySelector('[name="colonia"]');
    if (coloniaSelect) {
        coloniaSelect.addEventListener('change', () => {
            validarColoniaDomicilio();
            limpiarErrorDomicilio(coloniaSelect);
        });
        
        coloniaSelect.addEventListener('focus', () => {
            limpiarErrorDomicilio(coloniaSelect);
        });
    }
}

// Configurar intercepción del submit
function setupDomicilioSubmitInterception() {
    const originalGuardarDomicilio = window.domicilioData().guardarDomicilio;
    
    if (typeof originalGuardarDomicilio === 'function') {
        window.domicilioData().guardarDomicilio = function() {
            if (validarFormularioDomicilioCompleto()) {
                originalGuardarDomicilio.call(this);
            }
        };
    }

    // También interceptar el submit del formulario directamente
    const form = document.querySelector('form[data-validate="true"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioDomicilioCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico
function validarCampoDomicilio(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (!input) return false;

    const value = input.value.trim();
    const pattern = domicilioValidationPatterns[fieldName];
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoDomicilio(input, `Este campo es obligatorio`);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoDomicilio(input, domicilioErrorMessages[fieldName]);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
    mostrarEstadoValidoDomicilio(input);
    domicilioFieldStates[fieldName] = true;
    return true;
}

// Validar colonia (select)
function validarColoniaDomicilio() {
    const select = document.querySelector('[name="colonia"]');
    if (!select) return false;

    const value = select.value;
    const isRequired = select.hasAttribute('required');
    
    if (isRequired && (value === '' || value === null)) {
        mostrarEstadoInvalidoDomicilio(select, domicilioErrorMessages.colonia);
        domicilioFieldStates.colonia = false;
        return false;
    }
    
    mostrarEstadoValidoDomicilio(select);
    domicilioFieldStates.colonia = true;
    return true;
}

// Validar formulario completo
function validarFormularioDomicilioCompleto() {
    console.log('Validando formulario de domicilio completo...');
    
    let isValid = true;
    let camposConError = [];

    // Validar campos con patrones
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const fieldValid = validarCampoDomicilio(fieldName);
        if (!fieldValid) {
            isValid = false;
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || fieldName;
                const errorMessage = domicilioErrorMessages[fieldName] || 'Campo inválido';
                camposConError.push({
                    nombre: label,
                    errores: [errorMessage]
                });
            }
        }
    });

    // Validar colonia
    const coloniaValid = validarColoniaDomicilio();
    if (!coloniaValid) {
        isValid = false;
        const select = document.querySelector('[name="colonia"]');
        if (select) {
            const label = select.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Colonia';
            camposConError.push({
                nombre: label,
                errores: [domicilioErrorMessages.colonia]
            });
        }
    }

    if (!isValid) {
        mostrarModalErroresDomicilio(camposConError);
        return false;
    }

    console.log('Formulario de domicilio válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDomicilio(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensaje de error si existe
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDomicilio(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    const grupo = input.closest('.form-group');
    if (!grupo) return;
    
    // Remover mensaje anterior
    let mensajeError = grupo.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-1 text-sm text-red-600';
    mensajeError.textContent = mensaje;
    grupo.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorDomicilio(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Limpiar todos los errores
function limpiarErroresDomicilio() {
    document.querySelectorAll('.border-red-500').forEach(element => {
        element.classList.remove('border-red-500', 'bg-red-50');
    });
    
    document.querySelectorAll('.error-message').forEach(element => {
        element.remove();
    });
    
    // Cerrar modal de errores si existe
    const modal = document.getElementById('modal-errores-domicilio');
    if (modal) {
        modal.remove();
    }
}

// Modal de errores específico para domicilio
function mostrarModalErroresDomicilio(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-domicilio');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-domicilio';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoDomicilio('${campo.nombre}', ${index})">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
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
                                <i class="fas fa-map-marker-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Domicilio
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos del domicilio
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresDomicilio()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos del domicilio:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un campo para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} campo${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErroresDomicilio()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresDomicilio();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresDomicilio();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresDomicilio() {
    const modal = document.getElementById('modal-errores-domicilio');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoDomicilio(nombreCampo, index) {
    cerrarModalErroresDomicilio();
    
    setTimeout(() => {
        let elemento = null;
        
        // Buscar por el label text
        const labels = document.querySelectorAll('label');
        for (let label of labels) {
            if (label.textContent.replace('*', '').trim() === nombreCampo) {
                const input = label.nextElementSibling?.querySelector('input, select') || 
                             document.querySelector(`[name="${label.getAttribute('for')}"]`);
                if (input) {
                    elemento = input;
                    break;
                }
            }
        }
        
        // Si no se encontró, buscar directamente por name
        if (!elemento) {
            elemento = document.querySelector(`[name*="${nombreCampo.toLowerCase()}"]`);
        }
        
        if (elemento) {
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            setTimeout(() => {
                elemento.focus();
                elemento.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                setTimeout(() => {
                    elemento.style.boxShadow = '';
                }, 2000);
            }, 500);
        }
    }, 350);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que Alpine.js esté inicializado
    setTimeout(initDomicilioValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('domicilioValidation', () => ({
        init() {
            initDomicilioValidation();
        }
    }));
} 
 * Validador para el formulario de Domicilio
 * Valida campos específicos con patrones regex y feedback visual
 */

// Patrones de validación para domicilio
const domicilioValidationPatterns = {
    codigo_postal: /^[0-9]{4,5}$/,
    estado: /^.{2,50}$/,
    municipio: /^.{2,50}$/,
    calle: /^.{3,100}$/,
    numero_exterior: /^[a-zA-Z0-9\s\-\.]{1,10}$/,
    numero_interior: /^[a-zA-Z0-9\s\-\.]{0,10}$/,
    entre_calle_1: /^.{0,100}$/,
    entre_calle_2: /^.{0,100}$/
};

// Mensajes de error específicos
const domicilioErrorMessages = {
    codigo_postal: 'El código postal debe tener 4 o 5 dígitos',
    estado: 'El estado debe tener entre 2 y 50 caracteres',
    municipio: 'El municipio debe tener entre 2 y 50 caracteres',
    colonia: 'Debe seleccionar un asentamiento',
    calle: 'La calle debe tener entre 3 y 100 caracteres',
    numero_exterior: 'El número exterior debe tener máximo 10 caracteres',
    numero_interior: 'El número interior debe tener máximo 10 caracteres (opcional)',
    entre_calle_1: 'Máximo 100 caracteres',
    entre_calle_2: 'Máximo 100 caracteres'
};

// Estado de validación de campos
let domicilioFieldStates = {};

// Función principal de inicialización
function initDomicilioValidation() {
    console.log('Inicializando validación de domicilio...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[data-validate="true"]');
    if (!form) {
        console.log('Formulario de domicilio no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupDomicilioValidationEvents();
    
    // Configurar intercepción del submit
    setupDomicilioSubmitInterception();
    
    console.log('Validación de domicilio inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupDomicilioValidationEvents() {
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            // Evento al escribir (keyup)
            input.addEventListener('keyup', () => {
                validarCampoDomicilio(fieldName);
                limpiarErrorDomicilio(input);
            });
            
            // Evento al perder el foco (blur)
            input.addEventListener('blur', () => {
                validarCampoDomicilio(fieldName);
            });
            
            // Limpiar errores al empezar a escribir
            input.addEventListener('input', () => {
                limpiarErrorDomicilio(input);
            });
        }
    });

    // Validación especial para colonia (select)
    const coloniaSelect = document.querySelector('[name="colonia"]');
    if (coloniaSelect) {
        coloniaSelect.addEventListener('change', () => {
            validarColoniaDomicilio();
            limpiarErrorDomicilio(coloniaSelect);
        });
        
        coloniaSelect.addEventListener('focus', () => {
            limpiarErrorDomicilio(coloniaSelect);
        });
    }
}

// Configurar intercepción del submit
function setupDomicilioSubmitInterception() {
    const originalGuardarDomicilio = window.domicilioData().guardarDomicilio;
    
    if (typeof originalGuardarDomicilio === 'function') {
        window.domicilioData().guardarDomicilio = function() {
            if (validarFormularioDomicilioCompleto()) {
                originalGuardarDomicilio.call(this);
            }
        };
    }

    // También interceptar el submit del formulario directamente
    const form = document.querySelector('form[data-validate="true"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioDomicilioCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico
function validarCampoDomicilio(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (!input) return false;

    const value = input.value.trim();
    const pattern = domicilioValidationPatterns[fieldName];
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoDomicilio(input, `Este campo es obligatorio`);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoDomicilio(input, domicilioErrorMessages[fieldName]);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
    mostrarEstadoValidoDomicilio(input);
    domicilioFieldStates[fieldName] = true;
    return true;
}

// Validar colonia (select)
function validarColoniaDomicilio() {
    const select = document.querySelector('[name="colonia"]');
    if (!select) return false;

    const value = select.value;
    const isRequired = select.hasAttribute('required');
    
    if (isRequired && (value === '' || value === null)) {
        mostrarEstadoInvalidoDomicilio(select, domicilioErrorMessages.colonia);
        domicilioFieldStates.colonia = false;
        return false;
    }
    
    mostrarEstadoValidoDomicilio(select);
    domicilioFieldStates.colonia = true;
    return true;
}

// Validar formulario completo
function validarFormularioDomicilioCompleto() {
    console.log('Validando formulario de domicilio completo...');
    
    let isValid = true;
    let camposConError = [];

    // Validar campos con patrones
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const fieldValid = validarCampoDomicilio(fieldName);
        if (!fieldValid) {
            isValid = false;
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || fieldName;
                const errorMessage = domicilioErrorMessages[fieldName] || 'Campo inválido';
                camposConError.push({
                    nombre: label,
                    errores: [errorMessage]
                });
            }
        }
    });

    // Validar colonia
    const coloniaValid = validarColoniaDomicilio();
    if (!coloniaValid) {
        isValid = false;
        const select = document.querySelector('[name="colonia"]');
        if (select) {
            const label = select.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Colonia';
            camposConError.push({
                nombre: label,
                errores: [domicilioErrorMessages.colonia]
            });
        }
    }

    if (!isValid) {
        mostrarModalErroresDomicilio(camposConError);
        return false;
    }

    console.log('Formulario de domicilio válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDomicilio(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensaje de error si existe
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDomicilio(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    const grupo = input.closest('.form-group');
    if (!grupo) return;
    
    // Remover mensaje anterior
    let mensajeError = grupo.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-1 text-sm text-red-600';
    mensajeError.textContent = mensaje;
    grupo.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorDomicilio(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Limpiar todos los errores
function limpiarErroresDomicilio() {
    document.querySelectorAll('.border-red-500').forEach(element => {
        element.classList.remove('border-red-500', 'bg-red-50');
    });
    
    document.querySelectorAll('.error-message').forEach(element => {
        element.remove();
    });
    
    // Cerrar modal de errores si existe
    const modal = document.getElementById('modal-errores-domicilio');
    if (modal) {
        modal.remove();
    }
}

// Modal de errores específico para domicilio
function mostrarModalErroresDomicilio(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-domicilio');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-domicilio';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoDomicilio('${campo.nombre}', ${index})">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
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
                                <i class="fas fa-map-marker-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Domicilio
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos del domicilio
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresDomicilio()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos del domicilio:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un campo para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} campo${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErroresDomicilio()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresDomicilio();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresDomicilio();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresDomicilio() {
    const modal = document.getElementById('modal-errores-domicilio');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoDomicilio(nombreCampo, index) {
    cerrarModalErroresDomicilio();
    
    setTimeout(() => {
        let elemento = null;
        
        // Buscar por el label text
        const labels = document.querySelectorAll('label');
        for (let label of labels) {
            if (label.textContent.replace('*', '').trim() === nombreCampo) {
                const input = label.nextElementSibling?.querySelector('input, select') || 
                             document.querySelector(`[name="${label.getAttribute('for')}"]`);
                if (input) {
                    elemento = input;
                    break;
                }
            }
        }
        
        // Si no se encontró, buscar directamente por name
        if (!elemento) {
            elemento = document.querySelector(`[name*="${nombreCampo.toLowerCase()}"]`);
        }
        
        if (elemento) {
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            setTimeout(() => {
                elemento.focus();
                elemento.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                setTimeout(() => {
                    elemento.style.boxShadow = '';
                }, 2000);
            }, 500);
        }
    }, 350);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que Alpine.js esté inicializado
    setTimeout(initDomicilioValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('domicilioValidation', () => ({
        init() {
            initDomicilioValidation();
        }
    }));
} 
 * Validador para el formulario de Domicilio
 * Valida campos específicos con patrones regex y feedback visual
 */

// Patrones de validación para domicilio
const domicilioValidationPatterns = {
    codigo_postal: /^[0-9]{4,5}$/,
    estado: /^.{2,50}$/,
    municipio: /^.{2,50}$/,
    calle: /^.{3,100}$/,
    numero_exterior: /^[a-zA-Z0-9\s\-\.]{1,10}$/,
    numero_interior: /^[a-zA-Z0-9\s\-\.]{0,10}$/,
    entre_calle_1: /^.{0,100}$/,
    entre_calle_2: /^.{0,100}$/
};

// Mensajes de error específicos
const domicilioErrorMessages = {
    codigo_postal: 'El código postal debe tener 4 o 5 dígitos',
    estado: 'El estado debe tener entre 2 y 50 caracteres',
    municipio: 'El municipio debe tener entre 2 y 50 caracteres',
    colonia: 'Debe seleccionar un asentamiento',
    calle: 'La calle debe tener entre 3 y 100 caracteres',
    numero_exterior: 'El número exterior debe tener máximo 10 caracteres',
    numero_interior: 'El número interior debe tener máximo 10 caracteres (opcional)',
    entre_calle_1: 'Máximo 100 caracteres',
    entre_calle_2: 'Máximo 100 caracteres'
};

// Estado de validación de campos
let domicilioFieldStates = {};

// Función principal de inicialización
function initDomicilioValidation() {
    console.log('Inicializando validación de domicilio...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[data-validate="true"]');
    if (!form) {
        console.log('Formulario de domicilio no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupDomicilioValidationEvents();
    
    // Configurar intercepción del submit
    setupDomicilioSubmitInterception();
    
    console.log('Validación de domicilio inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupDomicilioValidationEvents() {
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            // Evento al escribir (keyup)
            input.addEventListener('keyup', () => {
                validarCampoDomicilio(fieldName);
                limpiarErrorDomicilio(input);
            });
            
            // Evento al perder el foco (blur)
            input.addEventListener('blur', () => {
                validarCampoDomicilio(fieldName);
            });
            
            // Limpiar errores al empezar a escribir
            input.addEventListener('input', () => {
                limpiarErrorDomicilio(input);
            });
        }
    });

    // Validación especial para colonia (select)
    const coloniaSelect = document.querySelector('[name="colonia"]');
    if (coloniaSelect) {
        coloniaSelect.addEventListener('change', () => {
            validarColoniaDomicilio();
            limpiarErrorDomicilio(coloniaSelect);
        });
        
        coloniaSelect.addEventListener('focus', () => {
            limpiarErrorDomicilio(coloniaSelect);
        });
    }
}

// Configurar intercepción del submit
function setupDomicilioSubmitInterception() {
    const originalGuardarDomicilio = window.domicilioData().guardarDomicilio;
    
    if (typeof originalGuardarDomicilio === 'function') {
        window.domicilioData().guardarDomicilio = function() {
            if (validarFormularioDomicilioCompleto()) {
                originalGuardarDomicilio.call(this);
            }
        };
    }

    // También interceptar el submit del formulario directamente
    const form = document.querySelector('form[data-validate="true"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioDomicilioCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico
function validarCampoDomicilio(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (!input) return false;

    const value = input.value.trim();
    const pattern = domicilioValidationPatterns[fieldName];
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoDomicilio(input, `Este campo es obligatorio`);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoDomicilio(input, domicilioErrorMessages[fieldName]);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
    mostrarEstadoValidoDomicilio(input);
    domicilioFieldStates[fieldName] = true;
    return true;
}

// Validar colonia (select)
function validarColoniaDomicilio() {
    const select = document.querySelector('[name="colonia"]');
    if (!select) return false;

    const value = select.value;
    const isRequired = select.hasAttribute('required');
    
    if (isRequired && (value === '' || value === null)) {
        mostrarEstadoInvalidoDomicilio(select, domicilioErrorMessages.colonia);
        domicilioFieldStates.colonia = false;
        return false;
    }
    
    mostrarEstadoValidoDomicilio(select);
    domicilioFieldStates.colonia = true;
    return true;
}

// Validar formulario completo
function validarFormularioDomicilioCompleto() {
    console.log('Validando formulario de domicilio completo...');
    
    let isValid = true;
    let camposConError = [];

    // Validar campos con patrones
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const fieldValid = validarCampoDomicilio(fieldName);
        if (!fieldValid) {
            isValid = false;
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || fieldName;
                const errorMessage = domicilioErrorMessages[fieldName] || 'Campo inválido';
                camposConError.push({
                    nombre: label,
                    errores: [errorMessage]
                });
            }
        }
    });

    // Validar colonia
    const coloniaValid = validarColoniaDomicilio();
    if (!coloniaValid) {
        isValid = false;
        const select = document.querySelector('[name="colonia"]');
        if (select) {
            const label = select.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Colonia';
            camposConError.push({
                nombre: label,
                errores: [domicilioErrorMessages.colonia]
            });
        }
    }

    if (!isValid) {
        mostrarModalErroresDomicilio(camposConError);
        return false;
    }

    console.log('Formulario de domicilio válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDomicilio(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensaje de error si existe
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDomicilio(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    const grupo = input.closest('.form-group');
    if (!grupo) return;
    
    // Remover mensaje anterior
    let mensajeError = grupo.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-1 text-sm text-red-600';
    mensajeError.textContent = mensaje;
    grupo.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorDomicilio(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Limpiar todos los errores
function limpiarErroresDomicilio() {
    document.querySelectorAll('.border-red-500').forEach(element => {
        element.classList.remove('border-red-500', 'bg-red-50');
    });
    
    document.querySelectorAll('.error-message').forEach(element => {
        element.remove();
    });
    
    // Cerrar modal de errores si existe
    const modal = document.getElementById('modal-errores-domicilio');
    if (modal) {
        modal.remove();
    }
}

// Modal de errores específico para domicilio
function mostrarModalErroresDomicilio(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-domicilio');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-domicilio';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoDomicilio('${campo.nombre}', ${index})">
                    <i class="fas fa-map-marker-alt mr-2 text-red-600"></i>
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
                                <i class="fas fa-map-marker-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Domicilio
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos del domicilio
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresDomicilio()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos del domicilio:
                        </p>
                        ${erroresHTML}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>Haga clic en un campo para ir directamente a él</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                            ${camposConError.length} campo${camposConError.length !== 1 ? 's' : ''} con errores
                        </div>
                        <button onclick="cerrarModalErroresDomicilio()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresDomicilio();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresDomicilio();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresDomicilio() {
    const modal = document.getElementById('modal-errores-domicilio');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoDomicilio(nombreCampo, index) {
    cerrarModalErroresDomicilio();
    
    setTimeout(() => {
        let elemento = null;
        
        // Buscar por el label text
        const labels = document.querySelectorAll('label');
        for (let label of labels) {
            if (label.textContent.replace('*', '').trim() === nombreCampo) {
                const input = label.nextElementSibling?.querySelector('input, select') || 
                             document.querySelector(`[name="${label.getAttribute('for')}"]`);
                if (input) {
                    elemento = input;
                    break;
                }
            }
        }
        
        // Si no se encontró, buscar directamente por name
        if (!elemento) {
            elemento = document.querySelector(`[name*="${nombreCampo.toLowerCase()}"]`);
        }
        
        if (elemento) {
            elemento.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            setTimeout(() => {
                elemento.focus();
                elemento.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.5)';
                setTimeout(() => {
                    elemento.style.boxShadow = '';
                }, 2000);
            }, 500);
        }
    }, 350);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que Alpine.js esté inicializado
    setTimeout(initDomicilioValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('domicilioValidation', () => ({
        init() {
            initDomicilioValidation();
        }
    }));
} 