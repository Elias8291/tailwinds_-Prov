@props(['title' => 'Accionistas'])

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <!-- Encabezado con icono -->
    <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
        <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Información sobre los accionistas de la empresa</p>
        </div>
    </div>

    <form class="space-y-8">
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="4">
        <div class="space-y-4">
            <div id="accionistas-container">
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre
                            </label>
                            <input type="text" name="accionistas[0][nombre]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Nombre completo">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                RFC
                            </label>
                            <input type="text" name="accionistas[0][rfc]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="RFC">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                % Acciones
                            </label>
                            <input type="number" name="accionistas[0][porcentaje]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="0" min="0" max="100">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="agregar-accionista" class="w-full py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200">
                + Agregar Accionista
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let contadorAccionistas = 1;
    const contenedor = document.getElementById('accionistas-container');
    const btnAgregar = document.getElementById('agregar-accionista');

    btnAgregar.addEventListener('click', function() {
        const nuevoAccionista = document.createElement('div');
        nuevoAccionista.className = 'bg-gray-50 p-4 rounded-lg mb-4';
        nuevoAccionista.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre
                    </label>
                    <input type="text" name="accionistas[${contadorAccionistas}][nombre]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="Nombre completo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        RFC
                    </label>
                    <input type="text" name="accionistas[${contadorAccionistas}][rfc]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="RFC">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        % Acciones
                    </label>
                    <div class="flex">
                        <input type="number" name="accionistas[${contadorAccionistas}][porcentaje]" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#9D2449] focus:ring focus:ring-[#9D2449] focus:ring-opacity-50 transition duration-200" placeholder="0" min="0" max="100">
                        <button type="button" class="ml-2 text-red-500 hover:text-red-700" onclick="this.closest('.bg-gray-50').remove()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        contenedor.appendChild(nuevoAccionista);
        contadorAccionistas++;
    });
});
</script>
@endpush 