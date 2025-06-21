/**
 * Sistema centralizado para manejar estados de carga en formularios
 * Proporciona funciones reutilizables para mostrar/ocultar indicadores de carga
 */

// Funciones generales para cualquier botón
function mostrarEstadoCarga(botonId, textoId, loadingId) {
    const btn = document.getElementById(botonId);
    const btnText = document.getElementById(textoId);
    const btnLoading = document.getElementById(loadingId);
    
    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.classList.remove('hover:-translate-y-0.5', 'transform-gpu');
        
        if (btnText) btnText.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');
    }
}

function ocultarEstadoCarga(botonId, textoId, loadingId) {
    const btn = document.getElementById(botonId);
    const btnText = document.getElementById(textoId);
    const btnLoading = document.getElementById(loadingId);
    
    if (btn) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.classList.add('hover:-translate-y-0.5', 'transform-gpu');
        
        if (btnText) btnText.classList.remove('hidden');
        if (btnLoading) btnLoading.classList.add('hidden');
    }
}

// Funciones específicas para cada sección (mantener compatibilidad)
function mostrarEstadoCargaFormulario(seccion) {
    const btn = document.getElementById(`btn-guardar-${seccion}`);
    const btnAlt = document.getElementById(`btn-guardar-${seccion}-alt`);
    const btnText = document.getElementById(`btn-text-${seccion}`);
    const btnTextAlt = document.getElementById(`btn-text-${seccion}-alt`);
    const btnLoading = document.getElementById(`btn-loading-${seccion}`);
    const btnLoadingAlt = document.getElementById(`btn-loading-${seccion}-alt`);
    
    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.classList.remove('hover:-translate-y-0.5', 'transform-gpu');
        if (btnText) btnText.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');
    }
    
    if (btnAlt) {
        btnAlt.disabled = true;
        btnAlt.classList.add('opacity-50', 'cursor-not-allowed');
        btnAlt.classList.remove('hover:-translate-y-0.5', 'transform-gpu');
        if (btnTextAlt) btnTextAlt.classList.add('hidden');
        if (btnLoadingAlt) btnLoadingAlt.classList.remove('hidden');
    }
}

function ocultarEstadoCargaFormulario(seccion) {
    const btn = document.getElementById(`btn-guardar-${seccion}`);
    const btnAlt = document.getElementById(`btn-guardar-${seccion}-alt`);
    const btnText = document.getElementById(`btn-text-${seccion}`);
    const btnTextAlt = document.getElementById(`btn-text-${seccion}-alt`);
    const btnLoading = document.getElementById(`btn-loading-${seccion}`);
    const btnLoadingAlt = document.getElementById(`btn-loading-${seccion}-alt`);
    
    if (btn) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.classList.add('hover:-translate-y-0.5', 'transform-gpu');
        if (btnText) btnText.classList.remove('hidden');
        if (btnLoading) btnLoading.classList.add('hidden');
    }
    
    if (btnAlt) {
        btnAlt.disabled = false;
        btnAlt.classList.remove('opacity-50', 'cursor-not-allowed');
        btnAlt.classList.add('hover:-translate-y-0.5', 'transform-gpu');
        if (btnTextAlt) btnTextAlt.classList.remove('hidden');
        if (btnLoadingAlt) btnLoadingAlt.classList.add('hidden');
    }
}

// Función para mostrar notificación de progreso
function mostrarNotificacionProgreso(mensaje, tipo = 'info') {
    // Crear elemento de notificación si no existe
    let notificacion = document.getElementById('loading-notification');
    if (!notificacion) {
        notificacion = document.createElement('div');
        notificacion.id = 'loading-notification';
        notificacion.className = 'fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full';
        document.body.appendChild(notificacion);
    }
    
    // Configurar estilos según el tipo
    const estilos = {
        info: 'bg-blue-500 text-white',
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    notificacion.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 ${estilos[tipo] || estilos.info}`;
    
    // Contenido con spinner si es info
    const spinner = tipo === 'info' ? `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    ` : '';
    
    notificacion.innerHTML = `
        <div class="flex items-center">
            ${spinner}
            <span>${mensaje}</span>
        </div>
    `;
    
    // Mostrar notificación
    setTimeout(() => {
        notificacion.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-ocultar después de 3 segundos (excepto para info)
    if (tipo !== 'info') {
        setTimeout(() => {
            ocultarNotificacionProgreso();
        }, 3000);
    }
}

function ocultarNotificacionProgreso() {
    const notificacion = document.getElementById('loading-notification');
    if (notificacion) {
        notificacion.classList.add('translate-x-full');
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
        }, 300);
    }
}

// Función para interceptar envíos de formularios y mostrar estado de carga automáticamente
function interceptarFormularios() {
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.tagName === 'FORM') {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                // Mostrar estado de carga en el botón
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                
                // Agregar spinner
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Enviando...
                `;
                
                // Restaurar estado original si hay error
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.innerHTML = originalText;
                    }
                }, 10000); // 10 segundos de timeout
            }
        }
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    interceptarFormularios();
});

// Exportar funciones para uso global
window.mostrarEstadoCarga = mostrarEstadoCarga;
window.ocultarEstadoCarga = ocultarEstadoCarga;
window.mostrarEstadoCargaFormulario = mostrarEstadoCargaFormulario;
window.ocultarEstadoCargaFormulario = ocultarEstadoCargaFormulario;
window.mostrarNotificacionProgreso = mostrarNotificacionProgreso;
window.ocultarNotificacionProgreso = ocultarNotificacionProgreso; 