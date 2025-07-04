@props(['disabled' => false])

<div class="p-4 border-t border-gray-200 bg-white space-y-3">
    <!-- Botones principales -->
    <div class="flex space-x-2">
        <button id="btn-aprobar-documento" 
                onclick="aplicarRevisionDocumento('aprobado')"
                class="flex-1 flex items-center justify-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all"
                {{ $disabled ? 'disabled' : '' }}>
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Aprobar
        </button>
        <button id="btn-rechazar-documento" 
                onclick="aplicarRevisionDocumento('rechazado')"
                class="flex-1 flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all"
                {{ $disabled ? 'disabled' : '' }}>
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Rechazar
        </button>
    </div>
    
    <!-- Botón guardar comentario -->
    <button id="btn-guardar-comentario-doc" 
            onclick="guardarComentarioDocumento()"
            class="w-full flex items-center justify-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-all"
            {{ $disabled ? 'disabled' : '' }}>
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
        </svg>
        Guardar Solo Comentario
    </button>
    
    <!-- Botón solicitar corrección -->
    <button id="btn-correccion-documento" 
            onclick="aplicarRevisionDocumento('correccion')"
            class="w-full flex items-center justify-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-all"
            {{ $disabled ? 'disabled' : '' }}>
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Solicitar Corrección
    </button>
</div> 