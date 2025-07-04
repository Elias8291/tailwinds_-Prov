@props(['documentName' => '', 'documentVersion' => 'v1'])

<div class="w-80 border-l border-gray-200 bg-gray-50 flex flex-col">
    <!-- Header del panel de revisión -->
    <div class="p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-lg font-semibold text-gray-900">Revisión de Documento</h4>
                <p class="text-sm text-gray-500">Evalúe y comente este documento</p>
            </div>
        </div>
    </div>
    
    <!-- Información del documento -->
    <x-revision.document-info :name="$documentName" :version="$documentVersion" />
    
    <!-- Controles de revisión -->
    <div class="flex-1 p-4 space-y-4 overflow-y-auto">
        <x-revision.review-comments />
        <x-revision.review-status />
        <x-revision.priority-selector />
        <x-revision.revision-history />
    </div>
    
    <!-- Botones de acción -->
    <x-revision.action-buttons />
</div> 