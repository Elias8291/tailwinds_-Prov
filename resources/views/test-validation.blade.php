@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                Sistema de Validación de Formularios
            </h1>
            <p class="text-gray-600">Prueba las validaciones en tiempo real de los formularios del sistema.</p>
        </div>

        <!-- Formulario de Prueba -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Formulario de Prueba</h2>
            
            <form id="test-form" data-validate="true" class="space-y-6">
                @csrf
                
                <!-- RFC -->
                <div class="form-group">
                    <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                        RFC
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="rfc" 
                               name="rfc"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="XAXX010101000"
                               maxlength="13"
                               data-validation="required|rfc"
                               required>
                    </div>
                </div>

                <!-- CURP -->
                <div class="form-group">
                    <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                        CURP
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="curp" 
                               name="curp"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="XAXX010101HDFXXX00"
                               maxlength="18"
                               data-validation="required|curp"
                               required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="email" 
                               id="email" 
                               name="email"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="usuario@ejemplo.com"
                               data-validation="required|email"
                               required>
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="tel" 
                               id="telefono" 
                               name="telefono"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="1234567890"
                               maxlength="10"
                               data-validation="required|phone"
                               required>
                    </div>
                </div>

                <!-- Código Postal -->
                <div class="form-group">
                    <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-2">
                        Código Postal
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="codigo_postal" 
                               name="codigo_postal"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="68000"
                               maxlength="5"
                               data-validation="required|cp"
                               required>
                    </div>
                </div>

                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="nombre" 
                               name="nombre"
                               class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="Juan Pérez García"
                               minlength="2"
                               maxlength="100"
                               data-validation="required|minLength:2|maxLength:100|alphanumeric"
                               required>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="form-group">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                        <span class="text-gray-500">(Opcional)</span>
                    </label>
                    <div class="relative">
                        <textarea 
                            id="observaciones" 
                            name="observaciones"
                            rows="4"
                            class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                            placeholder="Ingrese cualquier observación adicional..."
                            maxlength="500"
                            data-validation="maxLength:500"></textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <button type="button" onclick="clearFormErrors('#test-form')" 
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-eraser mr-2"></i>
                        Limpiar Errores
                    </button>
                    
                    <div class="space-x-3">
                        <button type="button" onclick="validateFormManually()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            Validar
                        </button>
                        
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información -->
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mt-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Funcionalidades de Validación
            </h3>
            <ul class="space-y-2 text-blue-800">
                <li><i class="fas fa-check text-blue-600 mr-2"></i>Validación en tiempo real al perder el foco</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i>Formato automático de RFC, CURP y teléfono</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i>Validación de email con expresiones regulares</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i>Indicadores visuales de éxito y error</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i>Mensajes de error descriptivos</li>
                <li><i class="fas fa-check text-blue-600 mr-2"></i>Prevención de envío si hay errores</li>
            </ul>
        </div>
    </div>
</div>

<script>
function validateFormManually() {
    const isValid = validateForm('#test-form');
    if (isValid) {
        showValidationToast('¡Todos los campos son válidos!', 'success');
    } else {
        showValidationToast('Hay errores en el formulario', 'error');
    }
}

// Manejar envío del formulario
document.getElementById('test-form').addEventListener('submit', function(e) {
    e.preventDefault();
    showValidationToast('¡Formulario enviado correctamente!', 'success');
});
</script>
@endsection 