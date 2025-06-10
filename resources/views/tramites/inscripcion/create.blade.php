@extends('tramites.create')

@section('tipo_tramite_header')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-2.5 shadow-md">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-emerald-600">Inscripción al Padrón</h2>
            <p class="text-sm text-gray-500">Complete todos los campos requeridos para su registro inicial</p>
        </div>
    </div>
    <div class="text-sm px-3 py-1 rounded-full bg-emerald-100 text-emerald-800">
        Nuevo Registro
    </div>
</div>
@endsection

@section('form_action')
    action="{{ route('tramites.store', ['tipo_tramite' => 'inscripcion']) }}"
@endsection

@section('additional_fields')
    <!-- Campos específicos para inscripción -->
    <input type="hidden" name="es_nuevo_registro" value="1">
    <input type="hidden" name="isPersonaFisica" value="{{ $datosTramite['tipo_persona'] === 'Física' ? 'true' : 'false' }}">
@endsection 