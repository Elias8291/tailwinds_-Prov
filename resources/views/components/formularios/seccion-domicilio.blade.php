@props(['title' => 'Domicilio'])

<div class="space-y-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
        <i class="fas fa-map-marker-alt text-red-800 mr-2"></i> Domicilio
    </h4>

    <!-- Código Postal -->
    <div class="form-group">
        <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
        <input type="text" id="codigo_postal" name="codigo_postal" 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
               placeholder="Ej: 12345" 
               pattern="[0-9]{4,5}" 
               maxlength="5"
               required>
        <p class="mt-1 text-sm text-gray-500">Al ingresar el código postal se llenarán automáticamente el estado y municipio</p>
    </div>

    <!-- Estado y Municipio -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-group">
            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <input type="text" id="estado" name="estado" 
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" 
                   placeholder="Ej: Jalisco" 
                   readonly 
                   required>
        </div>

        <div class="form-group">
            <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
            <input type="text" id="municipio" name="municipio" 
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" 
                   placeholder="Ej: Guadalajara" 
                   readonly 
                   required>
        </div>
    </div>

    <!-- Colonia -->
    <div class="form-group">
        <label for="colonia" class="block text-sm font-medium text-gray-700 mb-1">Asentamiento</label>
        <select id="colonia" name="colonia" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                required>
            <option value="">Seleccione un Asentamiento</option>
        </select>
    </div>

    <!-- Calle -->
    <div class="form-group">
        <label for="calle" class="block text-sm font-medium text-gray-700 mb-1">Calle</label>
        <input type="text" id="calle" name="calle" 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
               placeholder="Ej: Av. Principal" 
               maxlength="100"
               required>
    </div>

    <!-- Números -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-group">
            <label for="numero_exterior" class="block text-sm font-medium text-gray-700 mb-1">Número Exterior</label>
            <input type="text" id="numero_exterior" name="numero_exterior" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                   placeholder="Ej: 123 o S/N" 
                   maxlength="10" 
                   pattern="[A-Za-z0-9\/]+"
                   required>
        </div>

        <div class="form-group">
            <label for="numero_interior" class="block text-sm font-medium text-gray-700 mb-1">Número Interior</label>
            <input type="text" id="numero_interior" name="numero_interior" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                   placeholder="Ej: 5A" 
                   maxlength="10" 
                   pattern="[A-Za-z0-9]+">
            <p class="mt-1 text-sm text-gray-500">Opcional</p>
        </div>
    </div>

    <!-- Entre Calles -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-group">
            <label for="entre_calle_1" class="block text-sm font-medium text-gray-700 mb-1">Entre Calle</label>
            <input type="text" id="entre_calle_1" name="entre_calle_1" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                   placeholder="Ej: Calle Independencia" 
                   maxlength="100" 
                   pattern="[A-Za-z0-9\s]+"
                   required>
        </div>

        <div class="form-group">
            <label for="entre_calle_2" class="block text-sm font-medium text-gray-700 mb-1">Y Calle</label>
            <input type="text" id="entre_calle_2" name="entre_calle_2" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                   placeholder="Ej: Calle Morelos" 
                   maxlength="100" 
                   pattern="[A-Za-z0-9\s]+"
                   required>
        </div>
    </div>
</div> 