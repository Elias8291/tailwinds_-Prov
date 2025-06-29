/**
 * Validador para el formulario de Apoderado Legal
 * Valida campos específicos con patrones regex y feedback visual
 */

// Patrones de validación para apoderado
const apoderadoValidationPatterns = {
    nombre_apoderado: /^[a-zA-ZÀ-ÿñÑ\s\.]{3,100}$/,
    numero_escritura: /^[a-zA-Z0-9\s\/\-\.]{1,15}$/,
    nombre_notario: /^[a-zA-ZÀ-ÿñÑ\s\.]{3,100}$/,
    numero_notario: /^[0-9]{1,5}$/,
    numero_registro: /^[a-zA-Z0-9\s\/\-\.]{1,20}$/
};

// Mensajes de error específicos
const apoderadoErrorMessages = {
    nombre_apoderado: 'El nombre del apoderado debe tener entre 3 y 100 caracteres (solo letras, espacios y puntos)',
    numero_escritura: 'El número de escritura debe tener entre 1 y 15 caracteres (letras, números, espacios, /, -, .)',
    nombre_notario: 'El nombre del notario debe tener entre 3 y 100 caracteres (solo letras, espacios y puntos)',
    entidad_federativa: 'Debe seleccionar una entidad federativa',
    numero_notario: 'El número del notario debe ser numérico y tener máximo 5 dígitos',
    fecha_escritura: 'Debe seleccionar una fecha válida',
    numero_registro: 'El número de registro debe tener entre 1 y 20 caracteres',
    fecha_inscripcion: 'Debe seleccionar una fecha válida'
};

// Estado de validación de campos
let apoderadoFieldStates = {};

// Función principal de inicialización
function initApoderadoValidation() {
    const form = document.querySelector('form[x-ref="apoderadoForm"]');
    if (!form) {
        return;
    }

    setupApoderadoValidationEvents();
    setupApoderadoSubmitInterception();
}

// Configurar eventos de validación en tiempo real
function setupApoderadoValidationEvents() {
    Object.keys(apoderadoValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.addEventListener('keyup', () => {
                validarCampoApoderado(fieldName);
                limpiarErrorApoderado(input);
            });
            
            input.addEventListener('blur', () => {
                validarCampoApoderado(fieldName);
            });
            
            input.addEventListener('input', () => {
                limpiarErrorApoderado(input);
            });
        }
    });

    // Validación especial para fechas
    const fechaEscritura = document.querySelector('[name="fecha_escritura"]');
    if (fechaEscritura) {
        fechaEscritura.addEventListener('change', () => {
            validarFechaEscritura();
            limpiarErrorApoderado(fechaEscritura);
        });
    }

    const fechaInscripcion = document.querySelector('[name="fecha_inscripcion"]');
    if (fechaInscripcion) {
        fechaInscripcion.addEventListener('change', () => {
            validarFechaInscripcionApoderado();
            limpiarErrorApoderado(fechaInscripcion);
        });
    }

    // Validación para entidad federativa
    const entidadSelect = document.querySelector('[name="entidad_federativa"]');
    if (entidadSelect) {
        entidadSelect.addEventListener('change', () => {
            validarEntidadFederativaApoderado();
            limpiarErrorApoderado(entidadSelect);
        });
    }
}

// Configurar intercepción del submit
function setupApoderadoSubmitInterception() {
    const form = document.querySelector('form[x-ref="apoderadoForm"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioApoderadoCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico
function validarCampoApoderado(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (!input) return false;

    const value = input.value.trim();
    const pattern = apoderadoValidationPatterns[fieldName];
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && value === '') {
        mostrarEstadoInvalidoApoderado(input, 'Este campo es obligatorio');
        apoderadoFieldStates[fieldName] = false;
        return false;
    }
    
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoApoderado(input, apoderadoErrorMessages[fieldName]);
        apoderadoFieldStates[fieldName] = false;
        return false;
    }
    
    mostrarEstadoValidoApoderado(input);
    apoderadoFieldStates[fieldName] = true;
    return true;
}

// Validar fecha de escritura
function validarFechaEscritura() {
    const input = document.querySelector('[name="fecha_escritura"]');
    if (!input) return false;

    const value = input.value;
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && (!value || value === '')) {
        mostrarEstadoInvalidoApoderado(input, 'La fecha de escritura es obligatoria');
        apoderadoFieldStates.fecha_escritura = false;
        return false;
    }
    
    // Validar que no sea una fecha futura
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoApoderado(input, 'La fecha de escritura no puede ser futura');
            apoderadoFieldStates.fecha_escritura = false;
            return false;
        }
    }
    
    mostrarEstadoValidoApoderado(input);
    apoderadoFieldStates.fecha_escritura = true;
    return true;
}

// Validar fecha de inscripción del apoderado
function validarFechaInscripcionApoderado() {
    const input = document.querySelector('[name="fecha_inscripcion"]');
    if (!input) return false;

    const value = input.value;
    const isRequired = input.hasAttribute('required');
    
    if (isRequired && (!value || value === '')) {
        mostrarEstadoInvalidoApoderado(input, 'La fecha de inscripción es obligatoria');
        apoderadoFieldStates.fecha_inscripcion = false;
        return false;
    }
    
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoApoderado(input, 'La fecha de inscripción no puede ser futura');
            apoderadoFieldStates.fecha_inscripcion = false;
            return false;
        }
    }
    
    mostrarEstadoValidoApoderado(input);
    apoderadoFieldStates.fecha_inscripcion = true;
    return true;
}

// Validar entidad federativa del apoderado
function validarEntidadFederativaApoderado() {
    const select = document.querySelector('[name="entidad_federativa"]');
    if (!select) return false;

    const value = select.value;
    const isRequired = select.hasAttribute('required');
    
    if (isRequired && (value === '' || value === null)) {
        mostrarEstadoInvalidoApoderado(select, apoderadoErrorMessages.entidad_federativa);
        apoderadoFieldStates.entidad_federativa = false;
        return false;
    }
    
    mostrarEstadoValidoApoderado(select);
    apoderadoFieldStates.entidad_federativa = true;
    return true;
}

// Validar formulario completo
function validarFormularioApoderadoCompleto() {
    let isValid = true;
    let camposConError = [];

    Object.keys(apoderadoValidationPatterns).forEach(fieldName => {
        const fieldValid = validarCampoApoderado(fieldName);
        if (!fieldValid) {
            isValid = false;
            const input = document.querySelector(`[name="${fieldName}"]`);
            if (input) {
                const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || fieldName;
                const errorMessage = apoderadoErrorMessages[fieldName] || 'Campo inválido';
                camposConError.push({
                    nombre: label,
                    errores: [errorMessage]
                });
            }
        }
    });

    // Validar fechas
    const fechaEscrituraValid = validarFechaEscritura();
    if (!fechaEscrituraValid) {
        isValid = false;
            camposConError.push({
            nombre: 'Fecha de Escritura',
                errores: [apoderadoErrorMessages.fecha_escritura]
            });
    }

    const fechaInscripcionValid = validarFechaInscripcionApoderado();
    if (!fechaInscripcionValid) {
        isValid = false;
            camposConError.push({
            nombre: 'Fecha de Inscripción',
                errores: [apoderadoErrorMessages.fecha_inscripcion]
            });
    }

    // Validar entidad federativa
    const entidadValid = validarEntidadFederativaApoderado();
    if (!entidadValid) {
        isValid = false;
            camposConError.push({
            nombre: 'Entidad Federativa',
                errores: [apoderadoErrorMessages.entidad_federativa]
            });
    }

    if (!isValid && camposConError.length > 0) {
        mostrarModalErroresApoderado(camposConError);
        return false;
    }

    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoApoderado(input) {
    input.classList.remove('border-red-500');
    input.classList.add('border-green-300');
    
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoApoderado(input, mensaje) {
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
function limpiarErrorApoderado(input) {
    input.classList.remove('border-red-500');
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// Mostrar modal de errores
function mostrarModalErroresApoderado(camposConError) {
    let mensaje = 'Por favor corrija los siguientes errores:\n\n';
    camposConError.forEach(campo => {
        mensaje += `• ${campo.nombre}: ${campo.errores.join(', ')}\n`;
    });
    alert(mensaje);
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApoderadoValidation);
} else {
            initApoderadoValidation();
        }

// Alpine.js integration
if (typeof Alpine !== 'undefined') {
    Alpine.data('apoderadoValidator', () => ({
        init() {
            initApoderadoValidation();
        }
    }));
} 