@extends('layouts.app')

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-[95%] xl:max-w-[90%] 2xl:max-w-[85%] mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3 shadow-md">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Gestión de Calendario
                        </h2>
                        <p class="text-sm text-gray-500">Administra las citas y días inhábiles</p>
                    </div>
                </div>
            </div>

            <!-- Tabs de navegación -->
            <div class="mt-6">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 rounded-t-lg border-b-2 border-[#B4325E] text-[#B4325E]" 
                                id="citas-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#citas" 
                                type="button" 
                                role="tab" 
                                aria-controls="citas" 
                                aria-selected="true">
                            Citas
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300" 
                                id="dias-inhabiles-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#dias-inhabiles" 
                                type="button" 
                                role="tab" 
                                aria-controls="dias-inhabiles" 
                                aria-selected="false">
                            Días Inhábiles
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenido de los tabs -->
        <div class="tab-content">
            <!-- Tab de Citas -->
            <div class="tab-pane fade show active" id="citas" role="tabpanel">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden">
                    <!-- Barra de herramientas -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex flex-col lg:flex-row gap-4 justify-between">
                            <!-- Búsqueda y Filtros -->
                            <div class="flex flex-col md:flex-row gap-3 flex-grow">
                                <div class="flex flex-grow">
                                    <input type="text" 
                                           placeholder="Buscar por solicitante o trámite..."
                                           class="w-full px-3 h-10 rounded-l border-2 border-[#B4325E] focus:outline-none focus:border-[#B4325E]">
                                    <button type="button" 
                                            class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-r px-4 hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </button>
                                </div>
                                <select class="w-full md:w-48 h-10 border-2 border-gray-300 rounded px-3 focus:outline-none focus:border-[#B4325E]">
                                    <option value="">Todos los estados</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Confirmada">Confirmada</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                                <button type="button" 
                                        class="px-4 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-xl hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 shadow-sm hover:shadow flex items-center gap-2"
                                        data-toggle="modal" 
                                        data-target="#nuevaCitaModal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Nueva Cita
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Citas -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-[#B4325E] text-white">
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Solicitante</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Trámite</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Hora</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($citas as $cita)
                                <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-xl shadow-md flex items-center justify-center font-bold text-xl">
                                                {{ strtoupper(substr($cita->solicitante->nombre, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $cita->solicitante->nombre }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $cita->tramite->nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $cita->fecha_cita }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $cita->hora_cita }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium
                                            @if($cita->estado === 'Confirmada') bg-green-50 text-green-700 border border-green-100
                                            @elseif($cita->estado === 'Cancelada') bg-red-50 text-red-700 border border-red-100
                                            @else bg-yellow-50 text-yellow-700 border border-yellow-100
                                            @endif">
                                            {{ $cita->estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button onclick="editarCita({{ $cita->id }})" 
                                                    class="text-[#B4325E] hover:text-[#93264B] transform hover:scale-110 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                            <button onclick="eliminarCita({{ $cita->id }})"
                                                    class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab de Días Inhábiles -->
            <div class="tab-pane fade" id="dias-inhabiles" role="tabpanel">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden">
                    <!-- Barra de herramientas -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Días Inhábiles</h3>
                            <button type="button" 
                                    class="px-4 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-xl hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 shadow-sm hover:shadow flex items-center gap-2"
                                    data-toggle="modal" 
                                    data-target="#nuevoDiaInhabilModal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Nuevo Día Inhábil
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de Días Inhábiles -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-[#B4325E] text-white">
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha Inicio</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Fecha Fin</th>
                                    <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Descripción</th>
                                    <th class="px-6 py-4 text-center text-xs uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($diasInhabiles as $dia)
                                <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $dia->fecha_inicio }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $dia->fecha_fin ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $dia->descripcion }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button onclick="editarDiaInhabil({{ $dia->id }})"
                                                    class="text-[#B4325E] hover:text-[#93264B] transform hover:scale-110 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                            <button onclick="eliminarDiaInhabil({{ $dia->id }})"
                                                    class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Cita -->
<div class="modal fade" id="nuevaCitaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-2xl">
            <div class="modal-header border-b border-gray-100 p-4">
                <h5 class="text-lg font-semibold text-gray-900">Nueva Cita</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevaCita" action="{{ route('citas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Solicitante</label>
                            <select name="solicitante_id" required
                                    class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                                @foreach($solicitantes as $solicitante)
                                <option value="{{ $solicitante->id }}">{{ $solicitante->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trámite</label>
                            <select name="tramite_id" required
                                    class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                                @foreach($tramites as $tramite)
                                <option value="{{ $tramite->id }}">{{ $tramite->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                            <input type="date" name="fecha_cita" required
                                   class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                            <input type="time" name="hora_cita" required
                                   class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones"
                                      class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200"
                                      rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-gray-100 p-4">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200"
                            data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-xl hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 ml-3">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nuevo Día Inhábil -->
<div class="modal fade" id="nuevoDiaInhabilModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-2xl">
            <div class="modal-header border-b border-gray-100 p-4">
                <h5 class="text-lg font-semibold text-gray-900">Nuevo Día Inhábil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevoDiaInhabil" action="{{ route('dias-inhabiles.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" required
                                   class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                            <input type="date" name="fecha_fin"
                                   class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <input type="text" name="descripcion" required
                                   class="w-full rounded-xl border-2 border-gray-300 focus:border-[#B4325E] focus:outline-none transition-colors duration-200">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-gray-100 p-4">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200"
                            data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white rounded-xl hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 ml-3">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function editarCita(id) {
        window.location.href = `/admin/citas/${id}/edit`;
    }

    function eliminarCita(id) {
        if (confirm('¿Está seguro de que desea eliminar esta cita?')) {
            fetch(`/admin/citas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }

    function editarDiaInhabil(id) {
        window.location.href = `/admin/dias-inhabiles/${id}/edit`;
    }

    function eliminarDiaInhabil(id) {
        if (confirm('¿Está seguro de que desea eliminar este día inhábil?')) {
            fetch(`/admin/dias-inhabiles/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endpush 