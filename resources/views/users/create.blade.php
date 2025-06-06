@extends('layouts.app')

@section('content')
<section class="py-1 bg-blueGray-50">
    <div class="w-full lg:w-8/12 px-4 mx-auto mt-6">
        <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-white border-0">
            <div class="rounded-t bg-white mb-0 px-6 py-6">
                <div class="text-center flex justify-between">
                    <h6 class="text-xl font-bold text-gray-700">
                        Crear Nuevo Usuario
                    </h6>
                    <a href="{{ route('users.index') }}" 
                       class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white active:from-[#93264B] active:to-[#B4325E] font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                        Volver
                    </a>
                </div>
            </div>

            <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
                <!-- Errores de validación -->
                @if ($errors->any())
                <div class="mb-6">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <h6 class="text-gray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Información del Usuario
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-gray-600 text-xs font-bold mb-2">
                                    Nombre
                                </label>
                                <input type="text" 
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring focus:ring-[#B4325E]/50 w-full ease-linear transition-all duration-150"
                                       required>
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-gray-600 text-xs font-bold mb-2">
                                    Correo Electrónico
                                </label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring focus:ring-[#B4325E]/50 w-full ease-linear transition-all duration-150"
                                       required>
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-gray-600 text-xs font-bold mb-2">
                                    Contraseña
                                </label>
                                <input type="password"
                                       name="password"
                                       class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring focus:ring-[#B4325E]/50 w-full ease-linear transition-all duration-150"
                                       required>
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-gray-600 text-xs font-bold mb-2">
                                    Confirmar Contraseña
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring focus:ring-[#B4325E]/50 w-full ease-linear transition-all duration-150"
                                       required>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-6 border-b-1 border-gray-300">

                    <h6 class="text-gray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Roles
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full px-4">
                            <div class="relative w-full mb-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach($roles as $role)
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-[#B4325E] transition-colors duration-200 cursor-pointer">
                                        <input type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}"
                                               class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300 rounded"
                                               {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ $role->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                                class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white active:from-[#93264B] active:to-[#B4325E] font-bold uppercase text-xs px-6 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                            Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection 