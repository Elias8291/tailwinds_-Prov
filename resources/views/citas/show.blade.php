@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

<div class="min-h-screen bg-gray-100 font-montserrat py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[#9d2449]">Detalles de la Cita</h2>
                        <p class="text-gray-600 text-sm">Información completa de la cita</p>
                    </div>
                    <a href="{{ route('citas.index') }}" class="inline-flex items-center text-gray-500 hover:text-[#9d2449]">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-6 space-y-6">
                <!-- Estado de la Cita -->
                <div class="flex justify-between items-center">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        @if($cita->estado === 'pendiente') bg-yellow-100 text-yellow-800
                        @elseif($cita->estado === 'confirmada') bg-green-100 text-green-800
                        @elseif($cita->estado === 'cancelada') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($cita->estado) }}
                    </span>
                    <div class="flex space-x-2">
                        <a href="{{ route('citas.edit', $cita) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </a>
                        <form action="{{ route('citas.destroy', $cita) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar esta cita?')">
                                <i class="fas fa-trash mr-2"></i>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Información de la Cita -->
                <div class="grid grid-cols-1 gap-6">
                    <!-- Usuario -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Usuario</h3>
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-[#9d2449] text-white flex items-center justify-center">
                                <span class="text-lg font-medium">{{ substr($cita->user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $cita->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $cita->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fecha y Hora</h3>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-[#9d2449] mr-2"></i>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $cita->fecha_hora->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- Motivo -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Motivo de la Cita</h3>
                        <p class="text-sm text-gray-900">{{ $cita->motivo }}</p>
                    </div>

                    <!-- Notas -->
                    @if($cita->notas)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Notas Adicionales</h3>
                        <p class="text-sm text-gray-900">{{ $cita->notas }}</p>
                    </div>
                    @endif

                    <!-- Fechas de Registro -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Información del Registro</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                <p class="text-sm text-gray-500">
                                    Creada: {{ $cita->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            @if($cita->updated_at != $cita->created_at)
                            <div class="flex items-center">
                                <i class="fas fa-edit text-gray-400 mr-2"></i>
                                <p class="text-sm text-gray-500">
                                    Última actualización: {{ $cita->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 