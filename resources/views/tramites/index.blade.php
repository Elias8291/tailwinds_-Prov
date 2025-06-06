@extends('layouts.app')

@section('content')
<style>
    @keyframes slideInDown {
        from { opacity: 0; transform: translateY(-100%); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideOutUp {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-100%); }
    }
    .notification-slide-in {
        animation: slideInDown 0.5s ease-out forwards;
    }
    .notification-slide-out {
        animation: slideOutUp 0.5s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-animate {
        opacity: 0;
        animation: fadeIn 0.6s cubic-bezier(0.21, 0.6, 0.35, 1) forwards;
    }
    .card-animate:nth-child(1) { animation-delay: 0.1s; }
    .card-animate:nth-child(2) { animation-delay: 0.2s; }
    .card-animate:nth-child(3) { animation-delay: 0.3s; }

    .card-custom {
        background-color: rgba(255, 255, 255, 1);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    .card-custom:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    }

    .gradient-inscripcion,
    .gradient-renovacion,
    .gradient-actualizacion {
        background: linear-gradient(135deg, rgba(243, 247, 255, 0.8) 0%, rgba(255, 255, 255, 0.95) 100%);
        border-left: 4px solid #B4325E;
        backdrop-filter: blur(8px);
    }

    .card-border-inscripcion,
    .card-border-renovacion,
    .card-border-actualizacion {
        border: 1px solid rgba(180, 50, 94, 0.1);
    }

    .upload-area {
        border: 2px dashed rgba(180, 50, 94, 0.2);
        transition: all 0.3s ease;
    }
    .upload-area:hover {
        border-color: #B4325E;
        background-color: rgba(180, 50, 94, 0.05);
    }
</style>

<div class="min-h-screen py-6 bg-gray-50/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Notificaciones -->
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm">
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 class="card-custom rounded-lg shadow-lg border-l-4 border-[#B4325E] p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-[#B4325E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </div>

        <!-- Encabezado -->
        <div class="card-custom rounded-2xl shadow-md p-6 md:p-8 mb-8 transform transition-all duration-300 border border-gray-100/50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6">
                <div class="flex items-center space-x-4 md:space-x-6">
                    <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-2xl p-4 shadow-lg">
                        <svg class="w-7 h-7 text-white/90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                            Trámites Disponibles
                        </h2>
                        <p class="text-gray-500 mt-1 text-sm md:text-base">Seleccione el tipo de trámite que desea realizar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Constancia de Situación Fiscal Card -->
        <div class="mb-8">
            <div class="card-custom rounded-xl p-4 md:p-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-[#B4325E] mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Constancia de Situación Fiscal</h3>
                </div>
                
                <div class="upload-area rounded-lg p-4 text-center cursor-pointer">
                    <input type="file" accept=".pdf" class="hidden" id="fiscal-doc">
                    <label for="fiscal-doc" class="cursor-pointer">
                        <svg class="w-8 h-8 text-[#B4325E] mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm text-gray-600">Haga clic para subir su constancia en PDF</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach($tramites as $tramite)
            <div class="card-animate group">
                <div class="card-custom rounded-xl overflow-hidden transition-all duration-300 group-hover:shadow-lg group-hover:transform group-hover:scale-[1.02] card-border-{{ $tramite['tipo'] }} relative">
                    <div class="gradient-{{ $tramite['tipo'] }} p-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-[#B4325E]/5 rounded-full -mr-8 -mt-8 flex items-center justify-center">
                            @if($tramite['tipo'] === 'inscripcion')
                            <svg class="w-8 h-8 text-[#B4325E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            @elseif($tramite['tipo'] === 'renovacion')
                            <svg class="w-8 h-8 text-[#B4325E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            @else
                            <svg class="w-8 h-8 text-[#B4325E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            @endif
                        </div>
                        
                        <div class="relative">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">
                                {{ $tramite['nombre'] }}
                            </h3>
                            <div class="flex items-center">
                                <span class="text-xs font-medium bg-[#B4325E]/10 text-[#B4325E] rounded-lg px-2.5 py-0.5">
                                    @if($tramite['tipo'] === 'inscripcion')
                                        Nuevo registro
                                    @elseif($tramite['tipo'] === 'renovacion')
                                        Extensión
                                    @else
                                        Modificar datos
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-white">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-2">{{ $tramite['descripcion'] }}</p>
                        
                        <div class="flex justify-end">
                            <a href="{{ route('tramites.create', ['tipo' => $tramite['tipo']]) }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white shadow-sm hover:shadow-md transition-all duration-200 bg-[#B4325E] hover:bg-[#93264B]">
                                Iniciar
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.getElementById('fiscal-doc').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const fileName = e.target.files[0].name;
        const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2);
        e.target.nextElementSibling.querySelector('span').textContent = `${fileName} (${fileSize} MB)`;
    }
});
</script>

@endsection 