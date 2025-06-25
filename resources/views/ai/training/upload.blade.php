@extends('layouts.app')

@section('title', 'Subir Documentos - Entrenamiento IA')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">📤 Subir Documentos para Entrenamiento</h1>
                <p class="text-gray-600 mt-2">Sube documentos de ejemplo para entrenar el modelo de reconocimiento</p>
            </div>
            <a href="{{ route('ai.training.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Formulario de Subida -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <form id="uploadForm" onsubmit="handleUpload(event)">
                @csrf
                
                <!-- Selección de Tipo de Documento -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">1. Selecciona el Tipo de Documento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($documentTypes as $docType)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="document_type_id" value="{{ $docType->id }}" 
                                       class="sr-only peer" required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg transition-all duration-200 peer-checked:border-primary peer-checked:bg-primary-50 hover:border-primary-300">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 peer-checked:border-primary peer-checked:bg-primary flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $docType->nombre }}</h4>
                                            @if($docType->descripcion)
                                                <p class="text-sm text-gray-600 mt-1">{{ $docType->descripcion }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Zona de Arrastrar y Soltar -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">2. Sube los Documentos</h3>
                    
                    <div id="dropZone" 
                         class="border-2 border-dashed border-gray-300 rounded-xl p-12 text-center transition-colors duration-200 hover:border-primary-400 hover:bg-primary-50">
                        <div id="dropContent">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4"/>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">Arrastra archivos aquí</p>
                            <p class="text-gray-600 mb-4">o</p>
                            <button type="button" onclick="document.getElementById('fileInput').click()"
                                    class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Seleccionar Archivos
                            </button>
                            <input type="file" id="fileInput" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        </div>
                    </div>
                    
                    <!-- Información de Archivos -->
                    <div class="mt-4 text-sm text-gray-600">
                        <p>• Formatos permitidos: PDF, JPG, JPEG, PNG</p>
                        <p>• Tamaño máximo por archivo: 10MB</p>
                        <p>• Máximo 50 archivos por subida</p>
                    </div>
                </div>

                <!-- Lista de Archivos Seleccionados -->
                <div id="fileList" class="hidden mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivos Seleccionados</h3>
                    <div id="fileListContent" class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        <!-- Los archivos se mostrarán aquí -->
                    </div>
                </div>

                <!-- Descripción Opcional -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">3. Descripción (Opcional)</h3>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              placeholder="Agrega notas sobre estos documentos de entrenamiento..."></textarea>
                </div>

                <!-- Consejos -->
                <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">💡 Consejos para mejores resultados:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Incluye documentos de buena calidad y legibles</li>
                        <li>• Sube ejemplos variados del mismo tipo de documento</li>
                        <li>• Asegúrate de que los documentos correspondan al tipo seleccionado</li>
                        <li>• Se recomienda subir al menos 20-30 documentos por tipo para mejor precisión</li>
                    </ul>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="resetForm()"
                            class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Limpiar
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Subir Documentos
                    </button>
                </div>
            </form>
        </div>

        <!-- Progreso de Subida -->
        <div id="uploadProgress" class="hidden mt-8 bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mr-4"></div>
                <h3 class="text-lg font-semibold text-gray-900">🚀 Subiendo Documentos...</h3>
            </div>
            
            <!-- Progreso General -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span id="progressText">Preparando subida...</span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div id="progressBar" class="bg-primary h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
            
            <!-- Progreso Individual de Archivos -->
            <div id="fileProgressContainer" class="space-y-3 max-h-64 overflow-y-auto">
                <!-- Los archivos individuales se mostrarán aquí -->
            </div>
            
            <!-- Resultados -->
            <div id="uploadResults" class="mt-6 space-y-2"></div>
            
            <!-- Botón de Continuar (aparece al finalizar) -->
            <div id="continueSection" class="hidden mt-6 text-center">
                <button onclick="goToReview()" 
                        class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Revisar Documentos Subidos
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedFiles = [];
let isUploading = false;

document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    
    // Prevenir comportamientos por defecto
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Efectos visuales
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    // Manejar drop
    dropZone.addEventListener('drop', handleDrop, false);
    
    // Manejar selección de archivos
    fileInput.addEventListener('change', handleFileSelect, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    document.getElementById('dropZone').classList.add('border-primary-500', 'bg-primary-100');
}

function unhighlight(e) {
    document.getElementById('dropZone').classList.remove('border-primary-500', 'bg-primary-100');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

function handleFileSelect(e) {
    const files = e.target.files;
    handleFiles(files);
}

function handleFiles(files) {
    if (files.length > 50) {
        showAlert('No puedes subir más de 50 archivos a la vez', 'error');
        return;
    }
    
    selectedFiles = Array.from(files).filter(file => {
        const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!validTypes.includes(file.type)) {
            showAlert(`El archivo ${file.name} no es un tipo válido`, 'error');
            return false;
        }
        
        if (file.size > maxSize) {
            showAlert(`El archivo ${file.name} es demasiado grande (máx 10MB)`, 'error');
            return false;
        }
        
        return true;
    });
    
    displayFileList();
    updateSubmitButton();
}

function displayFileList() {
    const fileList = document.getElementById('fileList');
    const fileListContent = document.getElementById('fileListContent');
    
    if (selectedFiles.length === 0) {
        fileList.classList.add('hidden');
        return;
    }
    
    fileList.classList.remove('hidden');
    
    fileListContent.innerHTML = selectedFiles.map((file, index) => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-xs font-medium text-primary">${getFileIcon(file.type)}</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900">${file.name}</p>
                    <p class="text-sm text-gray-500">${formatFileSize(file.size)}</p>
                </div>
            </div>
            <button type="button" onclick="removeFile(${index})" 
                    class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `).join('');
}

function getFileIcon(type) {
    if (type === 'application/pdf') return 'PDF';
    if (type.startsWith('image/')) return 'IMG';
    return 'DOC';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    displayFileList();
    updateSubmitButton();
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    const hasFiles = selectedFiles.length > 0;
    const hasDocumentType = document.querySelector('input[name="document_type_id"]:checked');
    
    submitBtn.disabled = !hasFiles || !hasDocumentType || isUploading;
}

// Actualizar botón cuando se selecciona tipo de documento
document.addEventListener('change', function(e) {
    if (e.target.name === 'document_type_id') {
        updateSubmitButton();
    }
});

async function handleUpload(event) {
    event.preventDefault();
    
    if (selectedFiles.length === 0) {
        showAlert('Debe seleccionar al menos un archivo', 'error');
        return;
    }
    
    const documentTypeId = document.querySelector('input[name="document_type_id"]:checked')?.value;
    if (!documentTypeId) {
        showAlert('Debe seleccionar un tipo de documento', 'error');
        return;
    }
    
    isUploading = true;
    updateSubmitButton();
    
    // Mostrar progreso
    document.getElementById('uploadProgress').classList.remove('hidden');
    
    const description = document.querySelector('textarea[name="description"]').value;
    
    try {
        updateProgress(0, 'Iniciando subida fragmentada...');
        
        // Subir archivos uno por uno
        const uploadResults = await uploadFilesSequentially(selectedFiles, documentTypeId, description);
        
        updateProgress(100, 'Todos los archivos procesados');
        document.getElementById('progressPercent').textContent = '100%';
        
        // Mostrar resultados finales
        showFinalResults(uploadResults);
        
        // Mostrar botón para continuar
        document.getElementById('continueSection').classList.remove('hidden');
        
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error durante la subida', 'error');
    } finally {
        isUploading = false;
        updateSubmitButton();
    }
}

async function uploadFilesSequentially(files, documentTypeId, description) {
    const results = [];
    const totalFiles = files.length;
    
    // Preparar contenedor de progreso individual
    const container = document.getElementById('fileProgressContainer');
    container.innerHTML = '';
    
    for (let i = 0; i < totalFiles; i++) {
        const file = files[i];
        const fileProgress = createFileProgressElement(file, i);
        container.appendChild(fileProgress);
        
        try {
            // Actualizar progreso general
            const overallProgress = (i / totalFiles) * 100;
            updateProgress(overallProgress, `Subiendo archivo ${i + 1} de ${totalFiles}...`);
            
            // Marcar archivo como "subiendo"
            updateFileProgress(i, 'uploading', 'Subiendo...');
            
            const result = await uploadSingleFile(file, documentTypeId, description);
            
            if (result.success) {
                updateFileProgress(i, 'success', 'Completado');
                results.push({ file: file.name, status: 'success', data: result.data });
                
                // Pequeña pausa para mejor UX
                await sleep(200);
            } else {
                updateFileProgress(i, 'error', result.message || 'Error');
                results.push({ file: file.name, status: 'error', message: result.message });
            }
            
        } catch (error) {
            updateFileProgress(i, 'error', 'Error de conexión');
            results.push({ file: file.name, status: 'error', message: error.message });
        }
    }
    
    return results;
}

async function uploadSingleFile(file, documentTypeId, description) {
    const formData = new FormData();
    formData.append('document_type_id', documentTypeId);
    formData.append('description', description);
    formData.append('files[0]', file);
    
    const response = await fetch('{{ route("ai.training.upload.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    });
    
    return await response.json();
}

function createFileProgressElement(file, index) {
    const div = document.createElement('div');
    div.id = `file-progress-${index}`;
    div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border';
    
    div.innerHTML = `
        <div class="flex items-center">
            <div id="file-icon-${index}" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                <span class="text-xs font-medium text-gray-600">${getFileIcon(file.type)}</span>
            </div>
            <div>
                <p class="font-medium text-gray-900 text-sm">${file.name}</p>
                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
            </div>
        </div>
        <div class="flex items-center">
            <span id="file-status-${index}" class="text-sm text-gray-500 mr-2">En espera...</span>
            <div id="file-spinner-${index}" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
        </div>
    `;
    
    return div;
}

function updateFileProgress(index, status, message) {
    const icon = document.getElementById(`file-icon-${index}`);
    const statusText = document.getElementById(`file-status-${index}`);
    const spinner = document.getElementById(`file-spinner-${index}`);
    
    statusText.textContent = message;
    
    if (status === 'uploading') {
        icon.className = 'w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3';
        spinner.classList.remove('hidden');
        statusText.className = 'text-sm text-blue-600 mr-2';
    } else if (status === 'success') {
        icon.className = 'w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3';
        icon.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        spinner.classList.add('hidden');
        statusText.className = 'text-sm text-green-600 mr-2';
    } else if (status === 'error') {
        icon.className = 'w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3';
        icon.innerHTML = '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        spinner.classList.add('hidden');
        statusText.className = 'text-sm text-red-600 mr-2';
    }
}

function showFinalResults(results) {
    const successful = results.filter(r => r.status === 'success').length;
    const failed = results.filter(r => r.status === 'error').length;
    
    const resultsDiv = document.getElementById('uploadResults');
    
    let html = '<div class="bg-white border rounded-lg p-4">';
    
    if (successful > 0) {
        html += `
            <div class="flex items-center text-green-700 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">${successful} archivo(s) subido(s) exitosamente</span>
            </div>
        `;
    }
    
    if (failed > 0) {
        html += `
            <div class="flex items-center text-red-700 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">${failed} archivo(s) con errores</span>
            </div>
        `;
        
        const errorFiles = results.filter(r => r.status === 'error');
        if (errorFiles.length > 0) {
            html += '<div class="text-sm text-red-600 ml-7">';
            errorFiles.forEach(error => {
                html += `<p>• ${error.file}: ${error.message}</p>`;
            });
            html += '</div>';
        }
    }
    
    html += '</div>';
    resultsDiv.innerHTML = html;
    
    if (successful > 0) {
        showAlert(`¡Subida completada! ${successful} documentos listos para revisión`, 'success');
    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function goToReview() {
    window.location.href = '{{ route("ai.training.review") }}';
}

function updateProgress(percent, text) {
    document.getElementById('progressBar').style.width = `${percent}%`;
    document.getElementById('progressText').textContent = text;
    document.getElementById('progressPercent').textContent = `${Math.round(percent)}%`;
}

function showUploadResults(uploadedFiles, errors) {
    const resultsDiv = document.getElementById('uploadResults');
    
    let html = '';
    
    if (uploadedFiles && uploadedFiles.length > 0) {
        html += '<h4 class="font-semibold text-green-700 mb-2">✅ Archivos subidos exitosamente:</h4>';
        html += '<ul class="text-sm text-green-600 space-y-1 mb-4">';
        uploadedFiles.forEach(file => {
            html += `<li>• ${file.name} (${file.size})</li>`;
        });
        html += '</ul>';
    }
    
    if (errors && errors.length > 0) {
        html += '<h4 class="font-semibold text-red-700 mb-2">❌ Errores:</h4>';
        html += '<ul class="text-sm text-red-600 space-y-1">';
        errors.forEach(error => {
            html += `<li>• ${error}</li>`;
        });
        html += '</ul>';
    }
    
    resultsDiv.innerHTML = html;
}

function resetForm() {
    selectedFiles = [];
    document.getElementById('uploadForm').reset();
    document.getElementById('fileList').classList.add('hidden');
    document.getElementById('uploadProgress').classList.add('hidden');
    updateSubmitButton();
}

// Función de alerta reutilizable
function showAlert(message, type) {
    const alertColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500'
    };
    
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 ${alertColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
@endpush 