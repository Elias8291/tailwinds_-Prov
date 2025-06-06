@props(['title' => 'Dirección'])

<div class="card-custom rounded-xl p-6">
    <div class="flex items-center mb-6">
        <svg class="w-6 h-6 text-[#B4325E] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="action" value="next">
        
        <!-- Código Postal -->
        <div class="form-group">
            <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
            <input type="text" 
                   id="codigo_postal" 
                   name="codigo_postal" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: 12345" 
                   required 
                   pattern="[0-9]{4,5}" 
                   maxlength="5">
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <input type="text" 
                   id="estado" 
                   name="estado" 
                   class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm" 
                   placeholder="Ej: Jalisco" 
                   readonly 
                   required>
        </div>

        <!-- Municipio -->
        <div class="form-group">
            <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
            <input type="text" 
                   id="municipio" 
                   name="municipio" 
                   class="w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm" 
                   placeholder="Ej: Guadalajara" 
                   readonly 
                   required>
        </div>

        <!-- Colonia -->
        <div class="form-group">
            <label for="colonia" class="block text-sm font-medium text-gray-700 mb-1">Asentamiento</label>
            <select id="colonia" 
                    name="colonia" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                    required>
                <option value="">Seleccione un Asentamiento</option>
            </select>
        </div>

        <!-- Calle -->
        <div class="form-group md:col-span-2">
            <label for="calle" class="block text-sm font-medium text-gray-700 mb-1">Calle</label>
            <input type="text" 
                   id="calle" 
                   name="calle" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: Av. Principal" 
                   required 
                   maxlength="100">
        </div>

        <!-- Número Exterior -->
        <div class="form-group">
            <label for="numero_exterior" class="block text-sm font-medium text-gray-700 mb-1">Número Exterior</label>
            <input type="text" 
                   id="numero_exterior" 
                   name="numero_exterior" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: 123 o S/N" 
                   required 
                   maxlength="10" 
                   pattern="[A-Za-z0-9\/]+">
        </div>

        <!-- Número Interior -->
        <div class="form-group">
            <label for="numero_interior" class="block text-sm font-medium text-gray-700 mb-1">Número Interior</label>
            <input type="text" 
                   id="numero_interior" 
                   name="numero_interior" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: 5A" 
                   maxlength="10" 
                   pattern="[A-Za-z0-9]+">
        </div>

        <!-- Entre Calle 1 -->
        <div class="form-group">
            <label for="entre_calle_1" class="block text-sm font-medium text-gray-700 mb-1">Entre Calle 1</label>
            <input type="text" 
                   id="entre_calle_1" 
                   name="entre_calle_1" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: Calle Independencia" 
                   required 
                   maxlength="100" 
                   pattern="[A-Za-z0-9\s]+">
        </div>

        <!-- Entre Calle 2 -->
        <div class="form-group">
            <label for="entre_calle_2" class="block text-sm font-medium text-gray-700 mb-1">Entre Calle 2</label>
            <input type="text" 
                   id="entre_calle_2" 
                   name="entre_calle_2" 
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#B4325E] focus:ring focus:ring-[#B4325E] focus:ring-opacity-50 transition duration-200" 
                   placeholder="Ej: Calle Morelos" 
                   required 
                   maxlength="100" 
                   pattern="[A-Za-z0-9\s]+">
        </div>
    </div>
</div>

<script>
document.getElementById('codigo_postal').addEventListener('input', function(e) {
    const cp = e.target.value;
    if (cp.length === 5) {
        // Aquí puedes agregar la lógica para buscar el CP y llenar los campos
        // Por ejemplo, hacer una llamada AJAX a tu API de códigos postales
    }
});
</script> 