@props(['title' => 'Documentos'])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
            <i class="fas fa-file-upload text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Documentos requeridos para el registro</p>
        </div>
    </div>

    <form class="space-y-8">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="6">

        <!-- Documentos Requeridos -->
        <div class="space-y-6">
            <!-- Acta Constitutiva -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-[#9d2449]/50 transition-colors duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-[#9d2449] text-2xl mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Acta Constitutiva</h4>
                            <p class="text-xs text-gray-500">PDF, máximo 10MB</p>
                        </div>
                    </div>
                    <input type="file" 
                           name="acta_constitutiva" 
                           accept=".pdf"
                           class="hidden" 
                           id="acta_constitutiva">
                    <label for="acta_constitutiva" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300">
                        Seleccionar archivo
                    </label>
                </div>
                <div id="preview_acta_constitutiva" class="hidden">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                            <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile('acta_constitutiva')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Poder Notarial -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-[#9d2449]/50 transition-colors duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-[#9d2449] text-2xl mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Poder Notarial</h4>
                            <p class="text-xs text-gray-500">PDF, máximo 10MB</p>
                        </div>
                    </div>
                    <input type="file" 
                           name="poder_notarial" 
                           accept=".pdf"
                           class="hidden" 
                           id="poder_notarial">
                    <label for="poder_notarial" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300">
                        Seleccionar archivo
                    </label>
                </div>
                <div id="preview_poder_notarial" class="hidden">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                            <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile('poder_notarial')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Identificación Oficial -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-[#9d2449]/50 transition-colors duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-id-card text-[#9d2449] text-2xl mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Identificación Oficial</h4>
                            <p class="text-xs text-gray-500">PDF, máximo 10MB</p>
                        </div>
                    </div>
                    <input type="file" 
                           name="identificacion_oficial" 
                           accept=".pdf"
                           class="hidden" 
                           id="identificacion_oficial">
                    <label for="identificacion_oficial" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300">
                        Seleccionar archivo
                    </label>
                </div>
                <div id="preview_identificacion_oficial" class="hidden">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                            <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile('identificacion_oficial')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Comprobante de Domicilio -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-[#9d2449]/50 transition-colors duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-home text-[#9d2449] text-2xl mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Comprobante de Domicilio</h4>
                            <p class="text-xs text-gray-500">PDF, máximo 10MB</p>
                        </div>
                    </div>
                    <input type="file" 
                           name="comprobante_domicilio" 
                           accept=".pdf"
                           class="hidden" 
                           id="comprobante_domicilio">
                    <label for="comprobante_domicilio" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300">
                        Seleccionar archivo
                    </label>
                </div>
                <div id="preview_comprobante_domicilio" class="hidden">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                            <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile('comprobante_domicilio')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = ['acta_constitutiva', 'poder_notarial', 'identificacion_oficial', 'comprobante_domicilio'];
    
    fileInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById('preview_' + inputId);
        
        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    preview.querySelector('.file-name').textContent = file.name;
                    preview.classList.remove('hidden');
                }
            });
        }
    });
});

function removeFile(inputId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById('preview_' + inputId);
    
    if (input && preview) {
        input.value = '';
        preview.classList.add('hidden');
    }
}
</script>
@endpush 