@extends('tramites.create')

@section('tipo_tramite_header')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-2.5 shadow-md">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-blue-600">Renovación de Registro</h2>
            <p class="text-sm text-gray-500">Verifique y actualice la información para renovar su registro</p>
        </div>
    </div>
    <div class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-800">
        Renovación
    </div>
</div>
@endsection

@section('form_action')
    action="{{ route('tramites.store', ['tipo_tramite' => 'renovacion']) }}"
@endsection

@section('additional_fields')
    <!-- Campos específicos para renovación -->
    <input type="hidden" name="tramite_anterior_id" value="{{ $data['detalle_tramite']->tramite_id ?? '' }}">
    <input type="hidden" name="fecha_ultimo_tramite" value="{{ $data['detalle_tramite']->created_at ?? '' }}">
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