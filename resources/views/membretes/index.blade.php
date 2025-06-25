@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
                📄 Módulo de Membretes Oficiales
            </h1>
            
            <p class="text-gray-600 text-center mb-8">
                Seleccione el tipo de documento oficial que desea generar
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Inscripción -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200 hover:shadow-lg transition-all duration-300">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-plus text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-blue-800 mb-3">Inscripción</h3>
                        <p class="text-blue-600 mb-4 text-sm">
                            Oficio de registro inicial en el Padrón de Proveedores de la Administración Pública Estatal
                        </p>
                        <a href="{{ route('membretes.ejemplo.inscripcion') }}" 
                           class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                            📥 Ver Ejemplo
                        </a>
                    </div>
                </div>

                <!-- Renovación -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200 hover:shadow-lg transition-all duration-300">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-sync-alt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-green-800 mb-3">Renovación</h3>
                        <p class="text-green-600 mb-4 text-sm">
                            Oficio de renovación anual de registro en el Padrón de Proveedores
                        </p>
                        <a href="{{ route('membretes.ejemplo.renovacion') }}" 
                           class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                            🔄 Ver Ejemplo
                        </a>
                    </div>
                </div>

                <!-- Actualización -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200 hover:shadow-lg transition-all duration-300">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-edit text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-purple-800 mb-3">Actualización</h3>
                        <p class="text-purple-600 mb-4 text-sm">
                            Oficio de actualización de datos en el Padrón de Proveedores
                        </p>
                        <a href="{{ route('membretes.ejemplo.actualizacion') }}" 
                           class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition duration-200 font-semibold">
                            ✏️ Ver Ejemplo
                        </a>
                    </div>
                </div>

            </div>

            <!-- Información adicional -->
            <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">ℹ️ Información sobre los Documentos</h3>
                <div class="text-gray-700 space-y-2">
                    <p><strong>Formato:</strong> Oficio oficial del Gobierno del Estado de Oaxaca</p>
                    <p><strong>Elementos incluidos:</strong> Logo oficial, membrete, lema constitucional 2025</p>
                    <p><strong>Tipos disponibles:</strong></p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li><strong>Inscripción:</strong> Para registro inicial de nuevos proveedores</li>
                        <li><strong>Renovación:</strong> Para renovación anual de registros existentes</li>
                        <li><strong>Actualización:</strong> Para modificación de datos de proveedores activos</li>
                    </ul>
                    <p class="text-sm mt-4"><strong>Nota:</strong> Los ejemplos mostrados contienen datos ficticios para fines demostrativos.</p>
                </div>
            </div>

            <!-- Regresar -->
            <div class="mt-8 text-center">
                <a href="{{ route('documentos.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200 font-semibold">
                    <i class="fas fa-arrow-left"></i>
                    Regresar a Documentos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 