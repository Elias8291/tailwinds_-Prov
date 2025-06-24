/**
 * Validador para el formulario de Accionistas
 * Valida campos específicos con patrones regex y feedback visual
 */

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
function initAccionistasValidation() {
    console.log('Inicializando validación de accionistas...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="accionistasForm"]');
    if (!form) {
        console.log('Formulario de accionistas no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupAccionistasValidationEvents();
    
    // Configurar intercepción del submit
    setupAccionistasSubmitInterception();
    
    console.log('Validación de accionistas inicializada correctamente');
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
        mostrarEstadoInvalidoAccionista(input, `Este campo es obligatorio`);
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
    console.log('Validando formulario de accionistas completo...');
    
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
                const label = fieldType.charAt(0).toUpperCase() + fieldType.slice(1).replace('_', ' ');
                const errorMessage = accionistasErrorMessages[fieldType] || 'Campo inválido';
                camposConError.push({
                    nombre: `Accionista ${index + 1} - ${label}`,
                    errores: [errorMessage]
                });
            }
            
            // Sumar porcentajes
            if (fieldType === 'porcentaje' && input.value) {
                totalPorcentaje += parseFloat(input.value) || 0;
            }
        });
    });

    // Validar que la suma de porcentajes sea 100%
    if (accionistas.length > 0 && Math.abs(totalPorcentaje - 100) > 0.01) {
        isValid = false;
        camposConError.push({
            nombre: 'Total de Porcentajes',
            errores: [`La suma actual es ${totalPorcentaje.toFixed(2)}%. ${accionistasErrorMessages.porcentaje_total}`]
        });
    }

    if (!isValid) {
        mostrarModalErroresAccionistas(camposConError);
        return false;
    }

    console.log('Formulario de accionistas válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensaje de error si existe
    const container = input.closest('.bg-gray-50');
    if (container) {
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoAccionista(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    const container = input.closest('.bg-gray-50');
    if (!container) return;
    
    // Remover mensaje anterior
    let mensajeError = container.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-1 text-sm text-red-600';
    mensajeError.textContent = mensaje;
    
    // Insertar después del input
    input.parentNode.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const container = input.closest('.bg-gray-50');
    if (container) {
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Modal de errores específico para accionistas
function mostrarModalErroresAccionistas(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-accionistas');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-accionistas';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoAccionista('${campo.nombre}', ${index})">
                    <i class="fas fa-users mr-2 text-red-600"></i>
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
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Accionistas
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos de accionistas
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresAccionistas()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos de accionistas:
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
                            ${camposConError.length} error${camposConError.length !== 1 ? 'es' : ''} detectado${camposConError.length !== 1 ? 's' : ''}
                        </div>
                        <button onclick="cerrarModalErroresAccionistas()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresAccionistas();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresAccionistas();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresAccionistas() {
    const modal = document.getElementById('modal-errores-accionistas');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoAccionista(nombreCampo, index) {
    cerrarModalErroresAccionistas();
    
    setTimeout(() => {
        let elemento = null;
        
        // Si es un error de porcentaje total, ir al primer porcentaje
        if (nombreCampo.includes('Total de Porcentajes')) {
            elemento = document.querySelector('input[name*="[porcentaje]"]');
        } else {
            // Buscar el campo específico del accionista
            const contenedor = document.getElementById('accionistas-container');
            if (contenedor) {
                const accionistas = contenedor.querySelectorAll('.bg-gray-50');
                
                // Extraer número de accionista del nombre del campo
                const match = nombreCampo.match(/Accionista (\d+)/);
                if (match) {
                    const accionistaIndex = parseInt(match[1]) - 1;
                    const accionista = accionistas[accionistaIndex];
                    
                    if (accionista) {
                        if (nombreCampo.includes('Nombre')) {
                            elemento = accionista.querySelector('input[name*="[nombre]"]');
                        } else if (nombreCampo.includes('Apellido paterno')) {
                            elemento = accionista.querySelector('input[name*="[apellido_paterno]"]');
                        } else if (nombreCampo.includes('Apellido materno')) {
                            elemento = accionista.querySelector('input[name*="[apellido_materno]"]');
                        } else if (nombreCampo.includes('Porcentaje')) {
                            elemento = accionista.querySelector('input[name*="[porcentaje]"]');
                        }
                    }
                }
            }
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
    setTimeout(initAccionistasValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('accionistasValidation', () => ({
        init() {
            initAccionistasValidation();
        }
    }));
} 
 * Validador para el formulario de Accionistas
 * Valida campos específicos con patrones regex y feedback visual
 */

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
function initAccionistasValidation() {
    console.log('Inicializando validación de accionistas...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="accionistasForm"]');
    if (!form) {
        console.log('Formulario de accionistas no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupAccionistasValidationEvents();
    
    // Configurar intercepción del submit
    setupAccionistasSubmitInterception();
    
    console.log('Validación de accionistas inicializada correctamente');
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
        mostrarEstadoInvalidoAccionista(input, `Este campo es obligatorio`);
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
    console.log('Validando formulario de accionistas completo...');
    
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
                const label = fieldType.charAt(0).toUpperCase() + fieldType.slice(1).replace('_', ' ');
                const errorMessage = accionistasErrorMessages[fieldType] || 'Campo inválido';
                camposConError.push({
                    nombre: `Accionista ${index + 1} - ${label}`,
                    errores: [errorMessage]
                });
            }
            
            // Sumar porcentajes
            if (fieldType === 'porcentaje' && input.value) {
                totalPorcentaje += parseFloat(input.value) || 0;
            }
        });
    });

    // Validar que la suma de porcentajes sea 100%
    if (accionistas.length > 0 && Math.abs(totalPorcentaje - 100) > 0.01) {
        isValid = false;
        camposConError.push({
            nombre: 'Total de Porcentajes',
            errores: [`La suma actual es ${totalPorcentaje.toFixed(2)}%. ${accionistasErrorMessages.porcentaje_total}`]
        });
    }

    if (!isValid) {
        mostrarModalErroresAccionistas(camposConError);
        return false;
    }

    console.log('Formulario de accionistas válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensaje de error si existe
    const container = input.closest('.bg-gray-50');
    if (container) {
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoAccionista(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    const container = input.closest('.bg-gray-50');
    if (!container) return;
    
    // Remover mensaje anterior
    let mensajeError = container.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-1 text-sm text-red-600';
    mensajeError.textContent = mensaje;
    
    // Insertar después del input
    input.parentNode.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const container = input.closest('.bg-gray-50');
    if (container) {
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Modal de errores específico para accionistas
function mostrarModalErroresAccionistas(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-accionistas');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-accionistas';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoAccionista('${campo.nombre}', ${index})">
                    <i class="fas fa-users mr-2 text-red-600"></i>
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
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Accionistas
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos de accionistas
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresAccionistas()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos de accionistas:
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
                            ${camposConError.length} error${camposConError.length !== 1 ? 'es' : ''} detectado${camposConError.length !== 1 ? 's' : ''}
                        </div>
                        <button onclick="cerrarModalErroresAccionistas()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresAccionistas();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresAccionistas();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresAccionistas() {
    const modal = document.getElementById('modal-errores-accionistas');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoAccionista(nombreCampo, index) {
    cerrarModalErroresAccionistas();
    
    setTimeout(() => {
        let elemento = null;
        
        // Si es un error de porcentaje total, ir al primer porcentaje
        if (nombreCampo.includes('Total de Porcentajes')) {
            elemento = document.querySelector('input[name*="[porcentaje]"]');
        } else {
            // Buscar el campo específico del accionista
            const contenedor = document.getElementById('accionistas-container');
            if (contenedor) {
                const accionistas = contenedor.querySelectorAll('.bg-gray-50');
                
                // Extraer número de accionista del nombre del campo
                const match = nombreCampo.match(/Accionista (\d+)/);
                if (match) {
                    const accionistaIndex = parseInt(match[1]) - 1;
                    const accionista = accionistas[accionistaIndex];
                    
                    if (accionista) {
                        if (nombreCampo.includes('Nombre')) {
                            elemento = accionista.querySelector('input[name*="[nombre]"]');
                        } else if (nombreCampo.includes('Apellido paterno')) {
                            elemento = accionista.querySelector('input[name*="[apellido_paterno]"]');
                        } else if (nombreCampo.includes('Apellido materno')) {
                            elemento = accionista.querySelector('input[name*="[apellido_materno]"]');
                        } else if (nombreCampo.includes('Porcentaje')) {
                            elemento = accionista.querySelector('input[name*="[porcentaje]"]');
                        }
                    }
                }
            }
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
    setTimeout(initAccionistasValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('accionistasValidation', () => ({
        init() {
            initAccionistasValidation();
        }
    }));
} 
 * Validador para el formulario de Accionistas
 * Valida campos específicos con patrones regex y feedback visual
 */

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
function initAccionistasValidation() {
    console.log('Inicializando validación de accionistas...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="accionistasForm"]');
    if (!form) {
        console.log('Formulario de accionistas no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupAccionistasValidationEvents();
    
    // Configurar intercepción del submit
    setupAccionistasSubmitInterception();
    
    console.log('Validación de accionistas inicializada correctamente');
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
        mostrarEstadoInvalidoAccionista(input, `Este campo es obligatorio`);
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
    console.log('Validando formulario de accionistas completo...');
    
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
                const label = fieldType.charAt(0).toUpperCase() + fieldType.slice(1).replace('_', ' ');
                const errorMessage = accionistasErrorMessages[fieldType] || 'Campo inválido';
                camposConError.push({
                    nombre: `Accionista ${index + 1} - ${label}`,
                    errores: [errorMessage]
                });
            }
            
            // Sumar porcentajes
            if (fieldType === 'porcentaje' && input.value) {
                totalPorcentaje += parseFloat(input.value) || 0;
            }
        });
    });

    // Validar que la suma de porcentajes sea 100%
    if (accionistas.length > 0 && Math.abs(totalPorcentaje - 100) > 0.01) {
        isValid = false;
        camposConError.push({
            nombre: 'Total de Porcentajes',
            errores: [`La suma actual es ${totalPorcentaje.toFixed(2)}%. ${accionistasErrorMessages.porcentaje_total}`]
        });
    }

    if (!isValid) {
        mostrarModalErroresAccionistas(camposConError);
        return false;
    }

    console.log('Formulario de accionistas válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50');
    input.classList.add('border-green-500', 'bg-green-50');
    
    // Remover mensaje de error si existe
    const container = input.closest('.bg-gray-50');
    if (container) {
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoAccionista(input, mensaje) {
    input.classList.remove('border-green-500', 'bg-green-50');
    input.classList.add('border-red-500', 'bg-red-50');
    
    const container = input.closest('.bg-gray-50');
    if (!container) return;
    
    // Remover mensaje anterior
    let mensajeError = container.querySelector('.error-message');
    if (mensajeError) {
        mensajeError.remove();
    }
    
    // Agregar nuevo mensaje
    mensajeError = document.createElement('p');
    mensajeError.className = 'error-message mt-1 text-sm text-red-600';
    mensajeError.textContent = mensaje;
    
    // Insertar después del input
    input.parentNode.appendChild(mensajeError);
}

// Limpiar error de un campo
function limpiarErrorAccionista(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const container = input.closest('.bg-gray-50');
    if (container) {
        const errorMessage = container.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Modal de errores específico para accionistas
function mostrarModalErroresAccionistas(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-accionistas');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-accionistas';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoAccionista('${campo.nombre}', ${index})">
                    <i class="fas fa-users mr-2 text-red-600"></i>
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
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Accionistas
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos de accionistas
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresAccionistas()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos de accionistas:
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
                            ${camposConError.length} error${camposConError.length !== 1 ? 'es' : ''} detectado${camposConError.length !== 1 ? 's' : ''}
                        </div>
                        <button onclick="cerrarModalErroresAccionistas()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresAccionistas();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresAccionistas();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresAccionistas() {
    const modal = document.getElementById('modal-errores-accionistas');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoAccionista(nombreCampo, index) {
    cerrarModalErroresAccionistas();
    
    setTimeout(() => {
        let elemento = null;
        
        // Si es un error de porcentaje total, ir al primer porcentaje
        if (nombreCampo.includes('Total de Porcentajes')) {
            elemento = document.querySelector('input[name*="[porcentaje]"]');
        } else {
            // Buscar el campo específico del accionista
            const contenedor = document.getElementById('accionistas-container');
            if (contenedor) {
                const accionistas = contenedor.querySelectorAll('.bg-gray-50');
                
                // Extraer número de accionista del nombre del campo
                const match = nombreCampo.match(/Accionista (\d+)/);
                if (match) {
                    const accionistaIndex = parseInt(match[1]) - 1;
                    const accionista = accionistas[accionistaIndex];
                    
                    if (accionista) {
                        if (nombreCampo.includes('Nombre')) {
                            elemento = accionista.querySelector('input[name*="[nombre]"]');
                        } else if (nombreCampo.includes('Apellido paterno')) {
                            elemento = accionista.querySelector('input[name*="[apellido_paterno]"]');
                        } else if (nombreCampo.includes('Apellido materno')) {
                            elemento = accionista.querySelector('input[name*="[apellido_materno]"]');
                        } else if (nombreCampo.includes('Porcentaje')) {
                            elemento = accionista.querySelector('input[name*="[porcentaje]"]');
                        }
                    }
                }
            }
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
    setTimeout(initAccionistasValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('accionistasValidation', () => ({
        init() {
            initAccionistasValidation();
        }
    }));
} 