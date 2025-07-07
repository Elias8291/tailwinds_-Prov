// ===== ESTADO TRAMITE HANDLER =====
// Manejo completo del estado del trámite y actualizaciones automáticas

// Variables globales
let estadoPolling = {
    activo: false,
    intervalo: null,
    ultimoTimestamp: null,
    intentosReconexion: 0,
    maxIntentos: 3,
    tiempoEspera: 30000, // 30 segundos
    indicadorVisible: false
};

let tramiteId = null;
let prelineInitialized = false;

// ===== UTILIDADES DOM =====

// Función para verificar si un elemento es válido
function isValidElement(element) {
    return element && 
           typeof element === 'object' && 
           element.nodeType === Node.ELEMENT_NODE && 
           typeof element.closest === 'function';
}

// Función para inicializar PrelineUI de forma segura
function initPrelineSafe() {
    if (typeof HSStaticMethods !== 'undefined' && !prelineInitialized) {
        try {
            // Esperar a que el DOM esté completamente cargado
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    HSStaticMethods.autoInit();
                    prelineInitialized = true;
                });
            } else {
                HSStaticMethods.autoInit();
                prelineInitialized = true;
            }
        } catch (error) {
            console.warn('Error inicializando PrelineUI:', error);
        }
    }
}

// Función para reinicializar PrelineUI después de cambios en el DOM
function reinitPrelineComponents() {
    if (typeof HSStaticMethods !== 'undefined' && prelineInitialized) {
        try {
            // Reinicializar componentes específicos
            const components = ['HSSelect', 'HSDropdown', 'HSComboBox'];
            components.forEach(component => {
                if (typeof window[component] !== 'undefined' && window[component].autoInit) {
                    window[component].autoInit();
                }
            });
        } catch (error) {
            console.warn('Error reinicializando componentes PrelineUI:', error);
        }
    }
}

// ===== FUNCIONES PRINCIPALES =====

async function habilitarEdicion(id) {
    if (!confirm('¿Está seguro de que desea habilitar la edición de este trámite?')) {
        return;
    }
    
    try {
        const response = await window.safeFetch(`/tramites-solicitante/habilitar-edicion/${id}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarNotificacion('success', data.message);
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                window.location.reload();
            }
        } else {
            mostrarNotificacion('error', data.message);
        }
    } catch (error) {
        console.error('Error habilitando edición:', error);
        mostrarNotificacion('error', 'Error al habilitar la edición del trámite');
    }
}

async function corregirSeccion(id, seccionId) {
    if (!confirm('¿Desea ir a corregir esta sección?')) {
        return;
    }
    
    try {
        const response = await window.safeFetch(`/tramites-solicitante/corregir-seccion/${id}/${seccionId}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            mostrarNotificacion('error', data.message);
        }
    } catch (error) {
        console.error('Error corrigiendo sección:', error);
        mostrarNotificacion('error', 'Error al habilitar la corrección de la sección');
    }
}

function toggleDocumentos(seccionId) {
    const panel = document.getElementById(`documentos-panel-${seccionId}`);
    const chevron = document.getElementById(`chevron-${seccionId}`);
    
    if (!panel || !chevron) {
        console.error('No se encontraron los elementos del panel de documentos');
        return;
    }
    
    // Prevenir conflictos con eventos de PrelineUI
    event.stopPropagation();
    
    if (panel.classList.contains('hidden')) {
        // Mostrar panel
        panel.classList.remove('hidden');
        panel.classList.add('animate-slideDown');
        chevron.style.transform = 'rotate(180deg)';
        
        // Smooth scroll opcional
        setTimeout(() => {
            panel.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest' 
            });
        }, 100);
    } else {
        // Ocultar panel
        panel.classList.add('hidden');
        panel.classList.remove('animate-slideDown');
        chevron.style.transform = 'rotate(0deg)';
    }
}

// ===== MANEJO DE ARCHIVOS =====

function handleFileChange(documentoId, input) {
    const file = input.files[0];
    
    if (!file) {
        return;
    }
    
    // Validaciones
    if (!file.type.includes('pdf')) {
        mostrarNotificacion('error', 'Solo se permiten archivos PDF');
        input.value = '';
        return;
    }
    
    if (file.size > 50 * 1024 * 1024) { // 50MB
        mostrarNotificacion('error', 'El archivo es demasiado grande. Máximo 50MB permitido');
        input.value = '';
        return;
    }
    
    // Confirmar reemplazo
    if (!confirm('¿Está seguro de que desea reemplazar este documento?')) {
        input.value = '';
        return;
    }
    
    subirDocumento(documentoId, file);
}

async function subirDocumento(documentoId, file) {
    const progressContainer = document.getElementById(`progress-${documentoId}`);
    const progressBar = document.getElementById(`progress-bar-${documentoId}`);
    const progressText = document.getElementById(`progress-text-${documentoId}`);
    const botonSubir = document.getElementById(`btn-subir-${documentoId}`);
    
    try {
        // Mostrar progreso
        if (progressContainer) progressContainer.classList.remove('hidden');
        if (botonSubir) {
            botonSubir.disabled = true;
            botonSubir.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Progreso inicial
        if (progressBar) {
            progressBar.classList.add('progress-pulse');
            progressBar.style.width = '10%';
        }
        if (progressText) progressText.textContent = 'Preparando archivo...';
        
        // Crear FormData
        const formData = new FormData();
        formData.append('archivo', file);
        formData.append('documento_solicitante_id', documentoId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Progreso a 30%
        setTimeout(() => {
            if (progressBar) progressBar.style.width = '30%';
            if (progressText) progressText.textContent = 'Subiendo archivo...';
        }, 200);
        
        // Hacer petición
        const response = await fetch('/tramites-solicitante/reemplazar-documento', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        // Progreso a 70%
        if (progressBar) progressBar.style.width = '70%';
        if (progressText) progressText.textContent = 'Procesando...';
        
        const data = await response.json();
        
        // Progreso a 100%
        if (progressBar) {
            progressBar.style.width = '100%';
            progressBar.classList.remove('progress-pulse');
        }
        if (progressText) progressText.textContent = 'Completado';
        
        if (data.success) {
            mostrarNotificacion('success', data.message || 'Documento subido correctamente');
            
            // Actualizar la interfaz del documento
            setTimeout(() => {
                actualizarDocumentoUI(documentoId, 'En Revision', file.name);
            }, 500);
        } else {
            throw new Error(data.message || 'Error al subir el documento');
        }
        
    } catch (error) {
        console.error('Error al subir documento:', error);
        mostrarNotificacion('error', error.message || 'Error de conexión al subir el documento');
        
        // Resetear progreso en caso de error
        if (progressBar) {
            progressBar.classList.remove('progress-pulse');
            progressBar.style.width = '0%';
        }
        if (progressText) progressText.textContent = 'Error en la subida';
    } finally {
        // Ocultar progreso y restaurar botón después de un delay
        setTimeout(() => {
            if (progressContainer) progressContainer.classList.add('hidden');
            if (botonSubir) {
                botonSubir.disabled = false;
                botonSubir.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            if (progressBar) {
                progressBar.classList.remove('progress-pulse');
                progressBar.style.width = '0%';
            }
        }, 2000);
    }
}

function actualizarDocumentoUI(documentoId, nuevoEstado, nombreArchivo) {
    const documentoCard = document.querySelector(`input[id="file-${documentoId}"]`)?.closest('.documento-card');
    
    if (!documentoCard) {
        console.error('No se encontró la tarjeta del documento');
        // Recargar página como fallback
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        return;
    }
    
    // Actualizar clases y colores
    documentoCard.classList.remove('bg-red-50', 'border-red-200');
    documentoCard.classList.add('bg-blue-50', 'border-blue-200');
    
    // Actualizar estado visual
    const estadoElement = documentoCard.querySelector('.flex.items-center:last-child');
    if (estadoElement) {
        estadoElement.innerHTML = `
            <i class="fas fa-clock text-blue-600 mr-1"></i>
            <span>En Revisión</span>
        `;
        estadoElement.className = 'flex items-center text-blue-700 text-xs font-medium';
    }
    
    // Remover observaciones de rechazo
    const observacionesDiv = documentoCard.querySelector('.bg-red-100');
    if (observacionesDiv) {
        observacionesDiv.remove();
    }
    
    // Remover botón de subida
    const botonContainer = documentoCard.querySelector('.border-t.border-red-200');
    if (botonContainer) {
        botonContainer.remove();
    }
    
    // Agregar indicación de archivo nuevo
    const fechaElement = documentoCard.querySelector('.text-xs.text-gray-500');
    if (fechaElement) {
        fechaElement.innerHTML = `
            <i class="fas fa-upload mr-1"></i>
            Nuevo archivo subido: ${nombreArchivo}
        `;
    }
    
    // Efecto visual de actualización
    documentoCard.classList.add('documento-updated');
    setTimeout(() => {
        documentoCard.classList.remove('documento-updated');
    }, 600);
}

// ===== SISTEMA DE NOTIFICACIONES =====

function mostrarNotificacion(tipo, mensaje) {
    // Usar la función global si está disponible
    if (typeof window.showGlobalNotification === 'function') {
        window.showGlobalNotification(tipo, mensaje);
        return;
    }
    
    // Fallback a la implementación local
    const existentes = document.querySelectorAll('.notificacion-estado');
    existentes.forEach(el => el.remove());
    
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion-estado fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 max-w-sm ${
        tipo === 'success' ? 'bg-green-100 border border-green-300 text-green-800' : 'bg-red-100 border border-red-300 text-red-800'
    }`;
    
    notificacion.innerHTML = `
        <div class="flex items-start">
            <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium">${mensaje}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.style.transform = 'translateX(0)';
    }, 10);
    
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notificacion.remove();
            }, 300);
        }
    }, 5000);
}

// ===== SISTEMA DE ACTUALIZACIÓN AUTOMÁTICA =====

// Iniciar el polling automático
function iniciarPolling() {
    if (estadoPolling.activo) {
        return;
    }
    
    estadoPolling.activo = true;
    estadoPolling.intentosReconexion = 0;
    
    console.log('🔄 Iniciando sistema de actualización automática');
    
    // Actualizar botón de control
    actualizarBotonPolling();
    
    // Hacer la primera consulta inmediatamente
    consultarEstado();
    
    // Configurar intervalo para consultas periódicas
    estadoPolling.intervalo = setInterval(consultarEstado, estadoPolling.tiempoEspera);
}

// Detener el polling
function detenerPolling() {
    if (estadoPolling.intervalo) {
        clearInterval(estadoPolling.intervalo);
        estadoPolling.intervalo = null;
    }
    estadoPolling.activo = false;
    console.log('⏸️ Sistema de actualización pausado');
    actualizarBotonPolling();
}

// Toggle del polling manual
function togglePolling() {
    if (estadoPolling.activo) {
        detenerPolling();
        mostrarNotificacion('success', 'Actualizaciones automáticas pausadas');
    } else {
        iniciarPolling();
        mostrarNotificacion('success', 'Actualizaciones automáticas reanudadas');
    }
}

// Actualizar el botón de control de polling
function actualizarBotonPolling() {
    const boton = document.getElementById('btn-toggle-polling');
    const icono = document.getElementById('polling-icon');
    const texto = document.getElementById('polling-text');
    
    if (boton && icono && texto) {
        if (estadoPolling.activo) {
            icono.className = 'fas fa-sync-alt mr-2 animate-spin';
            texto.textContent = 'Actualizando...';
            boton.className = 'flex items-center px-4 py-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200 border border-blue-200 hover:border-blue-300 text-sm';
        } else {
            icono.className = 'fas fa-play mr-2';
            texto.textContent = 'Pausado';
            boton.className = 'flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-all duration-200 border border-gray-200 hover:border-gray-300 text-sm';
        }
    }
}

// Consultar el estado actual del trámite
async function consultarEstado() {
    try {
        mostrarIndicadorActualizacion();
        
        const response = await window.safeFetch(`/tramites-solicitante/estado-actualizado/${tramiteId}`);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Verificar si hay cambios
            if (estadoPolling.ultimoTimestamp && data.data.timestamp !== estadoPolling.ultimoTimestamp) {
                console.log('🔄 Cambios detectados en el trámite');
                actualizarInterfaz(data.data);
                mostrarNotificacion('success', '¡Estado del trámite actualizado!');
            }
            
            estadoPolling.ultimoTimestamp = data.data.timestamp;
            estadoPolling.intentosReconexion = 0;
            
        } else {
            console.warn('⚠️ Error al obtener estado:', data.message);
            manejarErrorPolling();
        }
        
    } catch (error) {
        console.error('❌ Error en consulta:', error);
        manejarErrorPolling();
    } finally {
        ocultarIndicadorActualizacion();
    }
}

// Actualizar la interfaz con los nuevos datos
function actualizarInterfaz(datos) {
    // Pausar temporalmente eventos para evitar conflictos
    const preventivoEvento = (e) => {
        if (e.target && !isValidElement(e.target)) {
            e.stopPropagation();
        }
    };
    
    document.addEventListener('click', preventivoEvento, true);
    
    try {
        // Actualizar componentes
        actualizarEstadoPrincipal(datos.tramite);
        actualizarProgreso(datos.tramite);
        actualizarDocumentos(datos.documentos, datos.estadisticas_documentos);
        actualizarSecciones(datos.secciones);
        actualizarCita(datos.cita, datos.tramite.estado);
        actualizarBotonesAccion(datos.tramite);
        
        // Reinicializar componentes PrelineUI después de cambios
        setTimeout(() => {
            reinitPrelineComponents();
        }, 100);
        
    } catch (error) {
        console.error('Error actualizando interfaz:', error);
    } finally {
        // Remover listener preventivo
        setTimeout(() => {
            document.removeEventListener('click', preventivoEvento, true);
        }, 200);
    }
}

// Actualizar estado principal del trámite
function actualizarEstadoPrincipal(tramite) {
    // Actualizar icono y colores del estado
    const estadoContainer = document.querySelector('.w-20.h-20.rounded-full');
    const estadoTitulo = document.querySelector('.text-2xl.font-bold');
    const estadoDescripcion = document.querySelector('.text-gray-600.text-lg');
    
    if (estadoContainer && estadoTitulo && estadoDescripcion) {
        // Aplicar animación de highlight
        const contenedorPrincipal = estadoContainer.closest('.bg-white.rounded-xl');
        if (contenedorPrincipal) {
            contenedorPrincipal.classList.add('elemento-actualizado');
            setTimeout(() => {
                contenedorPrincipal.classList.remove('elemento-actualizado');
            }, 800);
        }
        
        // Remover clases existentes
        estadoContainer.className = estadoContainer.className.replace(/bg-\w+-\d+|border-\w+-\d+/g, '');
        estadoTitulo.className = estadoTitulo.className.replace(/text-\w+-\d+/g, '');
        
        // Agregar nuevas clases según el estado
        const estadoClasses = getEstadoClasses(tramite.estado);
        estadoContainer.className += ` ${estadoClasses.container}`;
        estadoTitulo.className += ` ${estadoClasses.titulo}`;
        estadoTitulo.textContent = tramite.estado;
        
        // Actualizar icono
        const icono = estadoContainer.querySelector('i');
        if (icono) {
            icono.className = `text-3xl ${estadoClasses.icono}`;
        }
        
        // Actualizar descripción
        estadoDescripcion.innerHTML = getEstadoDescripcion(tramite.estado);
    }
}

// Actualizar barra de progreso
function actualizarProgreso(tramite) {
    const circle = document.querySelector('circle[stroke="url(#progressGradient)"]');
    const porcentajeTexto = document.querySelector('.text-xl.font-bold.text-\\[\\#9d2449\\]');
    const progresoTexto = document.querySelector('.text-sm.text-gray-500.font-medium');
    
    if (circle && porcentajeTexto && progresoTexto) {
        const nuevoPorcentaje = tramite.porcentaje_progreso;
        const strokeDashoffset = 251 - (251 * nuevoPorcentaje / 100);
        
        // Animación suave del progreso
        circle.style.transition = 'stroke-dashoffset 1s ease-out';
        circle.style.strokeDashoffset = strokeDashoffset;
        
        porcentajeTexto.textContent = `${Math.round(nuevoPorcentaje)}%`;
        progresoTexto.textContent = `${tramite.progreso_tramite}/${tramite.progreso_maximo} secciones completadas`;
    }
}

// Actualizar documentos
function actualizarDocumentos(documentos, estadisticas) {
    documentos.forEach(doc => {
        const documentoCard = document.querySelector(`[data-documento-id="${doc.id}"]`);
        if (documentoCard) {
            actualizarDocumentoCard(documentoCard, doc);
        }
    });
    
    // Actualizar estadísticas
    actualizarEstadisticasDocumentos(estadisticas);
}

// Actualizar una tarjeta de documento individual
function actualizarDocumentoCard(card, documento) {
    const estadoClasses = getDocumentoClasses(documento.estado);
    
    // Aplicar animación de highlight
    card.classList.add('elemento-actualizado');
    setTimeout(() => {
        card.classList.remove('elemento-actualizado');
    }, 800);
    
    // Actualizar clases del contenedor
    card.className = card.className.replace(/bg-\w+-\d+|border-\w+-\d+/g, '');
    card.className += ` ${estadoClasses.container}`;
    
    // Actualizar estado visual
    const estadoElement = card.querySelector('.flex.items-center:last-child');
    if (estadoElement) {
        estadoElement.innerHTML = `
            <i class="fas ${estadoClasses.icono} mr-1"></i>
            <span>${documento.estado === 'En Revision' ? 'En Revisión' : documento.estado}</span>
        `;
        estadoElement.className = `flex items-center ${estadoClasses.texto} text-xs font-medium`;
    }
    
    // Actualizar observaciones
    const observacionesDiv = card.querySelector('.bg-red-100');
    if (documento.observaciones && documento.estado === 'Rechazado') {
        if (!observacionesDiv) {
            // Crear div de observaciones
            const nuevasObservaciones = document.createElement('div');
            nuevasObservaciones.className = 'mt-3 p-2 bg-red-100 rounded border-l-2 border-red-400';
            nuevasObservaciones.innerHTML = `
                <p class="text-xs text-red-800 font-medium">Observaciones:</p>
                <p class="text-xs text-red-700 mt-1">${documento.observaciones}</p>
            `;
            card.appendChild(nuevasObservaciones);
        }
    } else if (observacionesDiv) {
        observacionesDiv.remove();
    }
    
    // Actualizar botón de subida para documentos rechazados
    const botonContainer = card.querySelector('.border-t.border-red-200');
    if (documento.estado === 'Rechazado' && !botonContainer) {
        // Agregar botón de subida si no existe
        const nuevoBoton = document.createElement('div');
        nuevoBoton.className = 'mt-3 border-t border-red-200 pt-3';
        nuevoBoton.innerHTML = `
            <input type="file" 
                   id="file-${documento.id}" 
                   accept=".pdf"
                   class="hidden"
                   onchange="handleFileChange(${documento.id}, this)">
            <button onclick="document.getElementById('file-${documento.id}').click()"
                    class="btn-upload w-full flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                    id="btn-subir-${documento.id}">
                <i class="fas fa-upload mr-2"></i>
                Subir Nuevo Documento
            </button>
            <div id="progress-${documento.id}" class="hidden mt-2">
                <div class="w-full bg-red-100 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full transition-all duration-300" style="width: 0%" id="progress-bar-${documento.id}"></div>
                </div>
                <div class="text-xs text-red-600 mt-1 text-center" id="progress-text-${documento.id}">
                    Subiendo documento...
                </div>
            </div>
        `;
        card.appendChild(nuevoBoton);
    } else if (documento.estado !== 'Rechazado' && botonContainer) {
        botonContainer.remove();
    }
}

// Actualizar estadísticas de documentos
function actualizarEstadisticasDocumentos(estadisticas) {
    const aprobadosSpan = document.querySelector('.text-emerald-600.font-bold.text-lg');
    const rechazadosSpan = document.querySelector('.text-red-600.font-bold.text-lg');
    const revisionSpan = document.querySelector('.text-blue-600.font-bold.text-lg');
    
    if (aprobadosSpan) aprobadosSpan.textContent = estadisticas.aprobados;
    if (rechazadosSpan) rechazadosSpan.textContent = estadisticas.rechazados;
    if (revisionSpan) revisionSpan.textContent = estadisticas.en_revision;
}

// Actualizar secciones
function actualizarSecciones(secciones) {
    Object.keys(secciones).forEach(numero => {
        const seccion = secciones[numero];
        const seccionElement = document.querySelector(`[data-seccion="${numero}"]`);
        
        if (seccionElement) {
            actualizarSeccionElement(seccionElement, seccion);
        }
    });
}

// Actualizar elemento de sección individual
function actualizarSeccionElement(element, seccion) {
    // Esta función se puede expandir para actualizar las secciones individuales
    // Por ahora, solo actualiza el texto de estado para la sección de documentos
    if (seccion.nombre === 'Documentos') {
        const statusElement = element.querySelector('.text-sm.font-medium');
        if (statusElement) {
            statusElement.textContent = seccion.estado;
            
            // Actualizar colores según el estado
            statusElement.className = statusElement.className.replace(/text-\w+-\d+/g, '');
            if (seccion.rechazada) {
                statusElement.className += ' text-red-700';
            } else if (seccion.aprobada) {
                statusElement.className += ' text-emerald-700';
            } else if (seccion.en_revision) {
                statusElement.className += ' text-blue-700';
            } else {
                statusElement.className += ' text-gray-600';
            }
        }
    }
}

// Actualizar botones de acción
function actualizarBotonesAccion(tramite) {
    const contenedorBotones = document.querySelector('.flex.justify-center.pt-6.border-t');
    
    if (contenedorBotones) {
        let nuevoHTML = '';
        
        if (tramite.estado === 'Rechazado' && tramite.puede_ser_editado) {
            nuevoHTML = `
                <button onclick="habilitarEdicion(${tramite.id})" 
                        class="group px-8 py-4 bg-gradient-to-r from-[#9d2449] to-[#7a1d3a] text-white rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-105 font-semibold">
                    <i class="fas fa-edit mr-3 group-hover:animate-pulse"></i>Corregir mi Trámite
                </button>
            `;
        }
        
        contenedorBotones.innerHTML = nuevoHTML;
    }
}

// Actualizar información de la cita
function actualizarCita(cita, estadoTramite) {
    // Solo actualizar cita si el trámite está aprobado
    if (estadoTramite !== 'Aprobado') {
        return;
    }
    
    // Buscar el contenedor de "¿Qué sigue?"
    const contenedorQueSigue = document.querySelector('.bg-gradient-to-br.from-gray-50.to-gray-100');
    if (!contenedorQueSigue) {
        return;
    }
    
    // Buscar el contenedor de elementos dentro de "¿Qué sigue?"
    const espacioElementos = contenedorQueSigue.querySelector('.space-y-4');
    if (!espacioElementos) {
        return;
    }
    
    // Aplicar animación de highlight
    contenedorQueSigue.classList.add('elemento-actualizado');
    setTimeout(() => {
        contenedorQueSigue.classList.remove('elemento-actualizado');
    }, 800);
    
    // Construir nuevo HTML para el estado aprobado
    let nuevoHTML = `
        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
            <i class="fas fa-check-circle mr-3 text-emerald-600"></i>
            <span class="text-gray-700 font-medium">Su trámite ha sido aprobado exitosamente</span>
        </div>
    `;
    
    // Agregar información de la cita si existe
    if (cita) {
        nuevoHTML += `
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <i class="fas fa-calendar-check mr-3 text-emerald-600 text-lg"></i>
                    <span class="text-gray-800 font-bold">Cita Programada</span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar-alt mr-2 text-emerald-600"></i>
                        <span class="text-gray-700">
                            <strong>Fecha:</strong> ${cita.fecha}
                        </span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-clock mr-2 text-emerald-600"></i>
                        <span class="text-gray-700">
                            <strong>Hora:</strong> ${cita.hora}
                        </span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar-day mr-2 text-emerald-600"></i>
                        <span class="text-gray-700">
                            <strong>Día:</strong> ${cita.dia}
                        </span>
                    </div>
                    <div class="flex items-start text-sm pt-2 border-t border-emerald-200">
                        <i class="fas fa-map-marker-alt mr-2 text-emerald-600 mt-1"></i>
                        <span class="text-gray-700">
                            <strong>Ubicación:</strong><br>
                            ${cita.ubicacion.nombre}<br>
                            ${cita.ubicacion.edificio}, ${cita.ubicacion.modulo}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Agregar mensaje final
    nuevoHTML += `
        <div class="flex items-center p-3 bg-white bg-opacity-80 rounded-lg">
            <i class="fas fa-file-signature mr-3 text-emerald-600"></i>
            <span class="text-gray-700 font-medium">Asiste a tu cita para finalizar el proceso</span>
        </div>
    `;
    
    // Actualizar el contenido
    espacioElementos.innerHTML = nuevoHTML;
}

// ===== MANEJO DE ERRORES =====

// Manejar errores de polling
function manejarErrorPolling() {
    estadoPolling.intentosReconexion++;
    
    if (estadoPolling.intentosReconexion >= estadoPolling.maxIntentos) {
        console.log('⚠️ Máximo número de intentos alcanzado, deteniendo actualizaciones automáticas');
        detenerPolling();
        mostrarNotificacion('error', 'Error en las actualizaciones automáticas. La página seguirá funcionando normalmente.');
    } else {
        console.log(`🔄 Reintentando conexión (${estadoPolling.intentosReconexion}/${estadoPolling.maxIntentos})`);
    }
}

// Mostrar indicador de actualización
function mostrarIndicadorActualizacion() {
    if (estadoPolling.indicadorVisible) return;
    
    const indicador = document.createElement('div');
    indicador.id = 'indicador-actualizacion';
    indicador.className = 'fixed top-4 left-4 bg-blue-500 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-50 flex items-center';
    indicador.innerHTML = `
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
        Actualizando...
    `;
    
    document.body.appendChild(indicador);
    estadoPolling.indicadorVisible = true;
    
    // Ocultar después de 2 segundos máximo
    setTimeout(() => {
        ocultarIndicadorActualizacion();
    }, 2000);
}

// Ocultar indicador de actualización
function ocultarIndicadorActualizacion() {
    const indicador = document.getElementById('indicador-actualizacion');
    if (indicador) {
        indicador.remove();
    }
    estadoPolling.indicadorVisible = false;
}

// ===== FUNCIONES AUXILIARES =====

// Funciones auxiliares para obtener clases CSS
function getEstadoClasses(estado) {
    const classes = {
        'Aprobado': {
            container: 'bg-emerald-50 border-2 border-emerald-200',
            titulo: 'text-emerald-700',
            icono: 'fas fa-check-circle text-emerald-600'
        },
        'En Revision': {
            container: 'bg-blue-50 border-2 border-blue-200',
            titulo: 'text-blue-700',
            icono: 'fas fa-clock text-blue-600'
        },
        'Rechazado': {
            container: 'bg-red-50 border-2 border-red-200',
            titulo: 'text-red-700',
            icono: 'fas fa-exclamation-circle text-red-600'
        },
        'Pendiente': {
            container: 'bg-amber-50 border-2 border-amber-200',
            titulo: 'text-amber-700',
            icono: 'fas fa-hourglass-half text-amber-600'
        }
    };
    
    return classes[estado] || classes['Pendiente'];
}

function getEstadoDescripcion(estado) {
    const descripciones = {
        'Aprobado': '🎉 Su trámite ha sido aprobado exitosamente',
        'En Revision': '👀 Estamos revisando su documentación cuidadosamente',
        'Rechazado': '⚠️ Su trámite requiere algunas correcciones',
        'Pendiente': '📝 Complete su trámite para continuar con el proceso'
    };
    
    return descripciones[estado] || descripciones['Pendiente'];
}

function getDocumentoClasses(estado) {
    const classes = {
        'Aprobado': {
            container: 'bg-emerald-50 border-emerald-200',
            icono: 'fa-check-circle text-emerald-600',
            texto: 'text-emerald-700'
        },
        'Rechazado': {
            container: 'bg-red-50 border-red-200',
            icono: 'fa-times-circle text-red-600',
            texto: 'text-red-700'
        },
        'En Revision': {
            container: 'bg-blue-50 border-blue-200',
            icono: 'fa-clock text-blue-600',
            texto: 'text-blue-700'
        },
        'Pendiente': {
            container: 'bg-blue-50 border-blue-200',
            icono: 'fa-clock text-blue-600',
            texto: 'text-blue-700'
        }
    };
    
    return classes[estado] || classes['Pendiente'];
}

// ===== INICIALIZACIÓN =====

// Función de inicialización principal
function initEstadoTramiteHandler(id) {
    tramiteId = id;
    
    // Inicializar PrelineUI de forma segura
    initPrelineSafe();
    
    // Iniciar polling automático
    iniciarPolling();
    
    // Manejar visibilidad de página para pausar/reanudar polling
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (estadoPolling.activo) {
                detenerPolling();
                console.log('🔄 Polling pausado - página no visible');
            }
        } else {
            if (!estadoPolling.activo) {
                iniciarPolling();
                console.log('🔄 Polling reanudado - página visible');
            }
        }
    });
    
    // Cleanup al cerrar/salir de la página
    window.addEventListener('beforeunload', () => {
        detenerPolling();
    });
    
    console.log('✅ Estado Tramite Handler inicializado correctamente');
}

// Exportar funciones globales
window.habilitarEdicion = habilitarEdicion;
window.corregirSeccion = corregirSeccion;
window.toggleDocumentos = toggleDocumentos;
window.handleFileChange = handleFileChange;
window.togglePolling = togglePolling;
window.initEstadoTramiteHandler = initEstadoTramiteHandler; 