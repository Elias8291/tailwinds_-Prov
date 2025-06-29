/**
 * Validador para el formulario de Constitución
 * Valida campos específicos con patrones regex y feedback visual
 */

// Patrones de validación para constitución
const constitucionValidationPatterns = {
    numero_escritura: /^[a-zA-Z0-9\s\/\-\.]{1,15}$/,
    nombre_notario: /^[a-zA-ZÀ-ÿñÑ\s\.]{3,100}$/,
    numero_notario: /^[0-9]{1,5}$/,
    numero_registro: /^[a-zA-Z0-9\s\/\-\.]{1,20}$/
};

// Mensajes de error específicos
const constitucionErrorMessages = {
    numero_escritura: 'El número de escritura debe tener entre 1 y 15 caracteres (letras, números, espacios, /, -, .)',
    fecha_constitucion: 'Debe seleccionar una fecha válida',
    nombre_notario: 'El nombre del notario debe tener entre 3 y 100 caracteres (solo letras, espacios y puntos)',
    entidad_federativa: 'Debe seleccionar una entidad federativa',
    numero_notario: 'El número del notario debe ser numérico y tener máximo 5 dígitos',
    numero_registro: 'El número de registro debe tener entre 1 y 20 caracteres',
    fecha_inscripcion: 'Debe seleccionar una fecha válida'
};

// Estado de validación de campos
let constitucionFieldStates = {};

// Función principal de inicialización
function initConstitucionValidation() {
    const form = document.querySelector('form[x-ref="constitucionForm"]');
    if (!form) {
        return;
    }

    setupConstitucionValidationEvents();
    setupConstitucionSubmitInterception();
}

// Configurar eventos de validación en tiempo real
function setupConstitucionValidationEvents() {
    Object.keys(constitucionValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.addEventListener('keyup', () => {
                validarCampoConstitucion(fieldName);
                limpiarErrorConstitucion(input);
            });
            
            input.addEventListener('blur', () => {
                validarCampoConstitucion(fieldName);
            });
            
            input.addEventListener('input', () => {
                limpiarErrorConstitucion(input);
            });
        }
    });

    // Validación especial para fechas
    const fechaConstitucion = document.querySelector('[name="fecha_constitucion"]');
    if (fechaConstitucion) {
        fechaConstitucion.addEventListener('change', () => {
            validarFechaConstitucion();
            limpiarErrorConstitucion(fechaConstitucion);
        });
    }

    const fechaInscripcion = document.querySelector('[name="fecha_inscripcion"]');
    if (fechaInscripcion) {
        fechaInscripcion.addEventListener('change', () => {
            validarFechaInscripcion();
            limpiarErrorConstitucion(fechaInscripcion);
        });
    }

    // Validación para entidad federativa
    const entidadSelect = document.querySelector('[name="entidad_federativa"]');
    if (entidadSelect) {
        entidadSelect.addEventListener('change', () => {
            validarEntidadFederativa();
            limpiarErrorConstitucion(entidadSelect);
        });
    }
}

// Configurar intercepción del submit
function setupConstitucionSubmitInterception() {
    const form = document.querySelector('form[x-ref="constitucionForm"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioConstitucionCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico
function validarCampoConstitucion(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (!input) return false;

    const value = input.value.trim();
    const pattern = constitucionValidationPatterns[fieldName];
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && value === '') {
        mostrarEstadoInvalidoConstitucion(input, 'Este campo es obligatorio');
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoConstitucion(input, constitucionErrorMessages[fieldName]);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    mostrarEstadoValidoConstitucion(input);
    constitucionFieldStates[fieldName] = true;
    return true;
}

// Validar fecha de constitución
function validarFechaConstitucion() {
    const input = document.querySelector('[name="fecha_constitucion"]');
    if (!input) return false;

    const value = input.value;
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && (!value || value === '')) {
        mostrarEstadoInvalidoConstitucion(input, 'La fecha de constitución es obligatoria');
        constitucionFieldStates.fecha_constitucion = false;
        return false;
    }
    
    // Validar que no sea una fecha futura
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoConstitucion(input, 'La fecha de constitución no puede ser futura');
            constitucionFieldStates.fecha_constitucion = false;
            return false;
        }
    }
    
    mostrarEstadoValidoConstitucion(input);
    constitucionFieldStates.fecha_constitucion = true;
    return true;
}

// Validar fecha de inscripción
function validarFechaInscripcion() {
    const input = document.querySelector('[name="fecha_inscripcion"]');
    if (!input) return false;

    const value = input.value;
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && (!value || value === '')) {
        mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción es obligatoria');
        constitucionFieldStates.fecha_inscripcion = false;
        return false;
    }
    
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción no puede ser futura');
            constitucionFieldStates.fecha_inscripcion = false;
            return false;
        }
    }
    
    mostrarEstadoValidoConstitucion(input);
    constitucionFieldStates.fecha_inscripcion = true;
    return true;
}

// Validar entidad federativa
function validarEntidadFederativa() {
    const select = document.querySelector('[name="entidad_federativa"]');
    if (!select) return false;

    const value = select.value;
    const isRequired = select.hasAttribute('required');
    
    if (isRequired && (value === '' || value === null)) {
        mostrarEstadoInvalidoConstitucion(select, constitucionErrorMessages.entidad_federativa);
        constitucionFieldStates.entidad_federativa = false;
        return false;
    }
    
    mostrarEstadoValidoConstitucion(select);
    constitucionFieldStates.entidad_federativa = true;
    return true;
}

// Validar formulario completo
function validarFormularioConstitucionCompleto() {
    let isValid = true;
    let camposConError = [];

    Object.keys(constitucionValidationPatterns).forEach(fieldName => {
        const fieldValid = validarCampoConstitucion(fieldName);
        if (!fieldValid) {
            isValid = false;
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || fieldName;
                const errorMessage = constitucionErrorMessages[fieldName] || 'Campo inválido';
                camposConError.push({
                    nombre: label,
                    errores: [errorMessage]
                });
            }
        }
    });

    // Validar fechas
    const fechaConstitucionValid = validarFechaConstitucion();
    if (!fechaConstitucionValid) {
        isValid = false;
            camposConError.push({
            nombre: 'Fecha de Constitución',
                errores: [constitucionErrorMessages.fecha_constitucion]
            });
    }

    const fechaInscripcionValid = validarFechaInscripcion();
    if (!fechaInscripcionValid) {
        isValid = false;
            camposConError.push({
            nombre: 'Fecha de Inscripción',
                errores: [constitucionErrorMessages.fecha_inscripcion]
            });
    }

    // Validar entidad federativa
    const entidadValid = validarEntidadFederativa();
    if (!entidadValid) {
        isValid = false;
            camposConError.push({
            nombre: 'Entidad Federativa',
                errores: [constitucionErrorMessages.entidad_federativa]
            });
    }

    if (!isValid && camposConError.length > 0) {
        mostrarModalErroresConstitucion(camposConError);
        return false;
    }

    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoConstitucion(input) {
    input.classList.remove('border-red-500');
    input.classList.add('border-green-300');
    
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoConstitucion(input, mensaje) {
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
function limpiarErrorConstitucion(input) {
    input.classList.remove('border-red-500');
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Mostrar modal de errores
function mostrarModalErroresConstitucion(camposConError) {
    let mensaje = 'Por favor corrija los siguientes errores:\n\n';
    camposConError.forEach(campo => {
        mensaje += `• ${campo.nombre}: ${campo.errores.join(', ')}\n`;
    });
    alert(mensaje);
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initConstitucionValidation);
} else {
            initConstitucionValidation();
        }

// Alpine.js integration
if (typeof Alpine !== 'undefined') {
    Alpine.data('constitucionValidator', () => ({
        init() {
            initConstitucionValidation();
        }
    }));
} 