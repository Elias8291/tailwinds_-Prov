@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-3">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="ml-2 text-sm font-medium text-gray-900">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Encabezado -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-4 mb-6 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-lg p-2 shadow-sm">
                        <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#B4325E]">Gestión de Citas</h2>
                        <p class="text-xs text-gray-500">Administra las citas del sistema</p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-2">
                    <a href="{{ route('dias-inhabiles.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-[#B4325E] rounded-lg text-sm font-medium text-[#B4325E] bg-white hover:bg-[#B4325E] hover:text-white transition-all duration-200 w-full md:w-auto">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Agregar Día Inhábil
                    </a>
                    <a href="{{ route('citas.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-200 w-full md:w-auto">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nueva Cita
                    </a>
                </div>
            </div>
        </div>

        <!-- Días Inhábiles -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md overflow-hidden border border-gray-100 mb-6">
            <div class="p-4">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-lg p-1.5 shadow-sm">
                        <svg class="w-4 h-4 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Días Inhábiles</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($diasInhabiles ?? [] as $dia)
                    <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $dia->fecha->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $dia->descripcion }}</p>
                        </div>
                        <form action="{{ route('dias-inhabiles.destroy', $dia) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este día inhábil?')"
                                    class="text-red-600 hover:text-red-900 p-1.5 rounded hover:bg-red-50 transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="col-span-full">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 mb-1">No hay días inhábiles registrados</h3>
                            <p class="text-xs text-gray-500">Agrega un día inhábil para bloquear fechas específicas</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md overflow-hidden border border-gray-100">
            <!-- Vista móvil -->
            <div class="block md:hidden">
                @forelse($citas as $cita)
                <div class="p-3 border-b border-gray-100 hover:bg-gray-50/50 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-lg shadow-sm flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $cita->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $cita->fecha_hora->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('citas.edit', $cita) }}" 
                               class="p-1.5 text-[#B4325E] hover:text-[#93264B] hover:bg-[#B4325E]/10 rounded transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                            <form action="{{ route('citas.destroy', $cita) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta cita?')"
                                        class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if($cita->estado === 'pendiente') bg-yellow-50 text-yellow-700 border border-yellow-100
                            @elseif($cita->estado === 'confirmada') bg-green-50 text-green-700 border border-green-100
                            @elseif($cita->estado === 'cancelada') bg-red-50 text-red-700 border border-red-100
                            @else bg-gray-50 text-gray-700 border border-gray-100 @endif">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </div>
                    <div class="mt-1.5 text-xs text-gray-500">
                        {{ $cita->motivo }}
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-900 mb-1">No hay citas registradas</h3>
                    <p class="text-xs text-gray-500">Agrega una nueva cita para comenzar</p>
                </div>
                @endforelse
            </div>

            <!-- Vista desktop -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-[#B4325E] text-white">
                            <th scope="col" class="px-4 py-3 text-left text-xs uppercase tracking-wider">Usuario</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs uppercase tracking-wider">Fecha y Hora</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs uppercase tracking-wider">Motivo</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($citas as $cita)
                        <tr class="hover:bg-gray-50/50 transition-all duration-200">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-[#B4325E] to-[#93264B] text-white rounded-lg shadow-sm flex items-center justify-center">
                                        {{ strtoupper(substr($cita->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $cita->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $cita->fecha_hora->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900">{{ $cita->motivo }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($cita->estado === 'pendiente') bg-yellow-50 text-yellow-700 border border-yellow-100
                                    @elseif($cita->estado === 'confirmada') bg-green-50 text-green-700 border border-green-100
                                    @elseif($cita->estado === 'cancelada') bg-red-50 text-red-700 border border-red-100
                                    @else bg-gray-50 text-gray-700 border border-gray-100 @endif">
                                    {{ ucfirst($cita->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('citas.show', $cita) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('citas.edit', $cita) }}" 
                                       class="text-[#B4325E] hover:text-[#93264B] transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('citas.destroy', $cita) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta cita?')"
                                                class="text-red-600 hover:text-red-900 transform hover:scale-110 transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-8">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-medium text-gray-900 mb-1">No hay citas registradas</h3>
                                    <p class="text-xs text-gray-500">Agrega una nueva cita para comenzar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($citas->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $citas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 