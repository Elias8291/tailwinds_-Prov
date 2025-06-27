@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

<div class="min-h-screen font-montserrat py-8">
    <!-- Contenedor principal más compacto -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Card contenedora principal -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="p-5 border-b border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-4 lg:mb-0">
                        <div class="flex items-center gap-3 mb-2">
                            <span id="currentTime" class="text-2xl font-semibold text-[#9d2449]/70"></span>
                        </div>
                        <h2 id="greeting" class="text-2xl font-bold text-[#9d2449] mb-1">
                            Buenos días, {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
                        </h2>
                        <p class="text-gray-600 text-sm">Bienvenido al Padrón de Proveedores del Estado De Oaxaca</p>
                    </div>
                   
                </div>
            </div>

            <div class="p-5">
                @can('dashboard.ver-estadisticas')
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Usuarios -->
                    <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-gray-800">Usuarios</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-2xl font-bold text-indigo-600">{{ $totalUsuarios ?? 0 }}</p>
                                    <span class="ml-2 text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full">Total</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium hover:underline transition-colors duration-200">
                                <span>Ver todos los usuarios</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Trámites -->
                    <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-amber-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-gray-800">Trámites</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-2xl font-bold text-amber-600">{{ $tramitesPendientes ?? 0 }}</p>
                                    <span class="ml-2 text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Pendientes</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('revision.index') }}" class="inline-flex items-center text-amber-600 hover:text-amber-800 text-sm font-medium hover:underline transition-colors duration-200">
                                <span>Revisar trámites</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Proveedores -->
                    <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-emerald-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-gray-800">Proveedores</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-2xl font-bold text-emerald-600">{{ $totalProveedores ?? 0 }}</p>
                                    <span class="ml-2 text-xs font-medium text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">Registrados</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('proveedores.index') }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-800 text-sm font-medium hover:underline transition-colors duration-200">
                                <span>Gestionar proveedores</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Citas -->
                    <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-rose-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-semibold text-gray-800">Citas</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-2xl font-bold text-rose-600">{{ $totalCitas ?? 0 }}</p>
                                    <span class="ml-2 text-xs font-medium text-rose-600 bg-rose-100 px-2 py-1 rounded-full">{{ $citasHoy ?? 0 }} hoy</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('citas.index') }}" class="inline-flex items-center text-rose-600 hover:text-rose-800 text-sm font-medium hover:underline transition-colors duration-200">
                                <span>Ver calendario</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions Section -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 h-full">
                            <div class="p-4 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-[#9d2449] flex items-center">
                                    <i class="fa-solid fa-bolt mr-2"></i>
                                    Acciones del Sistema
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <!-- Mis Trámites -->
                                <a href="{{ route('tramites.solicitante.index') }}" class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300 block">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-file-lines text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Mis Trámites</h4>
                                            <p class="text-xs text-gray-500">Gestiona tus trámites en proceso</p>
                                        </div>
                                    </div>
                                </a>

                                <!-- Estado del Proveedor -->
                                <a href="{{ route('mi-estado-proveedor.index') }}" class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300 block">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-chart-line text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Estado del Proveedor</h4>
                                            <p class="text-xs text-gray-500">Consulta tu estatus actual</p>
                                        </div>
                                    </div>
                                </a>

                                <!-- Citas -->
                                <a href="{{ route('citas.index') }}" class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300 block">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-calendar-check text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Agendar Cita</h4>
                                            <p class="text-xs text-gray-500">Programa tu visita para entrega de documentos</p>
                                        </div>
                                    </div>
                                </a>


                            </div>
                        </div>
                    </div>

                    <!-- Assistant Section -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 h-full flex flex-col justify-between overflow-hidden">
                            <div class="mt-auto flex justify-center items-end h-full bg-white">
                                <img src="{{ asset('images/mujer_bienvenida.png') }}" alt="Asistente" class="w-auto h-[400px] object-contain">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'pm' : 'am';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;

            document.getElementById('currentTime').textContent = `${formattedHours}:${formattedMinutes} ${ampm}`;

            const greeting = document.getElementById('greeting');
            const userName = '{{ auth()->check() ? auth()->user()->name : 'Invitado' }}';

            if (hours < 12) {
                greeting.textContent = `Buenos días, ${userName}`;
            } else if (hours >= 12 && hours < 19) {
                greeting.textContent = `Buenas tardes, ${userName}`;
            } else {
                greeting.textContent = `Buenas noches, ${userName}`;
            }
        }

        updateTime();
        setInterval(updateTime, 60000);
    });
</script>
@endsection