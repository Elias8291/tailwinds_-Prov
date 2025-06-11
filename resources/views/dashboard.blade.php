@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

<style>
    .dashboard-bg-pattern {
        background-image: url('/images/logoNegro.png');
        background-repeat: repeat;
        background-size: 150px auto;
        opacity: 0.05;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
    }
</style>

<div class="min-h-screen bg-gray-100 font-montserrat py-8 relative">
    <!-- Fondo con logo -->
    <div class="dashboard-bg-pattern"></div>
    
    <!-- Contenedor principal más compacto -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 relative">
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
                    <div class="flex flex-col items-end gap-3">
                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium inline-flex items-center border border-green-200">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                            Sesión Activa
                        </span>
                        <button class="inline-flex items-center bg-[#9d2449] text-white px-4 py-2 rounded-xl shadow-lg hover:bg-[#7a1c38] transition-all duration-300 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-[#9d2449]/20 text-sm">
                            <span class="font-semibold">Iniciar trámite</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
                    <!-- Usuarios -->
                    <div class="bg-white rounded-xl p-3 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-indigo-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-gray-800">Usuarios</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-lg font-bold text-indigo-600">2,450</p>
                                    <span class="ml-2 text-xs font-medium text-green-600 bg-green-100 px-1.5 py-0.5 rounded-full">+12%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trámites -->
                    <div class="bg-white rounded-xl p-3 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-amber-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-gray-800">Trámites</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-lg font-bold text-amber-600">145</p>
                                    <span class="ml-2 text-xs font-medium text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded-full">8 nuevos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Proveedores -->
                    <div class="bg-white rounded-xl p-3 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-emerald-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-gray-800">Proveedores</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-lg font-bold text-emerald-600">324</p>
                                    <span class="ml-2 text-xs font-medium text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded-full">+15%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Citas -->
                    <div class="bg-white rounded-xl p-3 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-rose-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-gray-800">Citas</h3>
                                <div class="flex items-baseline mt-1">
                                    <p class="text-lg font-bold text-rose-600">89</p>
                                    <span class="ml-2 text-xs font-medium text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded-full">4 hoy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <!-- Quick Actions Section -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 h-full">
                            <div class="p-3 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-[#9d2449] flex items-center">
                                    <i class="fa-solid fa-bolt mr-2"></i>
                                    Acciones Rápidas
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <!-- Subir Documentos -->
                                <div class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-cloud-arrow-up text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Subir Documentos</h4>
                                            <p class="text-xs text-gray-500">Carga tus documentos oficiales</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado de Registro -->
                                <div class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-clipboard-check text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Estado de Registro</h4>
                                            <p class="text-xs text-gray-500">Consulta tu proceso</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Directorio -->
                                <div class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-building-user text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Directorio</h4>
                                            <p class="text-xs text-gray-500">Encuentra proveedores locales</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ayuda -->
                                <div class="group p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <i class="fa-solid fa-circle-question text-xl text-[#9d2449]"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#9d2449] transition-colors">Ayuda</h4>
                                            <p class="text-xs text-gray-500">Nuestro equipo está aquí para asistirte en tu proceso de registro y resolver tus dudas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assistant Section -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 h-full flex flex-col justify-between overflow-hidden">
                            <div class="mt-auto flex justify-center items-end h-full bg-white">
                                <img src="{{ asset('images/mujer_bienvenida.png') }}" alt="Asistente" class="w-auto h-[350px] object-contain">
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