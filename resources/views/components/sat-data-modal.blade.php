<!-- Modal para mostrar datos del SAT -->
<div id="satDataModal" class="modal fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-30 transition-opacity" aria-hidden="true"></div>
        
        <div class="modal-content relative bg-white rounded-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-[#B4325E] to-[#93264B] px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white" id="modal-title">
                        Datos de la Constancia
                    </h3>
                    <button type="button" onclick="closeSatDataModal()" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6" id="satDataContent">
                <!-- El contenido se llenará dinámicamente -->
            </div>
        </div>
    </div>
</div> 