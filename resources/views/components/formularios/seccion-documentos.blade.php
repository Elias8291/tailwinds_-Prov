@props(['tipoPersona'])

@php
$documentos = \App\Models\Documento::where(function($query) use ($tipoPersona) {
    $query->where('tipo_persona', $tipoPersona)
          ->orWhere('tipo_persona', 'Ambas');
})->where('es_visible', true)->get();
@endphp

<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
    <!-- Encabezado con icono y formulario integrado -->
    <div class="space-y-8">
        <div class="flex items-center space-x-4 pb-6 border-b border-gray-100">
            <div class="h-12 w-12 flex items-center justify-center rounded-xl bg-[#9d2449] text-white shadow-md transform transition-all duration-300 hover:scale-105 hover:shadow-lg hover:bg-[#8a203f]">
                <i class="fas fa-file-upload text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Documentos Requeridos</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Documentos necesarios para {{ $tipoPersona === 'Física' ? 'persona física' : 'persona moral' }}
                </p>
            </div>
        </div>

        <form class="space-y-6">
            <input type="hidden" name="action" value="next">
            <input type="hidden" name="seccion" value="6">
            <input type="hidden" name="tipo_persona" value="{{ $tipoPersona }}">

            <!-- Lista de Documentos -->
            <div class="space-y-6">
                @forelse($documentos as $documento)
                    <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-[#9d2449]/50 transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-file-pdf text-[#9d2449] text-2xl mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $documento->nombre }}</h4>
                                    <p class="text-xs text-gray-500">{{ $documento->descripcion ?? 'PDF, máximo 10MB' }}</p>
                                </div>
                            </div>
                            <input type="file" 
                                   name="documento_{{ $documento->id }}" 
                                   accept=".pdf"
                                   class="hidden" 
                                   id="documento_{{ $documento->id }}"
                                   required>
                            <label for="documento_{{ $documento->id }}" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-[#9d2449] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9d2449] cursor-pointer transition-all duration-300">
                                Seleccionar archivo
                            </label>
                        </div>
                        <div id="preview_documento_{{ $documento->id }}" class="hidden">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-[#9d2449] mr-2"></i>
                                    <span class="text-sm text-gray-900 font-medium file-name">nombre_archivo.pdf</span>
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-800 transition-colors duration-300" onclick="removeFile('documento_{{ $documento->id }}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <i class="fas fa-exclamation-circle text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500">No hay documentos configurados para este tipo de persona.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los inputs de tipo file
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        const previewId = 'preview_' + input.id;
        const preview = document.getElementById(previewId);
        
        if (input && preview) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar el tamaño del archivo (10MB máximo)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                        this.value = '';
                        return;
                    }
                    
                    // Validar el tipo de archivo
                    if (!file.type.includes('pdf')) {
                        alert('Solo se permiten archivos PDF.');
                        this.value = '';
                        return;
                    }

                    preview.querySelector('.file-name').textContent = file.name;
                    preview.classList.remove('hidden');
                }
            });
        }
    });
});

function removeFile(inputId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById('preview_' + inputId);
    
    if (input && preview) {
        input.value = '';
        preview.classList.add('hidden');
    }
}
</script>
@endpush 