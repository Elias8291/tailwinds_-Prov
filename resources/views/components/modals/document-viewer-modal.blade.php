@props(['documentName' => '', 'documentVersion' => 'v1'])

<div id="documento-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalDocumento()"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-7xl max-h-[95vh] overflow-hidden">
            <!-- Header del modal -->
            <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-[#B4325E] to-[#93264B]">
                <h3 class="text-xl font-semibold text-white" id="documento-modal-title">
                    Visualización de Documento
                </h3>
                <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="cerrarModalDocumento()">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Contenido principal del modal -->
            <div class="flex h-[85vh]">
                <!-- Panel izquierdo - Documento -->
                <div class="flex-1 flex flex-col">
                    <div class="flex-1 overflow-hidden">
                        <iframe id="documento-iframe" class="w-full h-full" frameborder="0"></iframe>
                    </div>
                </div>
                
                <!-- Panel derecho - Controles de revisión -->
                <x-revision.document-review-panel :documentName="$documentName" :documentVersion="$documentVersion" />
            </div>
        </div>
    </div>
</div> 