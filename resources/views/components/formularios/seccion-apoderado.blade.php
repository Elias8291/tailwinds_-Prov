@props(['title' => 'Apoderado Legal'])

<div class="space-y-8">
    <input type="hidden" name="action" value="next">
    <input type="hidden" name="seccion" value="5">

    <!-- Apoderado Legal -->
    <div class="space-y-6">
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8 border border-gray-200">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 text-[#9D2449] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800">Apoderado Legal</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre Completo
                    </label>
                    <input type="text" name="apoderado_nombre" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Nombre completo">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        RFC
                    </label>
                    <input type="text" name="apoderado_rfc" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="RFC">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        CURP
                    </label>
                    <input type="text" name="apoderado_curp" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="CURP">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono
                    </label>
                    <input type="tel" name="apoderado_telefono" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="(123) 456-7890">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input type="email" name="apoderado_email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="correo@ejemplo.com">
                </div>
            </div>
        </div>

        <!-- Poder Notarial -->
        <div class="space-y-4">
            <h4 class="text-base font-medium text-gray-800">Poder Notarial</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Número de Escritura -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Escritura
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="numero_escritura_poder" 
                               name="numero_escritura_poder" 
                               class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                               placeholder="Ej: 1234 o 1234/2024" 
                               maxlength="15"
                               required>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Fecha del Poder -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha del Poder
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="fecha_poder" 
                               name="fecha_poder" 
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
        </div>
    </div>
</div> 