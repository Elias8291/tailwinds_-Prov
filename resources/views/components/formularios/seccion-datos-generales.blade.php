@props(['title' => 'Datos Generales'])

<div class="max-w-3xl mx-auto bg-white/85 rounded-lg shadow-subtle p-8">
    <!-- Encabezado de la sección -->
    <div class="mb-6 pb-4 border-b border-borderLight">
        <h4 class="text-xl font-semibold text-textDark mb-1 flex items-center gap-2">
            <i class="fas fa-building text-primary"></i> Datos Generales
        </h4>
        <p class="text-sm text-textLight">Complete la información general del proveedor</p>
    </div>

    <form>
        <!-- Grupo de campos horizontal -->
        <div class="flex flex-wrap gap-6 mb-6">
            <!-- Tipo de Proveedor -->
            <div class="flex-1 min-w-[300px]">
                <label for="tipo_persona" class="block text-sm font-medium text-textMedium mb-1">
                    <i class="fas fa-user-tie text-primary/80 mr-1"></i> Tipo de Proveedor
                </label>
                <select name="tipo_persona" id="tipo_persona" 
                        class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all">
                    <option value="">Seleccione un tipo</option>
                    <option value="Física">Física</option>
                    <option value="Moral">Moral</option>
                </select>
            </div>

            <!-- RFC -->
            <div class="flex-1 min-w-[300px]">
                <label for="rfc" class="block text-sm font-medium text-textMedium mb-1">
                    <i class="fas fa-id-card text-primary/80 mr-1"></i> RFC
                </label>
                <input type="text" name="rfc" id="rfc" 
                       class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all" 
                       placeholder="Ej. XAXX010101000" 
                       maxlength="13">
            </div>
        </div>

        <!-- Razón Social -->
        <div class="mb-6">
            <label for="razon_social" class="block text-sm font-medium text-textMedium mb-1">
                <i class="fas fa-building text-primary/80 mr-1"></i> Razón Social
            </label>
            <input type="text" id="razon_social" name="razon_social" 
                   class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all" 
                   placeholder="Nombre de la empresa"
                   maxlength="100">
        </div>

        <!-- Objeto Social -->
        <div class="mb-6">
            <label for="objeto_social" class="block text-sm font-medium text-textMedium mb-1">
                <i class="fas fa-file-alt text-primary/80 mr-1"></i> Objeto Social
            </label>
            <textarea id="objeto_social" name="objeto_social" 
                      class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all" 
                      placeholder="Describa el objeto social de la empresa"
                      maxlength="500" 
                      rows="4"></textarea>
        </div>

        <!-- Sectores -->
        <div class="mb-6">
            <label for="sectores" class="block text-sm font-medium text-textMedium mb-1">
                <i class="fas fa-industry text-primary/80 mr-1"></i> Sectores
            </label>
            <select name="sectores" id="sectores" 
                    class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all">
                <option value="">Seleccione un sector</option>
                <option value="1">Construcción</option>
                <option value="2">Servicios</option>
                <option value="3">Comercio</option>
            </select>
        </div>

        <!-- Actividades -->
        <div class="mb-6">
            <label for="actividad" class="block text-sm font-medium text-textMedium mb-1">
                <i class="fas fa-tasks text-primary/80 mr-1"></i> Actividades
            </label>
            <select name="actividad" id="actividad" 
                    class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all mb-2">
                <option value="">Seleccione una actividad</option>
                <option value="1">Construcción de edificios</option>
                <option value="2">Mantenimiento de edificios</option>
            </select>

            <!-- Actividades seleccionadas -->
            <div class="bg-bgSubtle p-4 rounded-custom border border-borderLight mt-2 min-h-[60px]">
                <div class="flex flex-wrap gap-2">
                    <div class="bg-white px-3 py-2 rounded-full border border-borderLight shadow-sm flex items-center">
                        <span class="text-sm text-textDark">Construcción de edificios</span>
                        <button type="button" class="ml-2 w-5 h-5 rounded-full bg-danger text-white flex items-center justify-center text-xs hover:bg-danger/90 transition-colors">×</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de datos de contacto -->
        <div class="mb-6 pt-6 border-t border-borderLight">
            <h4 class="text-xl font-semibold text-textDark mb-1 flex items-center gap-2">
                <i class="fas fa-address-card text-primary"></i> Datos de Contacto
            </h4>
            <p class="text-sm text-textLight mb-4">Persona encargada de recibir solicitudes y requerimientos</p>

            <!-- Nombre Completo -->
            <div class="mb-6">
                <label for="contacto_nombre" class="block text-sm font-medium text-textMedium mb-1">
                    <i class="fas fa-user text-primary/80 mr-1"></i> Nombre Completo
                </label>
                <input type="text" id="contacto_nombre" name="contacto_nombre" 
                       class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all" 
                       placeholder="Nombre completo del contacto"
                       maxlength="40">
            </div>

            <!-- Cargo o Puesto -->
            <div class="mb-6">
                <label for="contacto_cargo" class="block text-sm font-medium text-textMedium mb-1">
                    <i class="fas fa-briefcase text-primary/80 mr-1"></i> Cargo o Puesto
                </label>
                <input type="text" id="contacto_cargo" name="contacto_cargo" 
                       class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all" 
                       placeholder="Cargo en la empresa"
                       maxlength="50">
            </div>

            <!-- Correo Electrónico -->
            <div class="mb-6">
                <label for="contacto_correo" class="block text-sm font-medium text-textMedium mb-1">
                    <i class="fas fa-envelope text-primary/80 mr-1"></i> Correo Electrónico
                </label>
                <input type="email" id="contacto_correo" name="contacto_correo" 
                       class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all"
                       placeholder="correo@ejemplo.com">
            </div>

            <!-- Teléfono de Contacto -->
            <div class="mb-6">
                <label for="contacto_telefono" class="block text-sm font-medium text-textMedium mb-1">
                    <i class="fas fa-phone text-primary/80 mr-1"></i> Teléfono de Contacto
                </label>
                <input type="tel" id="contacto_telefono" name="contacto_telefono" 
                       class="w-full px-4 py-3 text-base border border-borderLight rounded-custom focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all" 
                       placeholder="10 dígitos"
                       pattern="[0-9]{10}" 
                       maxlength="10" 
                       inputmode="numeric">
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="flex justify-between mt-8">
            <button type="button" class="px-6 py-3 bg-secondary text-white rounded-custom font-medium hover:bg-secondary/90 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Anterior
            </button>
            <button type="button" class="px-6 py-3 bg-primary text-white rounded-custom font-medium hover:bg-primary/90 transition-colors flex items-center gap-2">
                Siguiente <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div> 