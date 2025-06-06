@props(['title' => 'Datos Generales'])

<div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8 border border-gray-200">
    <div class="flex items-center mb-6">
        <svg class="w-6 h-6 text-[#9D2449] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800">Datos Generales</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tipo de Persona
            </label>
            <select name="tipo_persona" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
                <option value="">Seleccione tipo</option>
                <option value="fisica">Física</option>
                <option value="moral">Moral</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                RFC
            </label>
            <input type="text" name="rfc" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Ej: XAXX010101000">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Razón Social
            </label>
            <input type="text" name="razon_social" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
        </div>
    </div>
</div> 