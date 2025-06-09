@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white rounded-xl shadow-lg p-8">
    <h2 class="text-2xl font-bold mb-6 text-center text-[#9d2449]">Iniciar Inscripción</h2>
    <form method="POST" action="{{ route('tramites.iniciarInscripcion') }}">
        @csrf
        <div class="mb-4">
            <label for="rfc" class="block text-sm font-medium text-gray-700">RFC</label>
            <input type="text" name="rfc" id="rfc" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre o Razón Social</label>
            <input type="text" name="nombre" id="nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="correo" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input type="email" name="correo" id="correo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div class="mb-6">
            <label for="tipo_persona" class="block text-sm font-medium text-gray-700">Tipo de persona</label>
            <select name="tipo_persona" id="tipo_persona" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Seleccione...</option>
                <option value="Física">Física</option>
                <option value="Moral">Moral</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-[#9d2449] text-white py-2 rounded-lg font-semibold hover:bg-[#7a1d37] transition">Iniciar inscripción</button>
    </form>
</div>
@endsection 