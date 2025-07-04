@props(['seccionId', 'estado' => null, 'comentario' => null])

<div class="mt-6 border-t border-gray-100 pt-6">
    <div class="space-y-4">
        <!-- Estado actual de la revisión -->
        @if($estado)
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-700">Estado actual:</span>
            <span class="px-3 py-1 text-sm rounded-full 
                @if($estado === 'aprobado') bg-green-100 text-green-800
                @elseif($estado === 'rechazado') bg-red-100 text-red-800
                @else bg-yellow-100 text-yellow-800 @endif">
                {{ ucfirst($estado) }}
            </span>
        </div>
        @endif

        <!-- Área de comentarios -->
        <div class="space-y-2">
            <label for="comentario_{{ $seccionId }}" class="block text-sm font-medium text-gray-700">
                Comentarios de revisión
            </label>
            <textarea 
                id="comentario_{{ $seccionId }}"
                name="comentario"
                rows="3"
                class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 focus:border-[#9d2449] focus:ring-2 focus:ring-[#9d2449]/20 transition-all"
                placeholder="Ingrese sus comentarios sobre esta sección...">{{ $comentario }}</textarea>
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Botón Aprobar -->
            <button 
                type="button"
                onclick="aprobarSeccion('{{ $seccionId }}')"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-check mr-2"></i>
                Aprobar
            </button>

            <!-- Botón Rechazar -->
            <button 
                type="button"
                onclick="rechazarSeccion('{{ $seccionId }}')"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Rechazar
            </button>

            <!-- Indicador de guardado -->
            <div id="indicador_guardado_{{ $seccionId }}" class="hidden items-center text-sm text-gray-500">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Guardando...
            </div>
        </div>
    </div>
</div>

<script>
function aprobarSeccion(seccionId) {
    procesarAccionSeccion(seccionId, 'aprobar');
}

function rechazarSeccion(seccionId) {
    procesarAccionSeccion(seccionId, 'rechazar');
}

function procesarAccionSeccion(seccionId, accion) {
    // Mostrar indicador de guardado
    const indicador = document.getElementById(`indicador_guardado_${seccionId}`);
    indicador.classList.remove('hidden');
    indicador.classList.add('flex');

    // Obtener el comentario
    const comentario = document.getElementById(`comentario_${seccionId}`).value;

    // Preparar los datos
    const data = {
        comentario: comentario,
        _token: '{{ csrf_token() }}'
    };

    // Realizar la petición
    fetch(`/revision/tramite/{{ request()->route('tramite') }}/${accion}-seccion/${seccionId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación de éxito
            mostrarNotificacion('Sección actualizada correctamente', 'success');
            
            // Actualizar la UI si es necesario
            actualizarEstadoSeccion(seccionId, accion === 'aprobar' ? 'aprobado' : 'rechazado');
        } else {
            throw new Error(data.message || 'Error al procesar la acción');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al procesar la acción: ' + error.message, 'error');
    })
    .finally(() => {
        // Ocultar indicador de guardado
        indicador.classList.add('hidden');
        indicador.classList.remove('flex');
    });
}

function actualizarEstadoSeccion(seccionId, nuevoEstado) {
    // Actualizar visualmente el estado de la sección
    const estadoElement = document.querySelector(`#estado_seccion_${seccionId}`);
    if (estadoElement) {
        estadoElement.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
        
        // Actualizar clases de estado
        estadoElement.className = `px-3 py-1 text-sm rounded-full ${
            nuevoEstado === 'aprobado' 
                ? 'bg-green-100 text-green-800'
                : nuevoEstado === 'rechazado'
                    ? 'bg-red-100 text-red-800'
                    : 'bg-yellow-100 text-yellow-800'
        }`;
    }
}

function mostrarNotificacion(mensaje, tipo) {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        tipo === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
    }`;
    
    notificacion.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${mensaje}</span>
        </div>
    `;
    
    // Agregar al DOM
    document.body.appendChild(notificacion);
    
    // Animar entrada
    setTimeout(() => {
        notificacion.classList.add('translate-y-0');
        notificacion.classList.remove('translate-y-[-100%]');
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notificacion.classList.add('translate-y-[-100%]', 'opacity-0');
        setTimeout(() => {
            notificacion.remove();
        }, 300);
    }, 3000);
}
</script> 