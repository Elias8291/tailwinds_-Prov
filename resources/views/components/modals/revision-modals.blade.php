<!-- Modal para aprobar todo -->
<div id="modal-aprobar-todo" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 rounded-full p-2 mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Aprobar Todo</h3>
                </div>
                
                <p class="text-gray-600 mb-6">
                    ¿Estás seguro de que deseas aprobar todas las secciones del trámite?
                </p>
                
                <div class="flex space-x-3 justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors" onclick="cerrarModal('modal-aprobar-todo')">
                        Cancelar
                    </button>
                    <button type="button" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors" onclick="confirmarAprobarTodo()">
                        Aprobar Todo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para rechazar todo -->
<div id="modal-rechazar-todo" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-full p-2 mr-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Rechazar Todo</h3>
                </div>
                
                <p class="text-gray-600 mb-4">
                    ¿Estás seguro de que deseas rechazar todas las secciones del trámite?
                </p>
                
                <div class="mb-4">
                    <label for="comentario-rechazo-todo" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo del rechazo (requerido)
                    </label>
                    <textarea id="comentario-rechazo-todo" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Especifica el motivo del rechazo..."></textarea>
                </div>
                
                <div class="flex space-x-3 justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors" onclick="cerrarModal('modal-rechazar-todo')">
                        Cancelar
                    </button>
                    <button type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" onclick="confirmarRechazarTodo()">
                        Rechazar Todo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para pausar revisión -->
<div id="modal-pausar" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 rounded-full p-2 mr-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Pausar Revisión</h3>
                </div>
                
                <p class="text-gray-600 mb-4">
                    ¿Deseas pausar la revisión de este trámite? Podrás continuar más tarde.
                </p>
                
                <div class="mb-4">
                    <label for="comentario-pausa" class="block text-sm font-medium text-gray-700 mb-2">
                        Notas de pausa (opcional)
                    </label>
                    <textarea id="comentario-pausa" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Agrega notas sobre el estado actual..."></textarea>
                </div>
                
                <div class="flex space-x-3 justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors" onclick="cerrarModal('modal-pausar')">
                        Cancelar
                    </button>
                    <button type="button" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors" onclick="confirmarPausar()">
                        Pausar Revisión
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para manejar los modales
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

function abrirModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function confirmarAprobarTodo() {
    if (window.revisionManager) {
        // Implementar lógica para aprobar todas las secciones
        console.log('Aprobando todas las secciones...');
        cerrarModal('modal-aprobar-todo');
        window.revisionManager.showNotification('Funcionalidad en desarrollo', 'info');
    }
}

function confirmarRechazarTodo() {
    const comentario = document.getElementById('comentario-rechazo-todo').value;
    if (!comentario.trim()) {
        alert('Debe proporcionar un motivo para el rechazo');
        return;
    }
    
    if (window.revisionManager) {
        // Implementar lógica para rechazar todas las secciones
        console.log('Rechazando todas las secciones...', comentario);
        cerrarModal('modal-rechazar-todo');
        window.revisionManager.showNotification('Funcionalidad en desarrollo', 'info');
    }
}

function confirmarPausar() {
    const comentario = document.getElementById('comentario-pausa').value;
    
    if (window.revisionManager) {
        // Implementar lógica para pausar la revisión
        console.log('Pausando revisión...', comentario);
        cerrarModal('modal-pausar');
        window.revisionManager.showNotification('Funcionalidad en desarrollo', 'info');
    }
}

// Cerrar modales al hacer clic fuera de ellos
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-gray-500')) {
        const modals = ['modal-aprobar-todo', 'modal-rechazar-todo', 'modal-pausar'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                cerrarModal(modalId);
            }
        });
    }
});
</script> 