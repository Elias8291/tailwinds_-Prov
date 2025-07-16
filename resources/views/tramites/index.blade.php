@extends('layouts.app')

@section('content')
@push('styles')
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
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .text-shadow {
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
</style>
@endpush

<div class="min-h-screen p-4 sm:p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="mb-3">
                <div class="inline-flex items-center gap-2 bg-primary text-white px-3 py-1.5 rounded-full text-xs font-semibold mb-3">
                    <i class="fas fa-file-alt text-xs"></i>
                    <span>Gestión de Tramites</span>
                </div>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold gradient-text mb-3 text-shadow">
                Tramites
            </h1>
            <div class="w-16 h-0.5 bg-primary mx-auto mb-3 rounded-full"></div>
            <p class="text-sm sm:text-base text-gray-600 max-w-2xl mx-auto px-4">
                Selecciona el tipo de tramite que deseas realizar.
            </p>
        </div>

        <!-- Cards Container -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 max-w-4xl mx-auto">
            
            <!-- Card 1: Inscripción - Clean Design -->
            <div class="group relative overflow-hidden rounded-xl bg-white border border-gray-200 p-4 sm:p-5 shadow-sm transform transition-all duration-300 hover:scale-105 card-hover">
                <!-- Icon -->
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-primary text-base"></i>
                    </div>
                    <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">
                        Nuevo
                    </span>
                </div>

                <!-- Content -->
                <div>
                    <h3 class="text-lg font-bold mb-2 text-gray-800">Inscripción</h3>
                    <p class="text-gray-600 text-sm mb-3 leading-relaxed">
                        Registra tu empresa por primera vez en el sistema.
                    </p>
                    
                    <!-- Features List -->
                    <ul class="space-y-1.5 mb-4">
                        <li class="flex items-center text-xs text-gray-600">
                            <i class="fas fa-check text-primary text-xs mr-2"></i>
                            Perfil completo
                        </li>
                        <li class="flex items-center text-xs text-gray-600">
                            <i class="fas fa-check text-primary text-xs mr-2"></i>
                            Documentación inicial
                        </li>
                    </ul>

                    <!-- Action Button -->
                    <button class="w-full bg-primary text-white font-semibold py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors duration-300 text-sm">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Iniciar
                    </button>
                </div>
            </div>

            <!-- Card 2: Renovación - Simple Design -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transform transition-all duration-300 hover:scale-105 card-hover">
                <!-- Header -->
                <div class="border-b border-gray-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sync-alt text-primary text-base"></i>
                        </div>
                        <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">
                            Renovar
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Renovación</h3>
                    <p class="text-gray-600 text-sm mb-3 leading-relaxed">
                        Actualiza información y renueva permisos de tu empresa.
                    </p>

                    <!-- Progress Indicator -->
                    <div class="mb-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Progreso</span>
                            <span>75%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="space-y-1.5 mb-4">
                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <i class="fas fa-file-alt text-primary text-xs mr-2"></i>
                            <span class="text-xs text-gray-700">Documentos actualizados</span>
                        </div>
                        <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                            <i class="fas fa-calendar-check text-primary text-xs mr-2"></i>
                            <span class="text-xs text-gray-700">Vigencia extendida</span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button class="w-full bg-primary text-white font-semibold py-2 px-3 rounded-lg hover:bg-primary-dark transition-all duration-300 text-sm">
                        <i class="fas fa-play mr-2"></i>
                        Continuar
                    </button>
                </div>
            </div>

            <!-- Card 3: Actualización - Minimal Design -->
            <div class="relative overflow-hidden rounded-xl bg-white border border-gray-200 shadow-sm transform transition-all duration-300 hover:scale-105 card-hover">
                <!-- Content -->
                <div class="p-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-edit text-primary text-base"></i>
                        </div>
                        <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">
                            Editar
                        </span>
                    </div>

                    <!-- Title and Description -->
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Actualización</h3>
                    <p class="text-gray-600 text-sm mb-3 leading-relaxed">
                        Modifica información específica de tu empresa.
                    </p>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-gray-50 rounded-lg p-2 text-center border border-gray-200 hover:border-primary hover:bg-primary/5 transition-all duration-300 cursor-pointer">
                            <i class="fas fa-map-marker-alt text-primary text-xs mb-1"></i>
                            <div class="text-xs text-gray-700">Dirección</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2 text-center border border-gray-200 hover:border-primary hover:bg-primary/5 transition-all duration-300 cursor-pointer">
                            <i class="fas fa-phone text-primary text-xs mb-1"></i>
                            <div class="text-xs text-gray-700">Contacto</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2 text-center border border-gray-200 hover:border-primary hover:bg-primary/5 transition-all duration-300 cursor-pointer">
                            <i class="fas fa-briefcase text-primary text-xs mb-1"></i>
                            <div class="text-xs text-gray-700">Actividades</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2 text-center border border-gray-200 hover:border-primary hover:bg-primary/5 transition-all duration-300 cursor-pointer">
                            <i class="fas fa-users text-primary text-xs mb-1"></i>
                            <div class="text-xs text-gray-700">Personal</div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button class="w-full bg-primary text-white font-semibold py-2 px-3 rounded-lg hover:bg-primary-dark transition-colors duration-300 text-sm">
                        <i class="fas fa-cog mr-2"></i>
                        Gestionar
                    </button>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="mt-6 sm:mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5 max-w-4xl mx-auto">
            <div class="text-center mb-4">
                <h2 class="text-lg sm:text-xl font-bold gradient-text mb-2">Información Importante</h2>
                <div class="w-12 h-0.5 bg-primary mx-auto mb-3 rounded-full"></div>
                <p class="text-sm text-gray-600">
                    Asegúrate de tener toda la documentación necesaria antes de iniciar.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center p-3 border border-gray-200 rounded-lg hover:border-primary transition-colors duration-300 card-hover">
                    <i class="fas fa-clock text-lg text-yellow-600 mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1 text-sm">Tiempo de Procesamiento</h3>
                    <p class="text-xs text-gray-600">3-5 días hábiles</p>
                </div>

                <div class="text-center p-3 border border-gray-200 rounded-lg hover:border-primary transition-colors duration-300 card-hover">
                    <i class="fas fa-shield-alt text-lg text-green-600 mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1 text-sm">Seguridad Garantizada</h3>
                    <p class="text-xs text-gray-600">Información confidencial</p>
                </div>

                <div class="text-center p-3 border border-gray-200 rounded-lg hover:border-primary transition-colors duration-300 card-hover">
                    <i class="fas fa-headset text-lg text-blue-600 mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1 text-sm">Soporte Disponible</h3>
                    <p class="text-xs text-gray-600">Ayuda durante el proceso</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Card hover effects for mobile
        const cards = document.querySelectorAll('.card-hover');
        
        cards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-1px) scale(1.01)';
            });
            
            card.addEventListener('touchend', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });
</script>
@endpush
@endsection
