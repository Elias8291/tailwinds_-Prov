<!-- Resumen del Proveedor -->
<div id="resumenProveedor" class="hidden mt-6">
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/80 overflow-hidden">
        <div class="relative">
            <!-- Fondo decorativo -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#9d2449]/5 to-[#7a1d37]/5"></div>
            
            <!-- Contenido -->
            <div class="relative p-6">
                <div class="flex items-start gap-4">
                    <!-- Ícono del estado -->
                    <div id="estadoIcono" class="flex-shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>

                    <!-- Información principal -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 id="nombreRazonSocial" class="text-xl font-bold text-gray-900 truncate"></h3>
                                <div class="mt-1 flex items-center gap-3">
                                    <span id="rfcProveedor" class="text-sm text-gray-500"></span>
                                    <span id="tipoPersona" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"></span>
                                </div>
                            </div>
                            <div id="estadoBadge" class="flex-shrink-0">
                                <!-- El badge de estado se insertará aquí -->
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="mt-4 flex items-center gap-3">
                            <button onclick="window.historialHandler.buscarHistorial(document.getElementById('rfcProveedor').textContent.replace('RFC: ', ''))" 
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-[#9d2449] to-[#7a1d37] hover:from-[#7a1d37] hover:to-[#9d2449] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ver Historial
                            </button>

                            <button id="revisarDatosBtn"
                                    onclick="revisarDatosTramite()"
                                    class="hidden inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-blue-600 bg-white border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Revisar Datos
                            </button>
                            
                            <button id="verDatosSATBtn"
                                    onclick="window.satHandler?.showModal()"
                                    class="hidden inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-[#9d2449] bg-white border-2 border-[#9d2449] hover:bg-[#9d2449]/5 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95 gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Ver Datos SAT
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variable global para almacenar el ID del trámite activo para revisión
window.tramiteActivoRevision = null;

window.actualizarResumenProveedor = function(data, mostrarBotonSAT = false, tramiteId = null) {
    const resumen = document.getElementById('resumenProveedor');
    const nombreRazonSocial = document.getElementById('nombreRazonSocial');
    const rfcProveedor = document.getElementById('rfcProveedor');
    const tipoPersona = document.getElementById('tipoPersona');
    const estadoIcono = document.getElementById('estadoIcono');
    const estadoBadge = document.getElementById('estadoBadge');
    const verDatosSATBtn = document.getElementById('verDatosSATBtn');
    const revisarDatosBtn = document.getElementById('revisarDatosBtn');

    if (!data || !resumen) return;

    // Almacenar el ID del trámite para revisión
    window.tramiteActivoRevision = tramiteId;

    // Mostrar el contenedor
    resumen.classList.remove('hidden');

    // Actualizar información
    nombreRazonSocial.textContent = data.tipoPersona === 'Moral' ? data.razonSocial : data.nombre;
    rfcProveedor.textContent = `RFC: ${data.rfc}`;
    tipoPersona.textContent = `Persona ${data.tipoPersona}`;

    // Configurar estado
    const estados = {
        'Activo': {
            bgColor: 'bg-green-500',
            textColor: 'text-green-700',
            bgLight: 'bg-green-100',
            label: 'Activo'
        },
        'Inactivo': {
            bgColor: 'bg-red-500',
            textColor: 'text-red-700',
            bgLight: 'bg-red-100',
            label: 'Inactivo'
        },
        'Pendiente Renovacion': {
            bgColor: 'bg-yellow-500',
            textColor: 'text-yellow-700',
            bgLight: 'bg-yellow-100',
            label: 'Pendiente Renovación'
        }
    };

    const estado = estados[data.estado] || estados['Inactivo'];
    
    // Actualizar ícono de estado
    estadoIcono.className = `flex-shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg ${estado.bgColor}`;
    
    // Actualizar badge de estado
    estadoBadge.innerHTML = `
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${estado.bgLight} ${estado.textColor}">
            ${estado.label}
        </span>
    `;

    // Mostrar/ocultar botón de datos SAT
    if (mostrarBotonSAT) {
        verDatosSATBtn.classList.remove('hidden');
    } else {
        verDatosSATBtn.classList.add('hidden');
    }

    // Mostrar/ocultar botón de revisar datos si hay un trámite activo
    if (tramiteId) {
        revisarDatosBtn.classList.remove('hidden');
    } else {
        revisarDatosBtn.classList.add('hidden');
    }
};

// Función para revisar datos del trámite
window.revisarDatosTramite = function() {
    if (!window.tramiteActivoRevision) {
        alert('No hay un trámite seleccionado para revisar');
        return;
    }
    
    // Redirigir a la página de revisión de datos generales
    window.location.href = `/datos-generales/revision/${window.tramiteActivoRevision}`;
};
</script> 