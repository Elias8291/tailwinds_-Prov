@props(['title' => 'Datos de Constitución'])

<!-- Sección de Constitución -->
<div class="space-y-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-6 pb-3 border-b border-gray-200 flex items-center">
        <i class="fas fa-file-contract text-red-800 mr-2"></i> Constitución
    </h4>

    <!-- Datos de Constitución -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Fecha de Constitución -->
        <div class="form-group">
            <label for="fecha_constitucion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Constitución</label>
            <input type="date" id="fecha_constitucion" name="fecha_constitucion" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                   required>
        </div>

        <!-- Número de Escritura -->
        <div class="form-group">
            <label for="numero_escritura" class="block text-sm font-medium text-gray-700 mb-1">Número de Escritura</label>
            <input type="text" id="numero_escritura" name="numero_escritura" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                   pattern="[0-9]+"
                   required>
        </div>
    </div>

    <!-- Notario -->
    <div class="bg-gray-50 p-6 rounded-lg space-y-6">
        <h5 class="font-medium text-gray-900">Datos del Notario</h5>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre del Notario -->
            <div class="form-group">
                <label for="notario_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Notario</label>
                <input type="text" id="notario_nombre" name="notario_nombre" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                       pattern="[A-Za-z\s]+"
                       required>
            </div>

            <!-- Número de Notaría -->
            <div class="form-group">
                <label for="notaria_numero" class="block text-sm font-medium text-gray-700 mb-1">Número de Notaría</label>
                <input type="text" id="notaria_numero" name="notaria_numero" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                       pattern="[0-9]+"
                       required>
            </div>
        </div>

        <!-- Estado de la Notaría -->
        <div class="form-group">
            <label for="notaria_estado" class="block text-sm font-medium text-gray-700 mb-1">Estado de la Notaría</label>
            <select id="notaria_estado" name="notaria_estado" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required>
                <option value="">Seleccione un estado</option>
                <!-- Los estados se cargarán dinámicamente -->
            </select>
        </div>
    </div>

    <!-- Documentos -->
    <div class="space-y-6">
        <!-- Acta Constitutiva -->
        <div class="form-group">
            <label for="acta_constitutiva" class="block text-sm font-medium text-gray-700 mb-1">Acta Constitutiva</label>
            <input type="file" id="acta_constitutiva" name="acta_constitutiva" 
                   class="mt-1 block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-md file:border-0
                          file:text-sm file:font-semibold
                          file:bg-red-50 file:text-red-700
                          hover:file:bg-red-100"
                   accept="application/pdf"
                   required>
            <p class="mt-1 text-sm text-gray-500">Archivo PDF, máximo 5MB</p>
        </div>

        <!-- Poder Notarial -->
        <div class="form-group">
            <label for="poder_notarial" class="block text-sm font-medium text-gray-700 mb-1">Poder Notarial</label>
            <input type="file" id="poder_notarial" name="poder_notarial" 
                   class="mt-1 block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-md file:border-0
                          file:text-sm file:font-semibold
                          file:bg-red-50 file:text-red-700
                          hover:file:bg-red-100"
                   accept="application/pdf"
                   required>
            <p class="mt-1 text-sm text-gray-500">Archivo PDF, máximo 5MB</p>
        </div>
    </div>
</div> 