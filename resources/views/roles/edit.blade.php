@extends('layouts.app')

@section('content')
<section class="py-1 bg-blueGray-50">
    <div class="w-full lg:w-8/12 px-4 mx-auto mt-6">
        <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-white border-0">
            <div class="rounded-t bg-white mb-0 px-6 py-6">
                <div class="text-center flex justify-between">
                    <h6 class="text-xl font-bold text-gray-700">
                        Editar Rol: {{ $role->name }}
                    </h6>
                    <a href="{{ route('roles.index') }}" 
                       class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white active:from-[#93264B] active:to-[#B4325E] font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                        Volver
                    </a>
                </div>
            </div>

            <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
                <form method="POST" action="{{ route('roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    <h6 class="text-gray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Información del Rol
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-gray-600 text-xs font-bold mb-2">
                                    Nombre del Rol
                                </label>
                                <input type="text" 
                                       name="name"
                                       value="{{ $role->name }}"
                                       class="border-0 px-3 py-3 placeholder-gray-300 text-gray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring focus:ring-[#B4325E]/50 w-full ease-linear transition-all duration-150"
                                       {{ in_array($role->name, ['admin', 'solicitante']) ? 'readonly' : '' }}
                                       required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="mt-6 border-b-1 border-gray-300">

                    <h6 class="text-gray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Permisos
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full px-4">
                            <div class="relative w-full mb-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach($permissions as $permission)
                                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-[#B4325E] transition-colors duration-200 cursor-pointer">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->name }}"
                                               class="h-4 w-4 text-[#B4325E] focus:ring-[#B4325E] border-gray-300 rounded"
                                               {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ $permission->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                                class="bg-gradient-to-r from-[#B4325E] to-[#93264B] text-white active:from-[#93264B] active:to-[#B4325E] font-bold uppercase text-xs px-6 py-3 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                            Actualizar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection 