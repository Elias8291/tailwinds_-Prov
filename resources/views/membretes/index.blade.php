@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center">
    <div class="w-full max-w-4xl px-2">
        <div class="bg-white shadow-xl rounded-3xl p-6 border border-gray-100">
            <h1 class="text-4xl font-extrabold text-[#9d2449] mb-6 text-center tracking-tight drop-shadow-lg">
                <i class="fas fa-stamp mr-2"></i> Membretes Oficiales
            </h1>
            <p class="text-gray-600 text-center mb-10 text-lg">
                Selecciona el tipo de documento oficial que deseas generar
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Inscripción -->
                <div class="group bg-white p-4 rounded-xl border border-blue-200 shadow hover:shadow-lg transition-all duration-300 relative overflow-hidden">
                    <div class="absolute -top-6 -right-6 opacity-10 text-blue-400 text-7xl pointer-events-none select-none">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="text-center relative z-10">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow group-hover:scale-105 transition-transform">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-blue-800 mb-1">Inscripción</h3>
                        <p class="text-blue-700 mb-3 text-sm">
                            Oficio de registro inicial en el Padrón de Proveedores de la Administración Pública Estatal
                        </p>
                        <a href="{{ route('membretes.ejemplo.inscripcion') }}" 
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold shadow group-hover:shadow-lg text-sm">
                            <i class="fas fa-file-download"></i> Ver Ejemplo
                        </a>
                    </div>
                </div>
                <!-- Renovación -->
                <div class="group bg-white p-4 rounded-xl border border-green-200 shadow hover:shadow-lg transition-all duration-300 relative overflow-hidden">
                    <div class="absolute -top-6 -right-6 opacity-10 text-green-400 text-7xl pointer-events-none select-none">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="text-center relative z-10">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow group-hover:scale-105 transition-transform">
                            <i class="fas fa-sync-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-green-800 mb-1">Renovación</h3>
                        <p class="text-green-700 mb-3 text-sm">
                            Oficio de renovación anual de registro en el Padrón de Proveedores
                        </p>
                        <a href="{{ route('membretes.ejemplo.renovacion') }}" 
                           class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-semibold shadow group-hover:shadow-lg text-sm">
                            <i class="fas fa-redo"></i> Ver Ejemplo
                        </a>
                    </div>
                </div>
                <!-- Actualización -->
                <div class="group bg-white p-4 rounded-xl border border-purple-200 shadow hover:shadow-lg transition-all duration-300 relative overflow-hidden">
                    <div class="absolute -top-6 -right-6 opacity-10 text-purple-400 text-7xl pointer-events-none select-none">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="text-center relative z-10">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow group-hover:scale-105 transition-transform">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-purple-800 mb-1">Actualización</h3>
                        <p class="text-purple-700 mb-3 text-sm">
                            Oficio de actualización de datos en el Padrón de Proveedores
                        </p>
                        <a href="{{ route('membretes.ejemplo.actualizacion') }}" 
                           class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-200 font-semibold shadow group-hover:shadow-lg text-sm">
                            <i class="fas fa-pen"></i> Ver Ejemplo
                        </a>
                    </div>
                </div>
            </div>
            <!-- Información adicional -->
            <div class="mt-8 bg-white border border-gray-200 rounded-2xl p-6 shadow">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-[#9d2449]"></i> Información sobre los Documentos</h3>
                <div class="text-gray-700 space-y-2 text-base">
                    <p><i class="fas fa-file-alt text-[#9d2449] mr-2"></i><strong>Formato:</strong> Oficio oficial del Gobierno del Estado de Oaxaca</p>
                    <p><i class="fas fa-certificate text-[#9d2449] mr-2"></i><strong>Elementos incluidos:</strong> Logo oficial, membrete, lema constitucional 2025</p>
                    <p><i class="fas fa-list text-[#9d2449] mr-2"></i><strong>Tipos disponibles:</strong></p>
                    <ul class="list-disc list-inside ml-8 space-y-1">
                        <li><strong>Inscripción:</strong> Para registro inicial de nuevos proveedores</li>
                        <li><strong>Renovación:</strong> Para renovación anual de registros existentes</li>
                        <li><strong>Actualización:</strong> Para modificación de datos de proveedores activos</li>
                    </ul>
                    <p class="text-sm mt-4"><i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i><strong>Nota:</strong> Los ejemplos mostrados contienen datos ficticios para fines demostrativos.</p>
                </div>
            </div>
            <!-- Regresar -->
            <div class="mt-8 text-center">
                <a href="{{ route('documentos.index') }}" 
                   class="inline-flex items-center gap-2 px-7 py-3 bg-gradient-to-r from-[#9d2449] to-[#8a203f] text-white rounded-xl hover:from-[#8a203f] hover:to-[#9d2449] transition duration-200 font-semibold shadow">
                    <i class="fas fa-arrow-left"></i>
                    Regresar a Documentos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 