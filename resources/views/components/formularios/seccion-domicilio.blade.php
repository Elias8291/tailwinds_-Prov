@props(['title' => 'Domicilio'])

<div class="space-y-8">
    <input type="hidden" name="action" value="next">
    <input type="hidden" name="seccion" value="2">

    <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="flex items-center mb-6">
            <svg class="w-6 h-6 text-[#9D2449] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800">Dirección</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Calle
                </label>
                <input type="text" name="calle" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Número Exterior
                </label>
                <input type="text" name="numero_exterior" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Número Interior
                </label>
                <input type="text" name="numero_interior" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Colonia
                </label>
                <input type="text" name="colonia" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Código Postal
                </label>
                <input type="text" name="codigo_postal" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Estado
                </label>
                <select name="estado" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
                    <option value="">Seleccione un estado</option>
                    @foreach(config('estados.mexico') as $estado)
                        <option value="{{ $estado }}">{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div> 