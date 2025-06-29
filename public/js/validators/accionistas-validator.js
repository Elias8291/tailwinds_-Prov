/**
 * Validador para la sección de Accionistas
 * Maneja la validación de datos de accionistas para personas morales
 */

// Variables globales
let accionistasValidados = false;

// Patrones de validación para accionistas
const accionistasValidationPatterns = {
    nombre: /^[a-zA-ZÀ-ÿñÑ\s]{2,50}$/,
    apellido_paterno: /^[a-zA-ZÀ-ÿñÑ\s]{2,50}$/,
    apellido_materno: /^[a-zA-ZÀ-ÿñÑ\s]{0,50}$/,
    porcentaje: /^(?:100(?:\.0{1,2})?|[1-9]?[0-9](?:\.[0-9]{1,2})?)$/
};

// Mensajes de error específicos
const accionistasErrorMessages = {
    nombre: 'El nombre debe tener entre 2 y 50 caracteres (solo letras y espacios)',
    apellido_paterno: 'El apellido paterno debe tener entre 2 y 50 caracteres (solo letras y espacios)',
    apellido_materno: 'El apellido materno debe tener máximo 50 caracteres (solo letras y espacios)',
    porcentaje: 'El porcentaje debe ser un número válido entre 0.01 y 100',
    porcentaje_total: 'La suma total de porcentajes debe ser exactamente 100%',
    min_accionistas: 'Debe haber al menos un accionista'
};

// Estado de validación de campos
let accionistasFieldStates = {};

// Función principal de inicialización
function inicializarValidacionAccionistas() {
    const formulario = document.getElementById('form-accionistas');
    
    if (!formulario) {
        return;
    }

    // Inicializar validaciones y eventos
    configurarEventosAccionistas(formulario);
}

// Configurar eventos de validación en tiempo real
function setupAccionistasValidationEvents() {
    // Usar un observer para detectar cambios en el contenedor de accionistas
    const contenedor = document.getElementById('accionistas-container');
    if (contenedor) {
        // Configurar MutationObserver para detectar cambios en accionistas
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    setupAccionistasFieldEvents();
                }
            });
        });
        
        observer.observe(contenedor, {
            childList: true,
            subtree: true
        });
        
        // Configurar eventos iniciales
        setupAccionistasFieldEvents();
    }
}

// Configurar eventos para campos de accionistas
function setupAccionistasFieldEvents() {
    // Obtener todos los inputs de accionistas
    const inputs = document.querySelectorAll('#accionistas-container input');
    
    inputs.forEach(input => {
        // Remover listeners previos
        input.removeEventListener('keyup', handleAccionistaValidation);
        input.removeEventListener('blur', handleAccionistaValidation);
        input.removeEventListener('input', handleAccionistaInput);
        
        // Agregar nuevos listeners
        input.addEventListener('keyup', handleAccionistaValidation);
        input.addEventListener('blur', handleAccionistaValidation);
        input.addEventListener('input', handleAccionistaInput);
    });
}

// Manejar validación de campo de accionista
function handleAccionistaValidation(e) {
    const input = e.target;
    const fieldType = getAccionistaFieldType(input);
    validarCampoAccionista(input, fieldType);
}

// Manejar input de accionista
function handleAccionistaInput(e) {
    const input = e.target;
    limpiarErrorAccionista(input);
}

// Obtener tipo de campo de accionista
function getAccionistaFieldType(input) {
    const name = input.name;
    if (name.includes('[nombre]')) return 'nombre';
    if (name.includes('[apellido_paterno]')) return 'apellido_paterno';
    if (name.includes('[apellido_materno]')) return 'apellido_materno';
    if (name.includes('[porcentaje]')) return 'porcentaje';
    return 'unknown';
}

// Configurar intercepción del submit
function setupAccionistasSubmitInterception() {
    const form = document.querySelector('form[x-ref="accionistasForm"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioAccionistasCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico de accionista
function validarCampoAccionista(input, fieldType) {
    if (!input || fieldType === 'unknown') return false;

    const value = input.value.trim();
    const pattern = accionistasValidationPatterns[fieldType];
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoAccionista(input, 'Este campo es obligatorio');
        return false;
    }
    
    // Si el campo es opcional y está vacío, es válido
    if (!isRequired && value === '') {
        mostrarEstadoValidoAccionista(input);
        return true;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoAccionista(input, accionistasErrorMessages[fieldType]);
        return false;
    }
    
    // Validación especial para porcentajes
    if (fieldType === 'porcentaje') {
        const porcentaje = parseFloat(value);
        if (porcentaje <= 0 || porcentaje > 100) {
            mostrarEstadoInvalidoAccionista(input, 'El porcentaje debe estar entre 0.01 y 100');
            return false;
        }
    }
    
    // Campo válido
    mostrarEstadoValidoAccionista(input);
    return true;
}

// Validar formulario completo de accionistas
function validarFormularioAccionistasCompleto() {
    let isValid = true;
    let camposConError = [];

    // Verificar que hay al menos un accionista
    const accionistas = document.querySelectorAll('#accionistas-container > div');
    if (accionistas.length === 0) {
        camposConError.push({
            nombre: 'Accionistas',
            errores: [accionistasErrorMessages.min_accionistas]
        });
        isValid = false;
    }

    // Validar cada accionista
    let totalPorcentaje = 0;
    accionistas.forEach((accionista, index) => {
        const inputs = accionista.querySelectorAll('input');
        
        inputs.forEach(input => {
            const fieldType = getAccionistaFieldType(input);
            const fieldValid = validarCampoAccionista(input, fieldType);
            
            if (!fieldValid) {
                isValid = false;
                camposConError.push({
                    nombre: `Accionista ${index + 1}`,
                    errores: [accionistasErrorMessages[fieldType] || 'Error de validación']
                });
            }
            
            // Sumar porcentajes
            if (fieldType === 'porcentaje' && input.value) {
                totalPorcentaje += parseFloat(input.value) || 0;
            }
        });
    });

    // Verificar que la suma de porcentajes sea 100%
    if (Math.abs(totalPorcentaje - 100) > 0.01) {
        isValid = false;
        camposConError.push({
            nombre: 'Porcentajes',
            errores: [`La suma total es ${totalPorcentaje.toFixed(2)}%. ${accionistasErrorMessages.porcentaje_total}`]
        });
    }

    if (!isValid) {
        mostrarModalErroresAccionistas(camposConError);
        return false;
    }

    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensajes de error
    limpiarErrorAccionista(input);
}

// Mostrar estado inválido
function mostrarEstadoInvalidoAccionista(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    // Mostrar mensaje de error
    let errorDiv = input.parentNode.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-red-600 text-sm mt-1';
        input.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = mensaje;
}

// Limpiar estado de error
function limpiarErrorAccionista(input) {
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
    
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
}

// Mostrar modal de errores
function mostrarModalErroresAccionistas(camposConError) {
    // Implementación del modal de errores
}

// Integración con Alpine.js
document.addEventListener('DOMContentLoaded', function() {
    inicializarValidacionAccionistas();
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('accionistasValidation', () => ({
        init() {
            inicializarValidacionAccionistas();
        }
    }));
} 

// Validador para accionistas 