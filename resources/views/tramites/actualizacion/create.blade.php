@extends('tramites.create')

@section('tipo_tramite_header')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-2.5 shadow-md">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-amber-600">Actualización de Datos</h2>
            <p class="text-sm text-gray-500">Modifique únicamente los campos que necesita actualizar</p>
        </div>
    </div>
    <div class="text-sm px-3 py-1 rounded-full bg-amber-100 text-amber-800">
        Actualización
    </div>
</div>
@endsection

@section('form_action')
    action="{{ route('tramites.store', ['tipo_tramite' => 'actualizacion']) }}"
@endsection

@section('additional_fields')
    <!-- Campos específicos para actualización -->
    <input type="hidden" name="tramite_anterior_id" value="{{ $data['detalle_tramite']->tramite_id ?? '' }}">
    <div class="mb-6">
        <label for="motivo_actualizacion" class="block text-sm font-medium text-gray-700 mb-1">
            Motivo de la Actualización <span class="text-red-500">*</span>
        </label>
        <textarea
            id="motivo_actualizacion"
            name="motivo_actualizacion"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
            required
        ></textarea>
        <p class="mt-1 text-xs text-gray-500">Describa brevemente el motivo por el cual necesita actualizar sus datos.</p>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formData', () => ({
            init() {
                this.$nextTick(() => {
                    // Pre-cargar datos del trámite anterior
                    if (this.formData.detalle_tramite) {
                        this.fillFormFields();
                    }
                });
            }
        }));
    });
</script>
@endpush 