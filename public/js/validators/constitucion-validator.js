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
    console.log('Inicializando validación de constitución...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="constitucionForm"]');
    if (!form) {
        console.log('Formulario de constitución no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupConstitucionValidationEvents();
    
    // Configurar intercepción del submit
    setupConstitucionSubmitInterception();
    
    console.log('Validación de constitución inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupConstitucionValidationEvents() {
    // Validación para campos con patrones
    Object.keys(constitucionValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            // Evento al escribir (keyup)
            input.addEventListener('keyup', () => {
                validarCampoConstitucion(fieldName);
                limpiarErrorConstitucion(input);
            });
            
            // Evento al perder el foco (blur)
            input.addEventListener('blur', () => {
                validarCampoConstitucion(fieldName);
            });
            
            // Limpiar errores al empezar a escribir
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
        
        fechaConstitucion.addEventListener('focus', () => {
            limpiarErrorConstitucion(fechaConstitucion);
        });
    }

    const fechaInscripcion = document.querySelector('[name="fecha_inscripcion"]');
    if (fechaInscripcion) {
        fechaInscripcion.addEventListener('change', () => {
            validarFechaInscripcion();
            limpiarErrorConstitucion(fechaInscripcion);
        });
        
        fechaInscripcion.addEventListener('focus', () => {
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
        
        entidadSelect.addEventListener('focus', () => {
            limpiarErrorConstitucion(entidadSelect);
        });
    }
}

// Configurar intercepción del submit
function setupConstitucionSubmitInterception() {
    // Buscar la función original en el scope global de Alpine
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
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoConstitucion(input, `Este campo es obligatorio`);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoConstitucion(input, constitucionErrorMessages[fieldName]);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
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
        fechaHoy.setHours(23, 59, 59, 999); // Fin del día actual
        
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
    
    // Validar que no sea una fecha futura
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción no puede ser futura');
            constitucionFieldStates.fecha_inscripcion = false;
            return false;
        }
        
        // Validar que la fecha de inscripción sea posterior a la fecha de constitución
        const fechaConstitucion = document.querySelector('[name="fecha_constitucion"]')?.value;
        if (fechaConstitucion) {
            const fechaConstitucionDate = new Date(fechaConstitucion);
            if (fechaSeleccionada < fechaConstitucionDate) {
                mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción debe ser posterior a la fecha de constitución');
                constitucionFieldStates.fecha_inscripcion = false;
                return false;
            }
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
    console.log('Validando formulario de constitución completo...');
    
    let isValid = true;
    let camposConError = [];

    // Validar campos con patrones
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
        const input = document.querySelector('[name="fecha_constitucion"]');
        if (input) {
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Fecha de Constitución';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.fecha_constitucion]
            });
        }
    }

    const fechaInscripcionValid = validarFechaInscripcion();
    if (!fechaInscripcionValid) {
        isValid = false;
        const input = document.querySelector('[name="fecha_inscripcion"]');
        if (input) {
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Fecha de Inscripción';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.fecha_inscripcion]
            });
        }
    }

    // Validar entidad federativa
    const entidadValid = validarEntidadFederativa();
    if (!entidadValid) {
        isValid = false;
        const select = document.querySelector('[name="entidad_federativa"]');
        if (select) {
            const label = select.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Entidad Federativa';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.entidad_federativa]
            });
        }
    }

    if (!isValid) {
        mostrarModalErroresConstitucion(camposConError);
        return false;
    }

    console.log('Formulario de constitución válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoConstitucion(input) {
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
function mostrarEstadoInvalidoConstitucion(input, mensaje) {
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
function limpiarErrorConstitucion(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Modal de errores específico para constitución
function mostrarModalErroresConstitucion(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-constitucion');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-constitucion';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoConstitucion('${campo.nombre}', ${index})">
                    <i class="fas fa-file-contract mr-2 text-red-600"></i>
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
                                <i class="fas fa-file-contract text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Constitución
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos de constitución
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresConstitucion()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos de constitución:
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
                        <button onclick="cerrarModalErroresConstitucion()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresConstitucion();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresConstitucion();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresConstitucion() {
    const modal = document.getElementById('modal-errores-constitucion');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoConstitucion(nombreCampo, index) {
    cerrarModalErroresConstitucion();
    
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
    setTimeout(initConstitucionValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('constitucionValidation', () => ({
        init() {
            initConstitucionValidation();
        }
    }));
} 
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
    console.log('Inicializando validación de constitución...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="constitucionForm"]');
    if (!form) {
        console.log('Formulario de constitución no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupConstitucionValidationEvents();
    
    // Configurar intercepción del submit
    setupConstitucionSubmitInterception();
    
    console.log('Validación de constitución inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupConstitucionValidationEvents() {
    // Validación para campos con patrones
    Object.keys(constitucionValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            // Evento al escribir (keyup)
            input.addEventListener('keyup', () => {
                validarCampoConstitucion(fieldName);
                limpiarErrorConstitucion(input);
            });
            
            // Evento al perder el foco (blur)
            input.addEventListener('blur', () => {
                validarCampoConstitucion(fieldName);
            });
            
            // Limpiar errores al empezar a escribir
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
        
        fechaConstitucion.addEventListener('focus', () => {
            limpiarErrorConstitucion(fechaConstitucion);
        });
    }

    const fechaInscripcion = document.querySelector('[name="fecha_inscripcion"]');
    if (fechaInscripcion) {
        fechaInscripcion.addEventListener('change', () => {
            validarFechaInscripcion();
            limpiarErrorConstitucion(fechaInscripcion);
        });
        
        fechaInscripcion.addEventListener('focus', () => {
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
        
        entidadSelect.addEventListener('focus', () => {
            limpiarErrorConstitucion(entidadSelect);
        });
    }
}

// Configurar intercepción del submit
function setupConstitucionSubmitInterception() {
    // Buscar la función original en el scope global de Alpine
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
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoConstitucion(input, `Este campo es obligatorio`);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoConstitucion(input, constitucionErrorMessages[fieldName]);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
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
        fechaHoy.setHours(23, 59, 59, 999); // Fin del día actual
        
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
    
    // Validar que no sea una fecha futura
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción no puede ser futura');
            constitucionFieldStates.fecha_inscripcion = false;
            return false;
        }
        
        // Validar que la fecha de inscripción sea posterior a la fecha de constitución
        const fechaConstitucion = document.querySelector('[name="fecha_constitucion"]')?.value;
        if (fechaConstitucion) {
            const fechaConstitucionDate = new Date(fechaConstitucion);
            if (fechaSeleccionada < fechaConstitucionDate) {
                mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción debe ser posterior a la fecha de constitución');
                constitucionFieldStates.fecha_inscripcion = false;
                return false;
            }
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
    console.log('Validando formulario de constitución completo...');
    
    let isValid = true;
    let camposConError = [];

    // Validar campos con patrones
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
        const input = document.querySelector('[name="fecha_constitucion"]');
        if (input) {
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Fecha de Constitución';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.fecha_constitucion]
            });
        }
    }

    const fechaInscripcionValid = validarFechaInscripcion();
    if (!fechaInscripcionValid) {
        isValid = false;
        const input = document.querySelector('[name="fecha_inscripcion"]');
        if (input) {
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Fecha de Inscripción';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.fecha_inscripcion]
            });
        }
    }

    // Validar entidad federativa
    const entidadValid = validarEntidadFederativa();
    if (!entidadValid) {
        isValid = false;
        const select = document.querySelector('[name="entidad_federativa"]');
        if (select) {
            const label = select.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Entidad Federativa';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.entidad_federativa]
            });
        }
    }

    if (!isValid) {
        mostrarModalErroresConstitucion(camposConError);
        return false;
    }

    console.log('Formulario de constitución válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoConstitucion(input) {
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
function mostrarEstadoInvalidoConstitucion(input, mensaje) {
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
function limpiarErrorConstitucion(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Modal de errores específico para constitución
function mostrarModalErroresConstitucion(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-constitucion');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-constitucion';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoConstitucion('${campo.nombre}', ${index})">
                    <i class="fas fa-file-contract mr-2 text-red-600"></i>
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
                                <i class="fas fa-file-contract text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Constitución
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos de constitución
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresConstitucion()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos de constitución:
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
                        <button onclick="cerrarModalErroresConstitucion()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresConstitucion();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresConstitucion();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresConstitucion() {
    const modal = document.getElementById('modal-errores-constitucion');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoConstitucion(nombreCampo, index) {
    cerrarModalErroresConstitucion();
    
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
    setTimeout(initConstitucionValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('constitucionValidation', () => ({
        init() {
            initConstitucionValidation();
        }
    }));
} 
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
    console.log('Inicializando validación de constitución...');
    
    // Verificar que el formulario existe
    const form = document.querySelector('form[x-ref="constitucionForm"]');
    if (!form) {
        console.log('Formulario de constitución no encontrado');
        return;
    }

    // Configurar eventos de validación
    setupConstitucionValidationEvents();
    
    // Configurar intercepción del submit
    setupConstitucionSubmitInterception();
    
    console.log('Validación de constitución inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupConstitucionValidationEvents() {
    // Validación para campos con patrones
    Object.keys(constitucionValidationPatterns).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            // Evento al escribir (keyup)
            input.addEventListener('keyup', () => {
                validarCampoConstitucion(fieldName);
                limpiarErrorConstitucion(input);
            });
            
            // Evento al perder el foco (blur)
            input.addEventListener('blur', () => {
                validarCampoConstitucion(fieldName);
            });
            
            // Limpiar errores al empezar a escribir
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
        
        fechaConstitucion.addEventListener('focus', () => {
            limpiarErrorConstitucion(fechaConstitucion);
        });
    }

    const fechaInscripcion = document.querySelector('[name="fecha_inscripcion"]');
    if (fechaInscripcion) {
        fechaInscripcion.addEventListener('change', () => {
            validarFechaInscripcion();
            limpiarErrorConstitucion(fechaInscripcion);
        });
        
        fechaInscripcion.addEventListener('focus', () => {
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
        
        entidadSelect.addEventListener('focus', () => {
            limpiarErrorConstitucion(entidadSelect);
        });
    }
}

// Configurar intercepción del submit
function setupConstitucionSubmitInterception() {
    // Buscar la función original en el scope global de Alpine
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
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoConstitucion(input, `Este campo es obligatorio`);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoConstitucion(input, constitucionErrorMessages[fieldName]);
        constitucionFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
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
        fechaHoy.setHours(23, 59, 59, 999); // Fin del día actual
        
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
    
    // Validar que no sea una fecha futura
    if (value) {
        const fechaSeleccionada = new Date(value);
        const fechaHoy = new Date();
        fechaHoy.setHours(23, 59, 59, 999);
        
        if (fechaSeleccionada > fechaHoy) {
            mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción no puede ser futura');
            constitucionFieldStates.fecha_inscripcion = false;
            return false;
        }
        
        // Validar que la fecha de inscripción sea posterior a la fecha de constitución
        const fechaConstitucion = document.querySelector('[name="fecha_constitucion"]')?.value;
        if (fechaConstitucion) {
            const fechaConstitucionDate = new Date(fechaConstitucion);
            if (fechaSeleccionada < fechaConstitucionDate) {
                mostrarEstadoInvalidoConstitucion(input, 'La fecha de inscripción debe ser posterior a la fecha de constitución');
                constitucionFieldStates.fecha_inscripcion = false;
                return false;
            }
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
    console.log('Validando formulario de constitución completo...');
    
    let isValid = true;
    let camposConError = [];

    // Validar campos con patrones
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
        const input = document.querySelector('[name="fecha_constitucion"]');
        if (input) {
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Fecha de Constitución';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.fecha_constitucion]
            });
        }
    }

    const fechaInscripcionValid = validarFechaInscripcion();
    if (!fechaInscripcionValid) {
        isValid = false;
        const input = document.querySelector('[name="fecha_inscripcion"]');
        if (input) {
            const label = input.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Fecha de Inscripción';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.fecha_inscripcion]
            });
        }
    }

    // Validar entidad federativa
    const entidadValid = validarEntidadFederativa();
    if (!entidadValid) {
        isValid = false;
        const select = document.querySelector('[name="entidad_federativa"]');
        if (select) {
            const label = select.closest('.form-group')?.querySelector('label')?.textContent?.replace('*', '').trim() || 'Entidad Federativa';
            camposConError.push({
                nombre: label,
                errores: [constitucionErrorMessages.entidad_federativa]
            });
        }
    }

    if (!isValid) {
        mostrarModalErroresConstitucion(camposConError);
        return false;
    }

    console.log('Formulario de constitución válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoConstitucion(input) {
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
function mostrarEstadoInvalidoConstitucion(input, mensaje) {
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
function limpiarErrorConstitucion(input) {
    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
    
    const grupo = input.closest('.form-group');
    if (grupo) {
        const errorMessage = grupo.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Modal de errores específico para constitución
function mostrarModalErroresConstitucion(camposConError) {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('modal-errores-constitucion');
    if (modalAnterior) {
        modalAnterior.remove();
    }
    
    const totalErrores = camposConError.reduce((total, campo) => total + campo.errores.length, 0);
    
    const modal = document.createElement('div');
    modal.id = 'modal-errores-constitucion';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    
    let erroresHTML = '';
    camposConError.forEach((campo, index) => {
        erroresHTML += `
            <div class="mb-4 border-l-4 border-red-400 pl-4 py-2 bg-red-50 rounded-r-lg">
                <h4 class="font-semibold text-red-800 mb-2 flex items-center cursor-pointer hover:text-red-900 transition-colors" onclick="irACampoConstitucion('${campo.nombre}', ${index})">
                    <i class="fas fa-file-contract mr-2 text-red-600"></i>
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
                                <i class="fas fa-file-contract text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Errores en Constitución
                                </h3>
                                <p class="text-red-100 text-sm">
                                    Se encontraron ${totalErrores} error${totalErrores !== 1 ? 'es' : ''} en los datos de constitución
                                </p>
                            </div>
                        </div>
                        <button onclick="cerrarModalErroresConstitucion()" class="text-white hover:text-red-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white px-6 py-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <div class="mb-4">
                        <p class="text-gray-700 text-sm mb-4">
                            Por favor, corrija los siguientes errores en los datos de constitución:
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
                        <button onclick="cerrarModalErroresConstitucion()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
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
            cerrarModalErroresConstitucion();
        }
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModalErroresConstitucion();
        }
    });
}

// Cerrar modal de errores
function cerrarModalErroresConstitucion() {
    const modal = document.getElementById('modal-errores-constitucion');
    if (modal) {
        modal.classList.add('animate-modal-disappear');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Ir a un campo específico desde el modal
function irACampoConstitucion(nombreCampo, index) {
    cerrarModalErroresConstitucion();
    
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
    setTimeout(initConstitucionValidation, 100);
});

// También inicializar si Alpine.js ya está cargado
if (window.Alpine) {
    Alpine.data('constitucionValidation', () => ({
        init() {
            initConstitucionValidation();
        }
    }));
} 