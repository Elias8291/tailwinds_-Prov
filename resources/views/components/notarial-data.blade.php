@props(['title' => 'Datos Notariales'])

<div class="card-custom rounded-xl p-6">
    <div class="flex items-center mb-6">
        <svg class="w-6 h-6 text-[#B4325E] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="3">

        <!-- Número de Escritura -->
        <div class="form-group">
            <label for="numero_escritura" class="block text-sm font-medium text-gray-700 mb-1">Número de Escritura</label>
            <input type="text" 
                   id="numero_escritura" 
                   name="numero_escritura" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: 1234 o 1234/2024" 
                   maxlength="15">
        </div>

        <!-- Nombre del Notario -->
        <div class="form-group md:col-span-2">
            <label for="nombre_notario" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Notario</label>
            <input type="text" 
                   id="nombre_notario" 
                   name="nombre_notario" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: Lic. Juan Pérez González" 
                   maxlength="100">
        </div>

        <!-- Entidad Federativa -->
        <div class="form-group">
            <label for="entidad_federativa" class="block text-sm font-medium text-gray-700 mb-1">Entidad Federativa</label>
            <select id="entidad_federativa" 
                    name="entidad_federativa" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                    required>
                <option value="">Seleccione un estado</option>
                @foreach(config('estados.mexico') as $estado)
                    <option value="{{ $estado }}">{{ $estado }}</option>
                @endforeach
            </select>
        </div>

        <!-- Fecha de Constitución -->
        <div class="form-group">
            <label for="fecha_constitucion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Constitución</label>
            <input type="date" 
                   id="fecha_constitucion" 
                   name="fecha_constitucion" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   required>
        </div>

        <!-- Número de Notario -->
        <div class="form-group">
            <label for="numero_notario" class="block text-sm font-medium text-gray-700 mb-1">Número de Notario</label>
            <input type="text" 
                   id="numero_notario" 
                   name="numero_notario" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: 123" 
                   maxlength="10">
        </div>

        <!-- Número de Registro -->
        <div class="form-group">
            <label for="numero_registro" class="block text-sm font-medium text-gray-700 mb-1">Número de Registro</label>
            <input type="text" 
                   id="numero_registro" 
                   name="numero_registro" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: 0123456789 o FME123456789" 
                   maxlength="14">
        </div>

        <!-- Fecha de Inscripción -->
        <div class="form-group">
            <label for="fecha_inscripcion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inscripción</label>
            <input type="date" 
                   id="fecha_inscripcion" 
                   name="fecha_inscripcion" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   required>
        </div>
    </div>
</div> 