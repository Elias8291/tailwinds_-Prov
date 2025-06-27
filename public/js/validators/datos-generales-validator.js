/**
 * Validador para el formulario de Datos Generales
 * Valida campos específicos con patrones regex y feedback visual
 */

// Patrones de validación para datos generales
const datosGeneralesValidationPatterns = {
    giro: /^.{10,500}$/,
    pagina_web: /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/,
    contacto_nombre: /^[a-zA-ZÀ-ÿñÑ\s]{2,100}$/,
    contacto_cargo: /^[a-zA-ZÀ-ÿñÑ\s]{2,50}$/,
    contacto_correo: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    contacto_telefono: /^[0-9]{10}$/
};

// Mensajes de error específicos
const datosGeneralesErrorMessages = {
    giro: 'El giro debe tener entre 10 y 500 caracteres',
    pagina_web: 'Ingrese una URL válida (ejemplo: https://www.ejemplo.com)',
    contacto_nombre: 'El nombre debe tener entre 2 y 100 caracteres (solo letras y espacios)',
    contacto_cargo: 'El cargo debe tener entre 2 y 50 caracteres (solo letras y espacios)',
    contacto_correo: 'Ingrese un correo electrónico válido',
    contacto_telefono: 'El teléfono debe tener exactamente 10 dígitos',
    actividades_required: 'Debe seleccionar al menos una actividad económica'
};

// Estado de validación de campos
let datosGeneralesFieldStates = {};

// Función principal de inicialización
function initDatosGeneralesValidation() {
    console.log('Inicializando validación de datos generales...');
    
    // Verificar que el formulario existe
    const form = document.getElementById('datos-generales-form');
    if (!form) {
        console.log('Formulario de datos generales no encontrado');
        return;
    }

    // Verificar integridad de tramite_id
    const tramiteIdInput = form.querySelector('input[name="tramite_id"]');
    if (tramiteIdInput) {
        const tramiteId = tramiteIdInput.value;
        console.log('🔍 Verificando tramite_id desde formulario:', tramiteId, typeof tramiteId);
        
        if (!tramiteId || tramiteId === '' || tramiteId.includes('object') || tramiteId.includes('HTMLElement')) {
            console.error('❌ tramite_id inválido detectado:', tramiteId);
            alert('Error: ID de trámite inválido. La página será recargada.');
            window.location.reload();
            return;
        }
    }

    // Configurar eventos de validación
    setupDatosGeneralesValidationEvents();
    
    // Configurar intercepción del submit
    setupDatosGeneralesSubmitInterception();
    
    console.log('Validación de datos generales inicializada correctamente');
}

// Configurar eventos de validación en tiempo real
function setupDatosGeneralesValidationEvents() {
    const fieldsToValidate = [
        'giro',
        'pagina_web',
        'contacto_nombre',
        'contacto_cargo',
        'contacto_correo',
        'contacto_telefono'
    ];

    fieldsToValidate.forEach(fieldName => {
        const input = document.getElementById(fieldName);
        if (input) {
            // Agregar eventos de validación
            input.addEventListener('blur', handleDatosGeneralesValidation);
            input.addEventListener('input', handleDatosGeneralesInput);
            
            // Evento especial para teléfono (solo números)
            if (fieldName === 'contacto_telefono') {
                input.addEventListener('keypress', handleTelefonoKeypress);
            }
        }
    });

    // Validación especial para actividades económicas
    const actividadesInput = document.getElementById('actividades_seleccionadas_input');
    if (actividadesInput) {
        // Observer para detectar cambios en actividades
        const observer = new MutationObserver(() => {
            validarActividades();
        });
        
        observer.observe(actividadesInput, {
            attributes: true,
            attributeFilter: ['value']
        });
    }
}

// Manejar validación de campo
function handleDatosGeneralesValidation(e) {
    const input = e.target;
    const fieldName = input.id;
    validarCampoDatosGenerales(input, fieldName);
}

// Manejar input para limpiar errores
function handleDatosGeneralesInput(e) {
    const input = e.target;
    limpiarErrorDatosGenerales(input);
}

// Manejar teclas presionadas en teléfono (solo números)
function handleTelefonoKeypress(e) {
    const char = String.fromCharCode(e.which);
    if (!/[0-9]/.test(char)) {
        e.preventDefault();
    }
}

// Configurar intercepción del submit
function setupDatosGeneralesSubmitInterception() {
    const form = document.getElementById('datos-generales-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormularioDatosGeneralesCompleto()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }
}

// Validar un campo específico
function validarCampoDatosGenerales(input, fieldName) {
    if (!input || !datosGeneralesValidationPatterns[fieldName]) return false;

    const value = input.value.trim();
    const pattern = datosGeneralesValidationPatterns[fieldName];
    
    // Verificar si el campo es requerido
    const isRequired = input.hasAttribute('required');
    
    // Si está vacío y es requerido
    if (isRequired && value === '') {
        mostrarEstadoInvalidoDatosGenerales(input, 'Este campo es obligatorio');
        datosGeneralesFieldStates[fieldName] = false;
        return false;
    }
    
    // Si el campo es opcional y está vacío, es válido
    if (!isRequired && value === '') {
        mostrarEstadoValidoDatosGenerales(input);
        datosGeneralesFieldStates[fieldName] = true;
        return true;
    }
    
    // Si tiene valor, validar con regex
    if (value !== '' && !pattern.test(value)) {
        mostrarEstadoInvalidoDatosGenerales(input, datosGeneralesErrorMessages[fieldName]);
        datosGeneralesFieldStates[fieldName] = false;
        return false;
    }
    
    // Validaciones especiales
    if (fieldName === 'giro' && value.length < 10) {
        mostrarEstadoInvalidoDatosGenerales(input, 'El giro debe tener al menos 10 caracteres');
        datosGeneralesFieldStates[fieldName] = false;
        return false;
    }
    
    // Campo válido
    mostrarEstadoValidoDatosGenerales(input);
    datosGeneralesFieldStates[fieldName] = true;
    return true;
}

// Validar actividades económicas
function validarActividades() {
    const hiddenInput = document.getElementById('actividades_seleccionadas_input');
    const tagsContainer = document.getElementById('actividades-seleccionadas');
    
    if (!hiddenInput || !tagsContainer) return false;
    
    const actividades = hiddenInput.value ? JSON.parse(hiddenInput.value) : [];
    
    if (actividades.length === 0) {
        // Mostrar error en el contenedor de tags
        tagsContainer.classList.add('border-red-300', 'bg-red-50');
        tagsContainer.classList.remove('border-[#9d2449]/20');
        
        // Mostrar mensaje de error
        const errorMsg = tagsContainer.querySelector('.error-message');
        if (!errorMsg) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-sm mt-1 flex items-center';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i>' + datosGeneralesErrorMessages.actividades_required;
            tagsContainer.parentNode.appendChild(errorDiv);
        }
        
        datosGeneralesFieldStates.actividades = false;
        return false;
    } else {
        // Limpiar estado de error
        tagsContainer.classList.remove('border-red-300', 'bg-red-50');
        tagsContainer.classList.add('border-[#9d2449]/20');
        
        // Remover mensaje de error
        const errorMsg = tagsContainer.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
        
        datosGeneralesFieldStates.actividades = true;
        return true;
    }
}

// Validar formulario completo
function validarFormularioDatosGeneralesCompleto() {
    console.log('Validando formulario de datos generales completo...');
    
    // PASO 1: Verificar integridad de datos críticos
    const form = document.getElementById('datos-generales-form');
    if (!form) {
        console.error('❌ Formulario no encontrado durante validación');
        alert('Error crítico: Formulario no encontrado');
        return false;
    }

    // Verificar tramite_id
    const tramiteIdInput = form.querySelector('input[name="tramite_id"]');
    if (!tramiteIdInput || !tramiteIdInput.value) {
        console.error('❌ tramite_id no encontrado o vacío');
        alert('Error: ID de trámite no válido');
        return false;
    }

    const tramiteId = tramiteIdInput.value;
    if (typeof tramiteId !== 'string' || tramiteId.includes('object') || tramiteId.includes('HTMLElement') || tramiteId.trim() === '') {
        console.error('❌ tramite_id corrupto:', tramiteId);
        alert('Error: ID de trámite corrupto. La página será recargada.');
        window.location.reload();
        return false;
    }

    console.log('✅ tramite_id válido:', tramiteId);
    
    let isValid = true;
    let camposConError = [];

    // PASO 2: Validar todos los campos
    const fieldsToValidate = {
        'giro': 'Giro',
        'pagina_web': 'Página Web',
        'contacto_nombre': 'Nombre del Contacto',
        'contacto_cargo': 'Cargo del Contacto',
        'contacto_correo': 'Correo del Contacto',
        'contacto_telefono': 'Teléfono del Contacto'
    };

    Object.entries(fieldsToValidate).forEach(([fieldId, fieldLabel]) => {
        const input = document.getElementById(fieldId);
        if (input) {
            const fieldValid = validarCampoDatosGenerales(input, fieldId);
            if (!fieldValid) {
                isValid = false;
                camposConError.push({
                    nombre: fieldLabel,
                    errores: [datosGeneralesErrorMessages[fieldId] || 'Error de validación']
                });
            }
        }
    });

    // PASO 3: Validar actividades económicas
    const actividadesValid = validarActividades();
    if (!actividadesValid) {
        isValid = false;
        camposConError.push({
            nombre: 'Actividades Económicas',
            errores: [datosGeneralesErrorMessages.actividades_required]
        });
    }

    if (!isValid) {
        mostrarModalErroresDatosGenerales(camposConError);
        return false;
    }

    console.log('Formulario de datos generales válido');
    return true;
}

// Mostrar estado válido
function mostrarEstadoValidoDatosGenerales(input) {
    input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
    input.classList.add('border-green-300', 'focus:border-green-500', 'focus:ring-green-200');
    
    // Remover mensaje de error previo
    const errorElement = input.parentNode.nextElementSibling;
    if (errorElement && errorElement.classList.contains('text-red-600')) {
        errorElement.remove();
    }
}

// Mostrar estado inválido
function mostrarEstadoInvalidoDatosGenerales(input, mensaje) {
    input.classList.remove('border-green-300', 'focus:border-green-500', 'focus:ring-green-200');
    input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
    
    // Remover mensaje de error previo
    const errorElement = input.parentNode.nextElementSibling;
    if (errorElement && errorElement.classList.contains('text-red-600')) {
        errorElement.remove();
    }
    
    // Crear nuevo mensaje de error
    const errorDiv = document.createElement('p');
    errorDiv.className = 'mt-1 text-sm text-red-600 flex items-center';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${mensaje}`;
    input.parentNode.insertAdjacentElement('afterend', errorDiv);
}

// Limpiar error
function limpiarErrorDatosGenerales(input) {
    input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200', 'border-green-300', 'focus:border-green-500', 'focus:ring-green-200');
    
    // Remover mensaje de error
    const errorElement = input.parentNode.nextElementSibling;
    if (errorElement && errorElement.classList.contains('text-red-600')) {
        errorElement.remove();
    }
}

// Mostrar modal de errores
function mostrarModalErroresDatosGenerales(camposConError) {
    const modalHtml = `
        <div id="modal-errores-datos-generales" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-full p-2 mr-3">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Errores de Validación</h3>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">Por favor corrija los siguientes errores:</p>
                    <ul class="space-y-2">
                        ${camposConError.map(campo => `
                            <li class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="font-medium text-red-800">${campo.nombre}</div>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    ${campo.errores.map(error => `<li>${error}</li>`).join('')}
                                </ul>
                            </li>
                        `).join('')}
                    </ul>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="cerrarModalErroresDatosGenerales()" 
                            class="px-4 py-2 bg-[#9d2449] text-white rounded-lg hover:bg-[#8a203f] transition-colors">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal previo si existe
    const modalPrevio = document.getElementById('modal-errores-datos-generales');
    if (modalPrevio) {
        modalPrevio.remove();
    }
    
    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Cerrar modal de errores
function cerrarModalErroresDatosGenerales() {
    const modal = document.getElementById('modal-errores-datos-generales');
    if (modal) {
        modal.remove();
    }
}

// Función de Alpine.js para integración
function datosGeneralesValidationData() {
    return {
        init() {
            // Inicializar validación al cargar el componente
            setTimeout(() => {
                initDatosGeneralesValidation();
            }, 100);
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initDatosGeneralesValidation();
});

console.log('Validador de datos generales cargado con validaciones del lado del cliente');
