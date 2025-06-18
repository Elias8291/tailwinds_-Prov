@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
                Generador de Oficios - Formato Gobierno de Oaxaca
            </h1>
            
            <form method="POST" action="{{ route('documento.generar-pdf') }}" class="space-y-8">
                @csrf
                
                <!-- Sección: Datos del Oficio -->
                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">📄 Datos del Oficio</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="origen" class="block text-sm font-medium text-gray-700 mb-2">
                                Origen
                            </label>
                            <input type="text" id="origen" name="origen" 
                                   value="Dirección de Recursos Materiales"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="numero_oficio" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Oficio
                            </label>
                            <input type="text" id="numero_oficio" name="numero_oficio" 
                                   value="SA/DRM/DMRA/001/01/2025"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="asunto" class="block text-sm font-medium text-gray-700 mb-2">
                                Asunto
                            </label>
                            <input type="text" id="asunto" name="asunto" 
                                   value="Registro en el Padrón de Proveedores de la Administración Pública Estatal"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="lugar" class="block text-sm font-medium text-gray-700 mb-2">
                                Lugar
                            </label>
                            <input type="text" id="lugar" name="lugar" 
                                   value="Tlalixtac de Cabrera, Oax."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha (formato: dd de mes de aaaa)
                            </label>
                            <input type="text" id="fecha" name="fecha" 
                                   value="{{ date('d \d\e F \d\e Y') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Sección: Destinatario -->
                <div class="bg-green-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-800 mb-4">👤 Datos del Destinatario</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tipo_persona" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Persona
                            </label>
                            <select id="tipo_persona" name="tipo_persona" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    onchange="toggleTipoPersona()">
                                <option value="FISICA">Física</option>
                                <option value="MORAL">Moral</option>
                            </select>
                        </div>

                        <div>
                            <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                                RFC
                            </label>
                            <input type="text" id="rfc" name="rfc" 
                                   value="ROTM850315ABC"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <!-- Campos para Persona Física -->
                        <div id="persona_fisica" class="md:col-span-2 space-y-4">
                            <div>
                                <label for="destinatario_completo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre Completo
                                </label>
                                <input type="text" id="destinatario_completo" name="destinatario_completo" 
                                       value="LIC. MARÍA ELENA RODRÍGUEZ TORRES"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <!-- Campos para Persona Moral -->
                        <div id="persona_moral" class="md:col-span-2 space-y-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="representante" class="block text-sm font-medium text-gray-700 mb-2">
                                        Representante Legal
                                    </label>
                                    <input type="text" id="representante" name="representante" 
                                           value="LIC. JUAN PÉREZ GONZÁLEZ"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label for="facultad" class="block text-sm font-medium text-gray-700 mb-2">
                                        Facultad/Cargo
                                    </label>
                                    <input type="text" id="facultad" name="facultad" 
                                           value="REPRESENTANTE LEGAL"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>

                            <div>
                                <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                                    Razón Social
                                </label>
                                <input type="text" id="razon_social" name="razon_social" 
                                       value="EMPRESA EJEMPLO S.A. DE C.V."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="domicilio" class="block text-sm font-medium text-gray-700 mb-2">
                                Domicilio Completo
                            </label>
                            <textarea id="domicilio" name="domicilio" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">CALLE EJEMPLO NÚMERO EXTERIOR 123, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección: Datos del Trámite -->
                <div class="bg-yellow-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-4">📋 Datos Específicos del Trámite</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fecha_solicitud" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Solicitud
                            </label>
                            <input type="text" id="fecha_solicitud" name="fecha_solicitud" 
                                   value="15 de enero de 2025"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <div>
                            <label for="fecha_recepcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Recepción
                            </label>
                            <input type="text" id="fecha_recepcion" name="fecha_recepcion" 
                                   value="20 de enero de 2025"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <div>
                            <label for="cedula" class="block text-sm font-medium text-gray-700 mb-2">
                                Cédula de Inscripción
                            </label>
                            <input type="text" id="cedula" name="cedula" 
                                   value="PV-2025-001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <div>
                            <label for="tipo_proveedor" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Proveedor
                            </label>
                            <select id="tipo_proveedor" name="tipo_proveedor" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="Estatal">Estatal</option>
                                <option value="Nacional">Nacional</option>
                                <option value="Internacional">Internacional</option>
                            </select>
                        </div>

                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio (MAYÚSCULAS)
                            </label>
                            <input type="text" id="fecha_inicio" name="fecha_inicio" 
                                   value="20 DE ENERO DE 2025"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <div>
                            <label for="fecha_vigencia" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Vigencia (MAYÚSCULAS)
                            </label>
                            <input type="text" id="fecha_vigencia" name="fecha_vigencia" 
                                   value="19 DE ENERO DE 2026"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="giro" class="block text-sm font-medium text-gray-700 mb-2">
                                Giro y/o Clasificación
                            </label>
                            <textarea id="giro" name="giro" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">Servicios profesionales de consultoría en tecnologías de la información y comunicación</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección: Firma -->
                <div class="bg-purple-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-800 mb-4">✍️ Datos de la Firma</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cargo_firmante" class="block text-sm font-medium text-gray-700 mb-2">
                                Cargo del Firmante
                            </label>
                            <input type="text" id="cargo_firmante" name="cargo_firmante" 
                                   value="DIRECTORA DE RECURSOS MATERIALES"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <div>
                            <label for="firmante" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Firmante
                            </label>
                            <input type="text" id="firmante" name="firmante" 
                                   value="LIC. SARA ZÁRATE SANTIAGO"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <div>
                            <label for="ccp" class="block text-sm font-medium text-gray-700 mb-2">
                                C.c.p.
                            </label>
                            <input type="text" id="ccp" name="ccp" 
                                   value="Expediente y Minutario."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <div>
                            <label for="iniciales_firma" class="block text-sm font-medium text-gray-700 mb-2">
                                Iniciales de Firma
                            </label>
                            <input type="text" id="iniciales_firma" name="iniciales_firma" 
                                   value="SZS/ERAG"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>

                <!-- Sección: Contenido Personalizado (Opcional) -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📝 Contenido Personalizado (Opcional)</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        Si desea usar un contenido diferente al texto estándar del padrón de proveedores, escriba aquí su contenido personalizado:
                    </p>
                    <textarea id="contenido_personalizado" name="contenido_personalizado" rows="8"
                              placeholder="Deje vacío para usar el contenido estándar del registro de padrón de proveedores..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"></textarea>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="button" onclick="previsualizar()" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">
                        👁️ Previsualizar en Navegador
                    </button>
                    
                    <button type="submit" 
                            class="flex-1 bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 transition duration-200 font-semibold">
                        📄 Generar PDF
                    </button>
                    
                    <a href="{{ route('documento.ejemplo') }}" 
                       class="flex-1 bg-purple-600 text-white py-3 px-6 rounded-md hover:bg-purple-700 transition duration-200 font-semibold text-center">
                        🎯 Ejemplo Completo
                    </a>
                </div>
            </form>
        </div>

        <!-- Información sobre el formato -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">ℹ️ Información sobre el Formato</h3>
            <div class="text-blue-700 space-y-2">
                <p><strong>Formato:</strong> Oficio oficial del Gobierno del Estado de Oaxaca</p>
                <p><strong>Logo Izquierdo:</strong> <code>public/images/logo_superior_izquierdo.jpg</code></p>
                <p><strong>Membrete Derecho:</strong> <code>public/images/membretep.png</code> (tamaño ampliado)</p>
                <p><strong>Contenido:</strong> Texto estándar para registro en el Padrón de Proveedores</p>
                <p class="text-sm">El documento incluye todos los elementos oficiales: lema constitucional, datos del oficio, fundamento legal y estructura formal.</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTipoPersona() {
    const tipoPersona = document.getElementById('tipo_persona').value;
    const personaFisica = document.getElementById('persona_fisica');
    const personaMoral = document.getElementById('persona_moral');
    
    if (tipoPersona === 'MORAL') {
        personaFisica.classList.add('hidden');
        personaMoral.classList.remove('hidden');
    } else {
        personaFisica.classList.remove('hidden');
        personaMoral.classList.add('hidden');
    }
}

function previsualizar() {
    // Obtener los datos del formulario
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    // Construir la URL con parámetros
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    // Abrir en nueva ventana
    window.open('{{ route("documento.vista") }}?' + params.toString(), '_blank');
}
</script>
@endsection