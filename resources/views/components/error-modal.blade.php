<!-- Modal de Error -->
<div id="errorModal" class="modal fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-30 transition-opacity" aria-hidden="true"></div>
        
        <div class="modal-content relative bg-white rounded-xl max-w-lg w-full mx-4">
            <div class="bg-red-50 px-6 py-4 rounded-t-xl border-b border-red-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-red-800">Error</h3>
                    <button type="button" onclick="closeErrorModal()" class="text-red-600 hover:text-red-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-700" id="errorMessage"></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl">
                <button type="button" 
                        onclick="closeErrorModal()" 
                        class="w-full inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div> 