@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .gradient-text {
        background: linear-gradient(135deg, #9d2449 0%, #be185d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-4px);
    }

    .icon-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>
@endpush

<div class="min-h-screen py-8 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-gray-100/50 overflow-hidden">
            <!-- Header Section -->
            <div class="p-8 border-b border-gray-100/30">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 px-4 py-2 rounded-full text-sm font-semibold text-gray-600 flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                <span id="currentTime" class="font-mono"></span>
                            </div>
                            <div class="h-6 w-px bg-gray-200"></div>
                            <div class="text-sm text-gray-500 font-medium flex items-center">
                                <i class="fas fa-calendar-day mr-2"></i>
                                <span id="currentDate"></span>
                            </div>
                        </div>
                        <h1 id="greeting" class="text-3xl font-bold gradient-text mb-2">
                            Buenos días, {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
                        </h1>
                        <p class="text-gray-600 text-base font-medium">
                            Bienvenido al <span class="text-[#9d2449] font-semibold">Padrón de Proveedores del Estado de Oaxaca</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                @can('dashboard.ver-estadisticas')
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Usuarios -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-indigo-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $totalUsuarios ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-full mt-1">
                                    Total registrados
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Usuarios del Sistema</h3>
                        <p class="text-sm text-gray-500">Gestión completa de usuarios</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a href="{{ route('users.index') }}" class="group inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition-colors">
                                <span>Administrar usuarios</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Trámites -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 to-amber-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-amber-500 to-amber-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-alt text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $tramitesPendientes ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-amber-600 bg-amber-50 rounded-full mt-1">
                                    Pendientes de revisión
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Trámites en Proceso</h3>
                        <p class="text-sm text-gray-500">Solicitudes por aprobar</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a href="{{ route('revision.index') }}" class="group inline-flex items-center text-amber-600 hover:text-amber-800 text-sm font-semibold transition-colors">
                                <span>Revisar trámites</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Proveedores -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-building text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $totalProveedores ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-full mt-1">
                                    Activos en el padrón
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Proveedores Registrados</h3>
                        <p class="text-sm text-gray-500">Base de datos completa</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a href="{{ route('proveedores.index') }}" class="group inline-flex items-center text-emerald-600 hover:text-emerald-800 text-sm font-semibold transition-colors">
                                <span>Gestionar proveedores</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Citas -->
                    <div class="bg-white rounded-2xl p-6 card-hover shadow-lg border border-gray-100/50 relative overflow-hidden group">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 to-rose-300"></div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-rose-500 to-rose-600 w-12 h-12 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-check text-white text-lg"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ $totalCitas ?? 0 }}</p>
                                <span class="inline-block px-3 py-1 text-xs font-medium text-rose-600 bg-rose-50 rounded-full mt-1">
                                    {{ $citasHoy ?? 0 }} programadas hoy
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Sistema de Citas</h3>
                        <p class="text-sm text-gray-500">Agenda digitalizada</p>
                        <div class="mt-4 pt-4 border-t border-gray-100/50">
                            <a href="{{ route('citas.index') }}" class="group inline-flex items-center text-rose-600 hover:text-rose-800 text-sm font-semibold transition-colors">
                                <span>Ver calendario</span>
                                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions Section -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100/50 overflow-hidden">
                            <div class="p-6 border-b border-gray-100/50">
                                <h3 class="text-xl font-bold gradient-text flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#9d2449] to-[#be185d] rounded-lg flex items-center justify-center mr-3 icon-pulse">
                                        <i class="fas fa-bolt text-white"></i>
                                    </div>
                                    Acciones Rápidas
                                </h3>
                                <p class="text-gray-500 text-sm mt-2">Herramientas principales del sistema</p>
                            </div>
                            <div class="divide-y divide-gray-100/50">
                                <!-- Mis Trámites -->
                                <a href="{{ route('tramites.solicitante.index') }}" class="group block p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <i class="fas fa-file-lines text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-800 group-hover:gradient-text transition-colors">Mis Trámites</h4>
                                            <p class="text-sm text-gray-500 mt-1">Gestiona y da seguimiento a tus solicitudes</p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-xs text-blue-600 font-medium">Ver detalles</span>
                                                <i class="fas fa-chevron-right text-xs text-blue-600 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Estado del Proveedor -->
                                <a href="{{ route('mi-estado-proveedor.index') }}" class="group block p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <i class="fas fa-chart-line text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-800 group-hover:gradient-text transition-colors">Estado del Proveedor</h4>
                                            <p class="text-sm text-gray-500 mt-1">Consulta tu estatus y certificaciones</p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-xs text-green-600 font-medium">Verificar estado</span>
                                                <i class="fas fa-chevron-right text-xs text-green-600 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Citas -->
                                <a href="{{ route('citas.index') }}" class="group block p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform">
                                            <i class="fas fa-calendar-check text-white text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-base font-bold text-gray-800 group-hover:gradient-text transition-colors">Agendar Cita</h4>
                                            <p class="text-sm text-gray-500 mt-1">Programa tu visita para entrega de documentos</p>
                                            <div class="flex items-center mt-2">
                                                <span class="text-xs text-purple-600 font-medium">Programar cita</span>
                                                <i class="fas fa-chevron-right text-xs text-purple-600 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Assistant Section -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100/50 h-full flex flex-col justify-between overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#9d2449]/5 to-transparent"></div>
                            <div class="p-6 border-b border-gray-100/50 relative z-10">
                                <h3 class="text-xl font-bold gradient-text flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#9d2449] to-[#be185d] rounded-lg flex items-center justify-center mr-3 icon-pulse">
                                        <i class="fas fa-user-tie text-white"></i>
                                    </div>
                                    Asistente Virtual
                                </h3>
                                <p class="text-gray-500 text-sm mt-2">Tu guía en el sistema de proveedores</p>
                            </div>
                            <div class="flex-1 flex justify-center items-end relative z-10">
                                <img src="{{ asset('images/mujer_bienvenida.png') }}" 
                                     alt="Asistente Virtual" 
                                     class="w-auto h-[350px] object-contain drop-shadow-xl">
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
        function updateDateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;

            document.getElementById('currentTime').textContent = `${formattedHours}:${formattedMinutes} ${ampm}`;

            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            document.getElementById('currentDate').textContent = now.toLocaleDateString('es-ES', options);

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

        updateDateTime();
        setInterval(updateDateTime, 60000);

        const cards = document.querySelectorAll('.card-hover');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection