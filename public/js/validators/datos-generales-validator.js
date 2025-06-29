/**
 * Validador para la sección de Datos Generales
 * Maneja la validación tanto para personas físicas como morales
 */

// Variables globales
let formularioValidado = false;
let tramiteId = null;

// Función principal de inicialización
function inicializarValidacionDatosGenerales() {
    const formulario = document.getElementById('form-datos-generales');
    
    if (!formulario) {
        return;
    }

    // Detectar tramite_id desde el formulario
    const tramiteIdInput = document.querySelector('input[name="tramite_id"]');
    if (tramiteIdInput) {
        tramiteId = tramiteIdInput.value;
        
        // Validar que tramite_id sea válido
        if (!tramiteId || tramiteId.trim() === '' || tramiteId === 'undefined' || tramiteId === 'null') {
            return;
        }

        // Verificar que tramite_id sea un número válido
        if (isNaN(parseInt(tramiteId))) {
            return;
        }
    }

    // Inicializar validaciones y eventos
    configurarEventos(formulario);
}

// Configurar eventos del formulario
function configurarEventos(formulario) {
    // Configurar validación en tiempo real
    const campos = formulario.querySelectorAll('input, select, textarea');
    campos.forEach(campo => {
        campo.addEventListener('blur', validarCampo);
        campo.addEventListener('change', validarCampo);
    });

    // Interceptar envío del formulario
    formulario.addEventListener('submit', function(e) {
        if (!validarFormularioCompleto()) {
                e.preventDefault();
                return false;
            }
        });
    }

// Función para validar un campo individual
function validarCampo(event) {
    const campo = event.target;
    const valor = campo.value.trim();
    let esValido = true;
    
    // Validaciones específicas según el tipo de campo
    if (campo.hasAttribute('required') && !valor) {
        esValido = false;
    }
    
    // Actualizar UI según el resultado
    actualizarUIValidacion(campo, esValido);
    
    return esValido;
}

// Función para validar el formulario completo
function validarFormularioCompleto() {
    const formulario = document.getElementById('form-datos-generales');
    
    if (!formulario) {
        return false;
    }

    // Validar que tramite_id esté presente
    if (!tramiteId || tramiteId.trim() === '') {
        return false;
    }

    // Verificar que tramite_id sea válido
    if (isNaN(parseInt(tramiteId))) {
        return false;
    }

    let todosValidos = true;
    const campos = formulario.querySelectorAll('input[required], select[required], textarea[required]');
    
    campos.forEach(campo => {
        if (!validarCampo({ target: campo })) {
            todosValidos = false;
        }
    });

    if (todosValidos) {
        formularioValidado = true;
    }

    return todosValidos;
}

// Función para actualizar la UI de validación
function actualizarUIValidacion(campo, esValido) {
    const contenedor = campo.closest('.form-group') || campo.parentElement;
    
    if (esValido) {
        contenedor.classList.remove('error');
        contenedor.classList.add('valid');
    } else {
        contenedor.classList.remove('valid');
        contenedor.classList.add('error');
    }
}

// Función para obtener datos del formulario
function obtenerDatosFormulario() {
    const formulario = document.getElementById('form-datos-generales');
    if (!formulario) return null;

    const formData = new FormData(formulario);
    const datos = {};
    
    for (let [key, value] of formData.entries()) {
        datos[key] = value;
    }
    
    return datos;
}

// Función para limpiar errores de validación
function limpiarErroresValidacion() {
    const campos = document.querySelectorAll('.form-group.error');
    campos.forEach(campo => {
        campo.classList.remove('error');
    });
}

// Función para mostrar errores de validación
function mostrarErroresValidacion(errores) {
    errores.forEach(error => {
        const campo = document.getElementById(error.campo);
        if (campo) {
            const contenedor = campo.closest('.form-group') || campo.parentElement;
            contenedor.classList.add('error');
        }
    });
}

// Función para resetear el formulario
function resetearFormulario() {
    const formulario = document.getElementById('form-datos-generales');
    if (formulario) {
        formulario.reset();
        limpiarErroresValidacion();
        formularioValidado = false;
    }
}

// Función para validar RFC
function validarRFC(rfc) {
    const rfcPattern = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
    return rfcPattern.test(rfc);
}

// Función para validar CURP (solo para personas físicas)
function validarCURP(curp) {
    const curpPattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/;
    return curpPattern.test(curp);
}

// Función para validar email
function validarEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

// Función para validar teléfono
function validarTelefono(telefono) {
    const telefonoPattern = /^[0-9]{10}$/;
    return telefonoPattern.test(telefono.replace(/\D/g, ''));
}

// Función para guardar datos automáticamente
async function guardarDatosAutomaticamente() {
    if (!formularioValidado || !tramiteId) return;

    const datos = obtenerDatosFormulario();
    if (!datos) return;

    try {
        const response = await fetch(`/tramites/${tramiteId}/datos-generales`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(datos)
        });

        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Error al guardar');
        }

        return result;
    } catch (error) {
        throw error;
    }
}

// Integración con Alpine.js
document.addEventListener('DOMContentLoaded', function() {
    inicializarValidacionDatosGenerales();
});

// Para Alpine.js si está disponible
if (typeof Alpine !== 'undefined') {
    Alpine.data('datosGeneralesValidation', () => ({
        init() {
            inicializarValidacionDatosGenerales();
        }
    }));
}

// Función de utilidad para validaciones personalizadas
window.validarDatosGenerales = validarFormularioCompleto;
window.obtenerDatosGenerales = obtenerDatosFormulario;
window.guardarDatosGenerales = guardarDatosAutomaticamente;
