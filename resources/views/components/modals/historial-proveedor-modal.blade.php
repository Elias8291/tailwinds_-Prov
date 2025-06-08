<!-- Modal de Historial de Proveedor -->
<div id="historialProveedorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <!-- Modal header -->
        <div class="px-6 py-4 bg-gradient-to-br from-[#9d2449] to-[#7a1d37] border-b border-[#9d2449]/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/10 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white">Historial del Proveedor</h3>
                </div>
                <button type="button" onclick="closeHistorialModal()" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal body -->
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 150px);">
            <div id="historialProveedorContent" class="space-y-6">
                <!-- El contenido se insertará dinámicamente -->
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="space-y-3 mt-4">
                        <div class="h-4 bg-gray-200 rounded"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="flex justify-end">
                <button type="button" onclick="closeHistorialModal()" 
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
window.openHistorialModal = function() {
    const modal = document.getElementById('historialProveedorModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
};

window.closeHistorialModal = function() {
    const modal = document.getElementById('historialProveedorModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
};

// Cerrar modal al hacer clic fuera
document.getElementById('historialProveedorModal')?.addEventListener('click', function(event) {
    const modalContent = this.querySelector('.bg-white');
    if (event.target === this && !modalContent.contains(event.target)) {
        window.closeHistorialModal();
    }
});
</script> 