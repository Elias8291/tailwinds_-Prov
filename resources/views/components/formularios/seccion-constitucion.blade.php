@props(['title' => 'Datos de Constitución'])

<div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8 border border-gray-200">
    <div class="flex items-center mb-6">
        <svg class="w-6 h-6 text-[#9D2449] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800">Datos Notariales</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Número de Escritura
            </label>
            <input type="text" name="numero_escritura" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Ej: 1234">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Fecha de Constitución
            </label>
            <input type="date" name="fecha_constitucion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nombre del Notario
            </label>
            <input type="text" name="nombre_notario" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Ej: Lic. Juan Pérez">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Número de Notario
            </label>
            <input type="text" name="numero_notario" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Ej: 123">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Entidad Federativa
            </label>
            <select name="entidad_federativa" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
                <option value="">Seleccione un estado</option>
                @foreach(config('estados.mexico') as $estado)
                    <option value="{{ $estado }}">{{ $estado }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Fecha de Inscripción
            </label>
            <input type="date" name="fecha_inscripcion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200">
        </div>
    </div>
</div> 