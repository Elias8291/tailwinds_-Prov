@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full overflow-x-hidden bg-gradient-to-br from-gray-50 to-white">
    <div class="py-6 px-3 sm:px-4">
        <div class="w-full max-w-4xl mx-auto">
            <!-- Formulario -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-100/50">
                <!-- Errores de validación -->
                @if ($errors->any())
                <div class="p-4 border-b border-gray-100">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
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

                <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="divide-y divide-gray-100">
                    @csrf
                    @method('PUT')

                    <!-- Encabezado -->
                    <div class="p-6">
                        <div class="flex flex-col items-center text-center">
                            <div class="bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl p-3.5 shadow-lg mb-4 transform transition-all duration-300 hover:scale-110 hover:rotate-3">
                                <i class="fas fa-file-alt text-white text-2xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent mb-2">
                                Editar Documento
                            </h2>
                            <p class="text-sm text-gray-600">Modifique la información y archivos del documento</p>
                        </div>
                    </div>

                    <!-- Información del Documento -->
                    <div class="p-6">
                        <div class="flex flex-col items-center mb-6">
                            <h3 class="text-lg font-semibold bg-gradient-to-r from-[#B4325E] to-[#93264B] bg-clip-text text-transparent">
                                Información del Documento
                            </h3>
                            <div class="w-32 h-0.5 bg-gradient-to-r from-[#B4325E] to-[#93264B] mt-2 rounded-full opacity-50"></div>
                        </div>

                        <div class="w-full max-w-2xl mx-auto space-y-6">
                            <!-- Título del Documento -->
                            <div class="form-group">
                                <div class="relative group">
                                    <input type="text" 
                                           id="title"
                                           name="title"
                                           value="{{ old('title', $document->title) }}"
                                           class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300 group-hover:border-gray-300"
                                           placeholder="Título del Documento"
                                           required>
                                    <label for="title" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Título del Documento<span class="text-[#B4325E] ml-1">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <div class="relative group">
                                    <textarea id="description"
                                              name="description"
                                              rows="4"
                                              class="peer w-full px-4 py-3 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300 group-hover:border-gray-300 resize-none"
                                              placeholder="Descripción del Documento"
                                              required>{{ old('description', $document->description) }}</textarea>
                                    <label for="description" 
                                           class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300
                                                  peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                                                  peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-placeholder-shown:bg-transparent
                                                  peer-focus:-top-2.5 peer-focus:left-2 peer-focus:bg-white 
                                                  peer-focus:text-[#B4325E] peer-focus:text-sm">
                                        Descripción<span class="text-[#B4325E] ml-1">*</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Categoría -->
                                <div class="form-group">
                                    <div class="relative group">
                                        <select id="category"
                                                name="category_id"
                                                class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 appearance-none focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300 group-hover:border-gray-300"
                                                required>
                                            <option value="">Seleccione una categoría</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $document->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="category" 
                                               class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300">
                                            Categoría<span class="text-[#B4325E] ml-1">*</span>
                                        </label>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 group-hover:text-[#B4325E] transition-colors duration-300"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="form-group">
                                    <div class="relative group">
                                        <select id="status"
                                                name="status"
                                                class="peer w-full h-11 px-4 bg-gray-50/30 border-2 border-gray-200 rounded-lg text-gray-900 appearance-none focus:outline-none focus:border-[#B4325E] focus:bg-white transition-all duration-300 group-hover:border-gray-300"
                                                required>
                                            <option value="draft" {{ old('status', $document->status) == 'draft' ? 'selected' : '' }}>
                                                <i class="fas fa-pencil-alt"></i> Borrador
                                            </option>
                                            <option value="review" {{ old('status', $document->status) == 'review' ? 'selected' : '' }}>
                                                <i class="fas fa-eye"></i> En Revisión
                                            </option>
                                            <option value="published" {{ old('status', $document->status) == 'published' ? 'selected' : '' }}>
                                                <i class="fas fa-check-circle"></i> Publicado
                                            </option>
                                        </select>
                                        <label for="status" 
                                               class="absolute left-2 -top-2.5 px-2 bg-white text-sm text-gray-600 transition-all duration-300">
                                            Estado<span class="text-[#B4325E] ml-1">*</span>
                                        </label>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 group-hover:text-[#B4325E] transition-colors duration-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Archivo -->
                            <div class="form-group">
                                <div class="relative">
                                    <input type="file" 
                                           id="file"
                                           name="file"
                                           class="hidden"
                                           accept=".pdf,.doc,.docx">
                                    <label for="file" 
                                           class="group flex items-center justify-center w-full h-40 px-4 border-2 border-gray-200 border-dashed rounded-lg cursor-pointer bg-gray-50/30 hover:bg-gray-50 hover:border-[#B4325E] transition-all duration-300">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div class="p-4 rounded-full bg-gray-100 group-hover:bg-white group-hover:shadow-md transition-all duration-300">
                                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-[#B4325E] transform group-hover:scale-110 transition-all duration-300"></i>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium text-[#B4325E]">Click para subir</span> o arrastrar y soltar
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX (MAX. 10MB)</p>
                                            </div>
                                        </div>
                                    </label>
                                    @if($document->file_path)
                                        <div class="mt-3 flex items-center p-3 bg-gray-50 rounded-lg group">
                                            <i class="fas fa-file-alt text-[#B4325E] mr-3"></i>
                                            <span class="text-sm text-gray-600">Archivo actual: 
                                                <span class="font-medium">{{ basename($document->file_path) }}</span>
                                            </span>
                                            <button type="button" 
                                                    class="ml-auto text-gray-400 hover:text-red-500 focus:outline-none transition-colors duration-300"
                                                    onclick="if(confirm('¿Está seguro de eliminar el archivo actual?')) document.getElementById('remove_file').value = '1';">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                    <input type="hidden" name="remove_file" id="remove_file" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('documents.index') }}" 
                               class="w-full sm:w-auto group inline-flex items-center justify-center px-5 py-2.5 rounded-lg border-2 border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-300">
                                <i class="fas fa-times mr-2 text-gray-400 group-hover:text-gray-600"></i>
                                <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-900">Cancelar</span>
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto group inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-gradient-to-r from-[#B4325E] to-[#93264B] hover:from-[#93264B] hover:to-[#B4325E] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B4325E] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <i class="fas fa-save mr-2 text-white/90 group-hover:text-white"></i>
                                <span class="text-sm font-semibold text-white group-hover:text-white/90">Actualizar Documento</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Previsualización del nombre del archivo seleccionado
    document.getElementById('file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const fileInfo = document.createElement('div');
            fileInfo.className = 'mt-3 flex items-center p-3 bg-gray-50 rounded-lg';
            fileInfo.innerHTML = `
                <i class="fas fa-file-alt text-[#B4325E] mr-3"></i>
                <span class="text-sm text-gray-600">Archivo seleccionado: 
                    <span class="font-medium">${fileName}</span>
                </span>
            `;
            
            // Remover previsualización anterior si existe
            const previousPreview = this.parentElement.querySelector('.mt-3');
            if (previousPreview) {
                previousPreview.remove();
            }
            
            this.parentElement.appendChild(fileInfo);
        }
    });
</script>
@endpush 