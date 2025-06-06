@props(['title' => 'Detalles del Trámite'])

<div class="space-y-8">
    <input type="hidden" name="action" value="next">


    <!-- Dirección -->
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">Dirección</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Código Postal -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Código Postal
                </label>
                <div class="relative">
                    <input type="text" 
                           id="codigo_postal" 
                           name="codigo_postal" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: 12345" 
                           required 
                           pattern="[0-9]{4,5}" 
                           maxlength="5">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Estado
                </label>
                <div class="relative">
                    <input type="text" 
                           id="estado" 
                           name="estado" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: Jalisco" 
                           readonly 
                           required>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Municipio -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Municipio
                </label>
                <div class="relative">
                    <input type="text" 
                           id="municipio" 
                           name="municipio" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: Guadalajara" 
                           readonly 
                           required>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Colonia -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Asentamiento
                </label>
                <div class="relative">
                    <select id="colonia" 
                            name="colonia" 
                            class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                            required>
                        <option value="">Seleccione un Asentamiento</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Calle -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Calle
                </label>
                <div class="relative">
                    <input type="text" 
                           id="calle" 
                           name="calle" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: Av. Principal" 
                           required 
                           maxlength="100">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Número Exterior -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Número Exterior
                </label>
                <div class="relative">
                    <input type="text" 
                           id="numero_exterior" 
                           name="numero_exterior" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: 123 o S/N" 
                           required 
                           maxlength="10" 
                           pattern="[A-Za-z0-9\/]+">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Número Interior -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Número Interior
                </label>
                <div class="relative">
                    <input type="text" 
                           id="numero_interior" 
                           name="numero_interior" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: 5A" 
                           maxlength="10" 
                           pattern="[A-Za-z0-9]+">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Entre Calle 1 -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Entre Calle
                </label>
                <div class="relative">
                    <input type="text" 
                           id="entre_calle_1" 
                           name="entre_calle_1" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: Calle Independencia" 
                           required 
                           maxlength="100" 
                           pattern="[A-Za-z0-9\s]+">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Entre Calle 2 -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Y Calle
                </label>
                <div class="relative">
                    <input type="text" 
                           id="entre_calle_2" 
                           name="entre_calle_2" 
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/10 transition-all duration-300"
                           placeholder="Ej: Calle Morelos" 
                           required 
                           maxlength="100" 
                           pattern="[A-Za-z0-9\s]+">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 