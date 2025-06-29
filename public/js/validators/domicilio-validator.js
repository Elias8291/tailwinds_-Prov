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
    // Verificar que el formulario existe
    const form = document.querySelector('form[data-validate="true"]');
    if (!form) {
        return;
    }

    // Configurar eventos de validación
    setupDomicilioValidationEvents();
    
    // Configurar intercepción del submit
    setupDomicilioSubmitInterception();
}

// Configurar eventos de validación en tiempo real
function setupDomicilioValidationEvents() {
    Object.keys(domicilioValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.addEventListener('keyup', () => {
                validarCampoDomicilio(fieldName);
                limpiarErrorDomicilio(input);
            });
            
            input.addEventListener('blur', () => {
                validarCampoDomicilio(fieldName);
            });
            
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
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && value === '') {
        mostrarEstadoInvalidoDomicilio(input, 'Este campo es obligatorio');
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoDomicilio(input, domicilioErrorMessages[fieldName]);
        domicilioFieldStates[fieldName] = false;
        return false;
    }
    
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
    let isValid = true;
    let camposConError = [];

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

    if (!isValid && camposConError.length > 0) {
        mostrarModalErroresDomicilio(camposConError);
        return false;
    }

    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDomicilio(input) {
    input.classList.remove('border-red-500');
    input.classList.add('border-green-300');
    
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDomicilio(input, mensaje) {
    input.classList.remove('border-green-300');
    input.classList.add('border-red-500');
    
    const errorMsgPrevio = input.parentElement.querySelector('.error-message');
    if (errorMsgPrevio) {
        errorMsgPrevio.remove();
    }
    
    const errorMsg = document.createElement('p');
    errorMsg.className = 'mt-1 text-sm text-red-600 error-message';
    errorMsg.textContent = mensaje;
    input.parentElement.appendChild(errorMsg);
}

// Limpiar errores
function limpiarErrorDomicilio(input) {
    input.classList.remove('border-red-500');
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Mostrar modal de errores
function mostrarModalErroresDomicilio(camposConError) {
    let mensaje = 'Por favor corrija los siguientes errores:\n\n';
    camposConError.forEach(campo => {
        mensaje += `• ${campo.nombre}: ${campo.errores.join(', ')}\n`;
    });
    alert(mensaje);
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDomicilioValidation);
} else {
            initDomicilioValidation();
        }

// Alpine.js integration
if (typeof Alpine !== 'undefined') {
    Alpine.data('domicilioValidator', () => ({
        init() {
            initDomicilioValidation();
        }
    }));
} 