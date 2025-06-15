@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
@endpush

<div class="min-h-screen py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
        <!-- Profile Header Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-4 sm:mb-8 border border-gray-100">


            <!-- Profile Info -->
            <div class="flex flex-col items-center pt-8 pb-8">
                <!-- Avatar -->
                <div class="w-32 h-32 sm:w-44 sm:h-44 border-4 sm:border-6 border-white rounded-full bg-gradient-to-br from-[#B4325E] to-[#93264B] flex items-center justify-center shadow-2xl ring-4 ring-gray-100">
                    <span class="text-4xl sm:text-6xl font-bold text-white">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
                
                <!-- Name and Title -->
                <div class="flex items-center space-x-2 mt-4">
                    <h1 class="text-xl sm:text-3xl font-bold text-gray-800 text-center">{{ auth()->user()->name ?? 'Usuario' }}</h1>
                    <span class="bg-[#B4325E] rounded-full p-1" title="Usuario Verificado">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-white h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                </div>
                
                <p class="text-gray-600 font-medium text-center">{{ auth()->user()->email ?? 'email@ejemplo.com' }}</p>
                <p class="text-sm text-gray-500 text-center px-4">Padrón de Proveedores del Estado de Oaxaca</p>
                

            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-4 sm:space-y-8">
            <!-- Personal Info Card -->
            <div class="bg-white rounded-3xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center mb-4 sm:mb-6">
                    <i class="fas fa-user-circle mr-2 sm:mr-3 text-[#B4325E]"></i>
                    Información Personal
                </h3>
                <ul class="space-y-3 sm:space-y-4 text-gray-700">
                    <li class="flex flex-col sm:flex-row border-b pb-2">
                        <span class="font-bold w-full sm:w-24 text-gray-600">Nombre:</span>
                        <span class="text-gray-800 break-words">{{ auth()->user()->name ?? 'N/A' }}</span>
                    </li>
                    <li class="flex flex-col sm:flex-row border-b pb-2">
                        <span class="font-bold w-full sm:w-24 text-gray-600">Email:</span>
                        <span class="text-gray-800 break-words">{{ auth()->user()->correo ?? auth()->user()->email ?? 'N/A' }}</span>
                    </li>
                    <li class="flex flex-col sm:flex-row border-b pb-2">
                        <span class="font-bold w-full sm:w-24 text-gray-600">RFC:</span>
                        <span class="text-gray-800 break-words">{{ auth()->user()->rfc ?? 'N/A' }}</span>
                    </li>
                    <li class="flex flex-col sm:flex-row border-b pb-2">
                        <span class="font-bold w-full sm:w-24 text-gray-600">Registro:</span>
                        <span class="text-gray-800">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d/m/Y') : 'N/A' }}</span>
                    </li>
                    <li class="flex flex-col sm:flex-row border-b pb-2">
                        <span class="font-bold w-full sm:w-24 text-gray-600">Último:</span>
                        <span class="text-gray-800 text-sm sm:text-base">{{ auth()->user()->ultimo_acceso ? auth()->user()->ultimo_acceso->format('d/m/Y H:i') : 'N/A' }}</span>
                    </li>
                    <li class="flex flex-col sm:flex-row border-b pb-2">
                        <span class="font-bold w-full sm:w-24 text-gray-600">Estado:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1 sm:mt-0 w-fit {{ auth()->user()->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(auth()->user()->estado ?? 'activo') }}
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Statistics Section -->
            <div class="bg-white rounded-3xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center mb-4 sm:mb-6">
                    <i class="fas fa-chart-bar mr-2 sm:mr-3 text-[#B4325E]"></i>
                    Estadísticas del Sistema
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <!-- Total Trámites -->
                    <div class="px-4 py-4 sm:px-6 sm:py-6 bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold text-sm text-indigo-600">Mis Trámites</span>
                            <span class="text-xs bg-indigo-200 text-indigo-700 px-2 py-1 rounded-lg">Total</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-500 bg-opacity-20 rounded-full">
                                <i class="fas fa-file-alt text-indigo-600 text-xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-indigo-600">
                                    {{ auth()->user()->solicitante ? auth()->user()->solicitante->tramites()->count() : 0 }}
                                </div>
                                <div class="text-xs text-gray-500">Trámites realizados</div>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos -->
                    <div class="px-4 py-4 sm:px-6 sm:py-6 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold text-sm text-green-600">Documentos</span>
                            <span class="text-xs bg-green-200 text-green-700 px-2 py-1 rounded-lg">Subidos</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center justify-center w-12 h-12 bg-green-500 bg-opacity-20 rounded-full">
                                <i class="fas fa-folder text-green-600 text-xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ auth()->user()->solicitante ? auth()->user()->solicitante->documentosSolicitante()->count() : 0 }}
                                </div>
                                <div class="text-xs text-gray-500">Archivos subidos</div>
                            </div>
                        </div>
                    </div>

                    <!-- Citas -->
                    <div class="px-4 py-4 sm:px-6 sm:py-6 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-bold text-sm text-blue-600">Citas</span>
                            <span class="text-xs bg-blue-200 text-blue-700 px-2 py-1 rounded-lg">Programadas</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-500 bg-opacity-20 rounded-full">
                                <i class="fas fa-calendar text-blue-600 text-xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ auth()->user()->citas()->count() }}
                                </div>
                                <div class="text-xs text-gray-500">Citas totales</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="bg-white rounded-3xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center mb-4 sm:mb-6">
                    <i class="fas fa-lock mr-2 sm:mr-3 text-[#B4325E]"></i>
                    Cambiar Contraseña
                </h3>
                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="w-full rounded-xl border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" 
                               class="w-full rounded-xl border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20">
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="w-full rounded-xl border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white px-4 py-3 rounded-xl shadow-lg hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 transform hover:scale-105 focus:ring-2 focus:ring-[#B4325E]/20 font-semibold">
                        <i class="fas fa-key mr-2"></i>
                        Actualizar Contraseña
                    </button>
                </form>
            </div>

            <!-- About Section -->
            <div class="bg-white rounded-3xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center mb-4 sm:mb-6">
                    <i class="fas fa-info-circle mr-2 sm:mr-3 text-[#B4325E]"></i>
                    Acerca de
                </h3>
                <p class="text-gray-700 leading-relaxed">
                    Bienvenido al Padrón de Proveedores del Estado de Oaxaca. Este sistema te permite gestionar tu información como proveedor, 
                    realizar trámites de registro, renovación y actualización de datos, así como programar citas y dar seguimiento a tus solicitudes.
                    Mantén tu información actualizada para poder participar en los procesos de contratación del gobierno estatal.
                </p>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white rounded-3xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center mb-4 sm:mb-6">
                    <i class="fas fa-history mr-2 sm:mr-3 text-[#B4325E]"></i>
                    Actividad Reciente
                </h3>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <!-- Timeline Item -->
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="w-8 h-8 bg-[#B4325E] rounded-full flex items-center justify-center z-10 shadow-lg">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Perfil actualizado</p>
                            <p class="text-xs text-gray-500">Hace 2 minutos</p>
                        </div>
                    </div>

                    <!-- Timeline Item -->
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center z-10 shadow-lg">
                            <i class="fas fa-file text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Nuevo documento subido</p>
                            <p class="text-xs text-gray-500">Hace 1 hora</p>
                        </div>
                    </div>

                    <!-- Timeline Item -->
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center z-10 shadow-lg">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Trámite aprobado</p>
                            <p class="text-xs text-gray-500">Hace 2 días</p>
                        </div>
                    </div>

                    <!-- Timeline Item -->
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center z-10 shadow-lg">
                            <i class="fas fa-calendar text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Cita programada</p>
                            <p class="text-xs text-gray-500">Hace 1 semana</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if (session('status'))
            <div class="mt-6 p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 p-4 rounded-xl bg-red-50 text-red-700 border border-red-200 shadow-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle mr-2 mt-0.5"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 