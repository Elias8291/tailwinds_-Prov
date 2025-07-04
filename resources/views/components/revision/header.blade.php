@props(['tramiteId', 'tipoTramite', 'rfc'])

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
    <!-- Información del Trámite -->
    <div class="flex items-center space-x-6">
        <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
            <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                Revisión de Trámite
            </h1>
            <p class="text-sm text-gray-500 mt-1">Proceso de validación y verificación de documentos</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mt-3">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] rounded-full"></div>
                    <span class="font-medium">ID: <span class="text-[#B4325E] font-mono font-semibold">#{{ $tramiteId }}</span></span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                    <span class="font-medium">{{ $tipoTramite }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                    <span class="font-medium">RFC: <span class="font-mono font-semibold text-gray-700">{{ $rfc }}</span></span>
                </div>
            </div>
        </div>
    </div>
</div> 