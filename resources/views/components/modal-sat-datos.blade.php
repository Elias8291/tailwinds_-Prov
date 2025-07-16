<div id="satDataModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden border border-gray-200">
        <!-- Modal header -->
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-800 to-blue-900">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-1.5 bg-blue-700 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Datos del SAT</h3>
                </div>
                <button onclick="closeSatModal()" class="text-blue-100 hover:text-white transition-colors duration-200 rounded-full p-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Modal body -->
        <div class="p-5 overflow-y-auto" style="max-height: calc(90vh - 120px);">
            <div id="satDataContent" class="space-y-4">
                <!-- Los datos del SAT se insertarán aquí -->
            </div>
        </div>
        <!-- Modal footer -->
        <div class="bg-gray-50 px-5 py-3 border-t border-gray-200">
            <div class="flex justify-end">
                <button onclick="closeSatModal()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-700 text-white hover:bg-blue-800 font-medium rounded-lg border border-blue-700 transition-colors duration-200 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div> 