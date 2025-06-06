@props(['title' => 'Documentos'])

<div class="space-y-8">
    <input type="hidden" name="action" value="next">
    <input type="hidden" name="seccion" value="6">

    <!-- Datos Notariales -->
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">Datos Notariales</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Número de Escritura -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Escritura
                </label>
                <div class="relative">
                    <input type="text" 
                           id="numero_escritura" 
                           name="numero_escritura" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: 1234 o 1234/2024" 
                           maxlength="15">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Nombre del Notario -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Notario
                </label>
                <div class="relative">
                    <input type="text" 
                           id="nombre_notario" 
                           name="nombre_notario" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: Lic. Juan Pérez González" 
                           maxlength="100">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Entidad Federativa -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Entidad Federativa
                </label>
                <div class="relative">
                    <select id="entidad_federativa" 
                            name="entidad_federativa" 
                            class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                            required>
                        <option value="">Seleccione un estado</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Fecha de Constitución -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Constitución
                </label>
                <div class="relative">
                    <input type="date" 
                           id="fecha_constitucion" 
                           name="fecha_constitucion" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           required>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Número de Notario -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Notario
                </label>
                <div class="relative">
                    <input type="text" 
                           id="numero_notario" 
                           name="numero_notario" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: 123" 
                           maxlength="10">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Número de Registro -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Registro
                </label>
                <div class="relative">
                    <input type="text" 
                           id="numero_registro" 
                           name="numero_registro" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: 0123456789 o FME123456789" 
                           maxlength="14">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fecha de Inscripción -->
        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Fecha de Inscripción
            </label>
            <div class="relative">
                <input type="date" 
                       id="fecha_inscripcion" 
                       name="fecha_inscripcion" 
                       class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                       required>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos -->
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">Documentos Requeridos</h3>
        
        <div class="space-y-6">
            <!-- Acta Constitutiva -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8 border border-gray-200">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-[#9D2449] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Acta Constitutiva</h3>
                </div>

                <div class="space-y-6">
                    <!-- Acta Constitutiva -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
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
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9D2449] cursor-pointer">
                                Seleccionar archivo
                            </label>
                        </div>
                        <div id="preview_acta_constitutiva" class="hidden">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile('acta_constitutiva')">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Poder Notarial -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
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
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9D2449] cursor-pointer">
                                Seleccionar archivo
                            </label>
                        </div>
                        <div id="preview_poder_notarial" class="hidden">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile('poder_notarial')">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identificación Oficial -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Identificación Oficial del Apoderado Legal
                </label>
                <div class="relative">
                    <input type="file" 
                           id="identificacion_oficial" 
                           name="identificacion_oficial" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           accept=".pdf"
                           required>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Archivo PDF, máximo 10MB</p>
            </div>

            <!-- Comprobante de Domicilio -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Comprobante de Domicilio
                </label>
                <div class="relative">
                    <input type="file" 
                           id="comprobante_domicilio" 
                           name="comprobante_domicilio" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           accept=".pdf"
                           required>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Archivo PDF, máximo 10MB</p>
            </div>

            <!-- Constancia de Situación Fiscal -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Constancia de Situación Fiscal
                </label>
                <div class="relative">
                    <input type="file" 
                           id="constancia_fiscal" 
                           name="constancia_fiscal" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           accept=".pdf"
                           required>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Archivo PDF, máximo 10MB</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar cambios en los archivos
    ['acta_constitutiva', 'poder_notarial'].forEach(function(inputId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById('preview_' + inputId);
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                preview.querySelector('.file-name').textContent = file.name;
                preview.classList.remove('hidden');
            }
        });
    });
});

function removeFile(inputId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById('preview_' + inputId);
    
    input.value = '';
    preview.classList.add('hidden');
}
</script>
@endpush 