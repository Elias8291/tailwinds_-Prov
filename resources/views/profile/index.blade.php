@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">Mi Perfil</h2>
                </div>
                <p class="text-gray-600 text-sm">Gestiona tu información personal y seguridad</p>
            </div>

            <!-- Profile Content -->
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Información Personal -->
                    <div class="md:col-span-2">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-gray-100 p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-[#B4325E]"></i>
                                Información Personal
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <p class="text-gray-800 font-semibold">{{ auth()->user()->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                    <p class="text-gray-800 font-semibold">{{ auth()->user()->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                                    <p class="text-gray-800 font-semibold">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cambiar Contraseña -->
                    <div class="md:col-span-1">
                        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-gray-100 p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-lock mr-2 text-[#B4325E]"></i>
                                Seguridad
                            </h3>
                            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                                    <input type="password" name="current_password" id="current_password" 
                                           class="w-full rounded-lg border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20">
                                </div>
                                
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                                    <input type="password" name="password" id="password" 
                                           class="w-full rounded-lg border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20">
                                </div>
                                
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="w-full rounded-lg border-gray-300 focus:border-[#B4325E] focus:ring focus:ring-[#B4325E]/20">
                                </div>

                                <button type="submit" class="w-full bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white px-4 py-2 rounded-xl shadow-lg hover:from-[#93264B] hover:to-[#B4325E] transition-all duration-300 transform hover:scale-105 focus:ring-2 focus:ring-[#B4325E]/20 text-sm font-semibold">
                                    Actualizar Contraseña
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mensajes de éxito o error -->
                @if (session('status'))
                    <div class="mt-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-100">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-100">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 