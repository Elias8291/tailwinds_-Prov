@props(['title' => 'Información Personal'])

<div class="space-y-8">
    <!-- Constancia de Situación Fiscal -->
    <div class="w-full bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-lg bg-[#B4325E]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-grow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Constancia de Situación Fiscal</h3>
                    <p class="text-sm text-gray-500 mb-4">Sube tu Constancia de Situación Fiscal en formato PDF</p>
                    <div class="relative">
                        <input type="file" 
                               id="constancia_upload" 
                               name="constancia_upload" 
                               class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#B4325E] file:text-white hover:file:bg-[#93264B]"
                               accept="application/pdf">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Básica -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Tipo de Persona -->
        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tipo de Persona
            </label>
            <div class="relative">
                <select name="tipo_persona" 
                        id="tipo_persona" 
                        class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300">
                    <option value="">Seleccione un tipo</option>
                    <option value="Física">Física</option>
                    <option value="Moral">Moral</option>
                </select>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- RFC -->
        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                RFC
            </label>
            <div class="relative">
                <input type="text" 
                       name="rfc" 
                       id="rfc" 
                       class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                       placeholder="Ej. XAXX010101000" 
                       maxlength="13" 
                       pattern="[A-Z0-9]{12,13}">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Razón Social -->
    <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Razón Social
        </label>
        <div class="relative">
            <input type="text" 
                   id="razon_social" 
                   name="razon_social" 
                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                   maxlength="100" 
                   pattern="[A-Za-z\s&.,0-9]+">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Correo Electrónico -->
    <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Correo Electrónico
        </label>
        <div class="relative">
            <input type="email" 
                   id="correo_electronico" 
                   name="correo_electronico" 
                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Objeto Social -->
    <div class="w-full">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Objeto Social
        </label>
        <div class="relative">
            <textarea id="objeto_social" 
                      name="objeto_social" 
                      class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                      maxlength="500"
                      rows="4"></textarea>
            <div class="absolute top-3 left-0 flex items-start pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Sectores y Actividad -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Sector
            </label>
            <div class="relative">
                <select name="sectores" 
                        id="sectores" 
                        class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300">
                    <option value="">Seleccione un sector</option>
                </select>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="w-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Actividad
            </label>
            <div class="relative">
                <select name="actividad" 
                        id="actividad" 
                        class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300">
                    <option value="">Seleccione una actividad</option>
                </select>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="actividades_seleccionadas" id="actividades_seleccionadas_input">

    <!-- Información de Contacto -->
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">Información de Contacto</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Nombre de Contacto -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre de Contacto
                </label>
                <div class="relative">
                    <input type="text" 
                           id="contacto_nombre" 
                           name="contacto_nombre" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           maxlength="40" 
                           pattern="[A-Za-z\s]+">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Cargo -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cargo
                </label>
                <div class="relative">
                    <input type="text" 
                           id="contacto_cargo" 
                           name="contacto_cargo" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           maxlength="50" 
                           pattern="[A-Za-z\s]+">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Correo de Contacto -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Correo de Contacto
                </label>
                <div class="relative">
                    <input type="email" 
                           id="contacto_correo" 
                           name="contacto_correo" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Teléfono de Contacto -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Teléfono de Contacto
                </label>
                <div class="relative">
                    <input type="tel" 
                           id="contacto_telefono" 
                           name="contacto_telefono" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           pattern="[0-9]{10}" 
                           maxlength="10" 
                           inputmode="numeric">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Teléfono Alternativo -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Teléfono Alternativo
                </label>
                <div class="relative">
                    <input type="tel" 
                           id="contacto_telefono_2" 
                           name="contacto_telefono_2" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           pattern="[0-9]{10}" 
                           maxlength="10" 
                           inputmode="numeric">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sitio Web -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sitio Web
                </label>
                <div class="relative">
                    <input type="url" 
                           id="contacto_web" 
                           name="contacto_web" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="https://www.ejemplo.com">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 