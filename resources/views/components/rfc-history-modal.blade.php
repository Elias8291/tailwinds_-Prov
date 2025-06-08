<!-- RFC History Modal -->
<div id="rfcHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
        <!-- Modal header -->
        <div class="px-6 py-4 bg-gradient-to-br from-[#B4325E] to-[#93264B] border-b border-[#B4325E]/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/10 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Historial del RFC</h3>
                </div>
                <button onclick="closeRfcHistoryModal()" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal body -->
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 150px);">
            <div id="rfcHistoryContent" class="space-y-6">
                <ol class="relative border-s-2 border-[#B4325E]/20">
                    <!-- El contenido del historial se insertará aquí dinámicamente -->
                </ol>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button onclick="closeRfcHistoryModal()" 
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 font-medium rounded-lg border border-gray-300 transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function calcularTiempoRestante(fechaVencimiento) {
    if (!fechaVencimiento) return null;

    const ahora = new Date();
    const vencimiento = new Date(fechaVencimiento);
    const diferencia = vencimiento - ahora;

    // Si ya venció
    if (diferencia < 0) {
        const tiempoVencido = Math.abs(diferencia);
        const diasVencidos = Math.floor(tiempoVencido / (1000 * 60 * 60 * 24));
        return {
            texto: `Vencido hace ${diasVencidos} días`,
            clase: 'text-red-600',
            vencido: true
        };
    }

    // Calcular todas las unidades de tiempo
    const milisegundosEnHora = 1000 * 60 * 60;
    const milisegundosEnDia = milisegundosEnHora * 24;
    const milisegundosEnMes = milisegundosEnDia * 30.44; // Promedio de días por mes

    const mesesTotales = Math.floor(diferencia / milisegundosEnMes);
    const restoDespuesMeses = diferencia % milisegundosEnMes;
    
    const diasTotales = Math.floor(restoDespuesMeses / milisegundosEnDia);
    const restoDespuesDias = restoDespuesMeses % milisegundosEnDia;
    
    const horasTotales = Math.floor(restoDespuesDias / milisegundosEnHora);

    // Determinar clase de color
    let clase = '';
    if (mesesTotales > 3) {
        clase = 'text-green-600';
    } else if (mesesTotales > 0 || diasTotales > 15) {
        clase = 'text-yellow-600';
    } else {
        clase = 'text-red-600';
    }

    // Construir mensaje
    const partesMensaje = [];
    
    // Siempre incluir los tres valores
    partesMensaje.push(`${mesesTotales} ${mesesTotales === 1 ? 'mes' : 'meses'}`);
    partesMensaje.push(`${diasTotales} ${diasTotales === 1 ? 'día' : 'días'}`);
    partesMensaje.push(`${horasTotales} ${horasTotales === 1 ? 'hora' : 'horas'}`);

    return {
        texto: `Tiempo para vencer: ${partesMensaje.join(', ')}`,
        clase,
        vencido: false,
        detalle: `Fecha de vencimiento: ${vencimiento.toLocaleDateString('es-MX', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })} a las ${vencimiento.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
        })}`
    };
}

async function showRfcHistoryModal(rfc) {
    try {
        const response = await fetch(`/api/rfc-history/${rfc}`);
        if (!response.ok) throw new Error('Error al obtener el historial');
        
        const data = await response.json();
        const historyContent = document.getElementById('rfcHistoryContent');
        const modal = document.getElementById('rfcHistoryModal');
        
        if (historyContent && modal && data.history) {
            historyContent.querySelector('ol').innerHTML = data.history.map((item) => {
                const fechaRegistro = item.date ? new Date(item.date) : null;
                const fechaVencimiento = item.expiration_date ? new Date(item.expiration_date) : null;
                const tiempoRestante = calcularTiempoRestante(item.expiration_date);
                const estadoInfo = determinarEstadoYClases(item.status || (tiempoRestante?.vencido ? 'Inactivo' : 'Activo'));

                return `
                    <li class="mb-10 ms-6">
                        <div class="absolute w-4 h-4 ${estadoInfo.dot} rounded-full mt-1.5 -start-2 border-2 border-white"></div>
                        <div class="p-4 bg-white rounded-lg border ${estadoInfo.border} shadow-sm hover:shadow-md transition-all duration-300 ${estadoInfo.hover}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <time class="text-sm font-normal text-gray-400">
                                        Registro: ${fechaRegistro ? fechaRegistro.toLocaleDateString('es-MX', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        }) : 'No especificada'}
                                    </time>
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full ${estadoInfo.badge}">
                                        ${item.status || 'Estado no disponible'}
                                    </span>
                                </div>
                                <div class="text-sm font-medium ${estadoInfo.textColor}">
                                    PV: ${item.pv || 'No asignado'}
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        ${item.title || 'Título no disponible'}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        ${item.description || 'Sin descripción disponible'}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">Estado de Vencimiento</p>
                                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full ${tiempoRestante?.clase || 'bg-gray-100 text-gray-800'}">
                                                ${tiempoRestante?.vencido ? 'Vencido' : 'En Vigencia'}
                                            </span>
                                        </div>
                                        ${tiempoRestante ? `
                                            <p class="text-sm ${tiempoRestante.clase} font-medium mt-2">
                                                ${tiempoRestante.texto}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                ${tiempoRestante.detalle}
                                            </p>
                                        ` : ''}
                                    </div>
                                    <div class="flex justify-end pt-2 border-t border-gray-200">
                                        <button onclick="showRfcDetails('${item.pv}')" 
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E]">
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Actualizar tiempos cada minuto
            const intervalId = setInterval(() => {
                const items = historyContent.querySelectorAll('li');
                items.forEach(item => {
                    const fechaVencimientoElement = item.querySelector('[data-fecha-vencimiento]');
                    if (fechaVencimientoElement) {
                        const fechaVencimiento = fechaVencimientoElement.dataset.fechaVencimiento;
                        const tiempoRestante = calcularTiempoRestante(fechaVencimiento);
                        if (tiempoRestante) {
                            const tiempoElement = item.querySelector('.tiempo-restante');
                            if (tiempoElement) {
                                tiempoElement.textContent = tiempoRestante.texto;
                                tiempoElement.className = `text-sm ${tiempoRestante.clase} font-medium tiempo-restante`;
                            }
                            const detalleElement = item.querySelector('.tiempo-detalle');
                            if (detalleElement) {
                                detalleElement.textContent = tiempoRestante.detalle;
                            }
                        }
                    }
                });
            }, 60000);

            modal.addEventListener('hidden.modal', () => clearInterval(intervalId));
        }
    } catch (error) {
        console.error('Error al mostrar el historial:', error);
        showError('Error al cargar el historial del RFC');
    }
}

function determinarEstadoYClases(estado) {
    const clases = {
        'Activo': {
            dot: 'bg-green-500',
            badge: 'bg-green-100 text-green-800',
            border: 'border-green-200',
            hover: 'hover:border-green-300',
            textColor: 'text-green-600'
        },
        'Pendiente Renovacion': {
            dot: 'bg-yellow-500',
            badge: 'bg-yellow-100 text-yellow-800',
            border: 'border-yellow-200',
            hover: 'hover:border-yellow-300',
            textColor: 'text-yellow-600'
        },
        'Inactivo': {
            dot: 'bg-red-500',
            badge: 'bg-red-100 text-red-800',
            border: 'border-red-200',
            hover: 'hover:border-red-300',
            textColor: 'text-red-600'
        }
    };

    return clases[estado] || clases['Inactivo'];
}

// Función para mostrar detalles del PV
function showRfcDetails(pv) {
    // Aquí puedes implementar la lógica para mostrar más detalles del PV
    console.log('Mostrar detalles del PV:', pv);
}

function closeRfcHistoryModal() {
    const modal = document.getElementById('rfcHistoryModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(event) {
    const modal = document.getElementById('rfcHistoryModal');
    const modalContent = modal?.querySelector('.bg-white');
    if (modal && event.target === modal && modalContent && !modalContent.contains(event.target)) {
        closeRfcHistoryModal();
    }
});
</script> 