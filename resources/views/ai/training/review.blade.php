@extends('layouts.app')

@section('title', 'Revisar Documentos - Entrenamiento IA')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">✅ Revisar Documentos de Entrenamiento</h1>
                <p class="text-gray-600 mt-2">Revisa y aprueba documentos para el entrenamiento del modelo de IA</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('ai.training.upload') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Subir Más Documentos
                </a>
                <a href="{{ route('ai.training.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        @if($pendingDocuments->count() > 0)
            <!-- Filtros y Acciones Masivas -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <select id="documentTypeFilter" onchange="filterDocuments()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Todos los tipos</option>
                            @foreach($documentTypes as $id => $nombre)
                                <option value="{{ $id }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                        
                        <span class="text-sm text-gray-500">
                            {{ $pendingDocuments->total() }} documentos pendientes
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button onclick="selectAll()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Seleccionar Todo
                        </button>
                        <button onclick="approveSelected()" id="approveSelectedBtn"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50"
                                disabled>
                            <div id="massApproveSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            <span id="massApproveText">Aprobar Seleccionados</span>
                        </button>
                        <span id="selectedCount" class="text-sm text-gray-500">0 seleccionados</span>
                    </div>
                </div>
            </div>

            <!-- Lista de Documentos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="documentsGrid">
                @foreach($pendingDocuments as $document)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden document-card" 
                         data-document-type="{{ $document->expected_document_type }}">
                        <!-- Checkbox de Selección -->
                        <div class="p-4 border-b border-gray-100">
                            <label class="flex items-center">
                                <input type="checkbox" class="document-checkbox rounded border-gray-300 text-primary focus:ring-primary" 
                                       value="{{ $document->id }}" onchange="updateSelectionCount()">
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    {{ $documentTypes[$document->expected_document_type] ?? 'Tipo Desconocido' }}
                                </span>
                            </label>
                        </div>

                        <!-- Preview del Documento -->
                        <div class="p-4">
                            <div class="aspect-w-16 aspect-h-9 mb-4">
                                <div class="w-full h-48 bg-gray-100 rounded-lg overflow-hidden">
                                    @if(str_contains($document->file_type, 'image'))
                                        <img src="{{ route('ai.training.file.serve', $document) }}" 
                                             alt="Preview" 
                                             class="w-full h-full object-cover rounded cursor-pointer"
                                             onclick="openPreview('{{ route('ai.training.file.serve', $document) }}', '{{ $document->file_name }}')">
                                    @else
                                        <div class="text-center cursor-pointer" 
                                             onclick="openPreview('{{ route('ai.training.file.serve', $document) }}', '{{ $document->file_name }}')">
                                            <div class="flex items-center justify-center h-full">
                                                <div class="text-center">
                                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    <p class="text-sm text-gray-600">{{ strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Información del Documento -->
                            <div class="space-y-2">
                                <h4 class="font-medium text-gray-900 truncate">{{ $document->file_name }}</h4>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ $document->file_size_human }}</span>
                                    <span>{{ $document->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                
                                @if($document->extracted_text)
                                    <div class="mt-3">
                                        <p class="text-xs text-gray-500 mb-1">Vista previa del texto:</p>
                                        <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded text-xs">
                                            {{ Str::limit($document->extracted_text, 100) }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Acciones -->
                            <div class="flex space-x-2 mt-4">
                                <button onclick="openPreview('{{ route('ai.training.file.serve', $document) }}', '{{ $document->file_name }}')"
                                        class="inline-flex items-center px-3 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </button>
                                <button id="approve-btn-{{ $document->id }}" 
                                        onclick="approveDocument({{ $document->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50">
                                    <svg id="approve-icon-{{ $document->id }}" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <div id="approve-spinner-{{ $document->id }}" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1"></div>
                                    <span id="approve-text-{{ $document->id }}">Aprobar</span>
                                </button>
                                <button id="reject-btn-{{ $document->id }}" 
                                        onclick="rejectDocument({{ $document->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50">
                                    <svg id="reject-icon-{{ $document->id }}" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <div id="reject-spinner-{{ $document->id }}" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1"></div>
                                    <span id="reject-text-{{ $document->id }}">Rechazar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                {{ $pendingDocuments->links() }}
            </div>
        @else
            <!-- Estado Vacío -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay documentos pendientes</h3>
                <p class="text-gray-600 mb-6">Todos los documentos han sido revisados o no hay documentos subidos</p>
                <a href="{{ route('ai.training.upload') }}" 
                   class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Subir Documentos
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Vista Previa -->
<div id="previewModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-screen overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 id="previewTitle" class="text-lg font-bold text-gray-900">Vista Previa del Documento</h3>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 max-h-96 overflow-auto">
                <div id="previewContent" class="text-center">
                    <!-- El contenido se cargará aquí -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprobación -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aprobar Documento</h3>
                <form id="approveForm" onsubmit="submitApproval(event)">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
                        <textarea id="approveNotes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Agrega notas sobre la aprobación..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            Aprobar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Rechazar Documento</h3>
                <form id="rejectForm" onsubmit="submitRejection(event)">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Razón del rechazo *</label>
                        <textarea id="rejectReason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Explica por qué se rechaza este documento..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            Rechazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
let currentDocumentId = null;
let selectedDocuments = [];

// Filtrar documentos
function filterDocuments() {
    const filter = document.getElementById('documentTypeFilter').value;
    const cards = document.querySelectorAll('.document-card');
    
    cards.forEach(card => {
        if (filter === '' || card.dataset.documentType === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Seleccionar todos los documentos visibles
function selectAll() {
    const checkboxes = document.querySelectorAll('.document-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        const card = checkbox.closest('.document-card');
        if (card.style.display !== 'none') {
            checkbox.checked = !allChecked;
        }
    });
    
    updateSelectionCount();
}

// Actualizar contador de seleccionados
function updateSelectionCount() {
    selectedDocuments = Array.from(document.querySelectorAll('.document-checkbox:checked'))
        .map(cb => cb.value);
    
    document.getElementById('selectedCount').textContent = `${selectedDocuments.length} seleccionados`;
    document.getElementById('approveSelectedBtn').disabled = selectedDocuments.length === 0;
}

// Vista previa de documento
function openPreview(fileUrl, fileName) {
    document.getElementById('previewTitle').textContent = fileName;
    document.getElementById('previewModal').classList.remove('hidden');
    
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = '<div class="text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div><p class="mt-2 text-gray-600">Cargando...</p></div>';
    
    if (fileName.toLowerCase().endsWith('.pdf')) {
        loadPDF(fileUrl);
    } else {
        previewContent.innerHTML = `<img src="${fileUrl}" alt="${fileName}" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg">`;
    }
}

// Cargar PDF
async function loadPDF(url) {
    try {
        const pdf = await pdfjsLib.getDocument(url).promise;
        const page = await pdf.getPage(1);
        
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        const viewport = page.getViewport({ scale: 1.5 });
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
        await page.render({ canvasContext: context, viewport: viewport }).promise;
        
        document.getElementById('previewContent').innerHTML = '';
        document.getElementById('previewContent').appendChild(canvas);
        canvas.className = 'max-w-full mx-auto rounded-lg shadow-lg';
        
    } catch (error) {
        console.error('Error loading PDF:', error);
        document.getElementById('previewContent').innerHTML = 
            '<div class="text-red-500"><p>Error al cargar el PDF</p><p class="text-sm mt-2">Haz clic <a href="' + url + '" target="_blank" class="underline">aquí</a> para abrir en nueva ventana</p></div>';
    }
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Aprobar documento individual
function approveDocument(documentId) {
    currentDocumentId = documentId;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveNotes').value = '';
    currentDocumentId = null;
}

async function submitApproval(event) {
    event.preventDefault();
    
    const notes = document.getElementById('approveNotes').value;
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Mostrar carga
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Aprobando...';
    
    try {
        const response = await fetch(`/ai/training/approve/${currentDocumentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ notes })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Mostrar éxito brevemente
            submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>¡Aprobado!';
            submitBtn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg';
            
            setTimeout(() => {
                showAlert(result.message, 'success');
                removeDocumentCard(currentDocumentId);
                closeApproveModal();
            }, 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al aprobar el documento', 'error');
    } finally {
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            submitBtn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors';
        }, result?.success ? 1000 : 0);
    }
}

// Rechazar documento
function rejectDocument(documentId) {
    currentDocumentId = documentId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectReason').value = '';
    currentDocumentId = null;
}

async function submitRejection(event) {
    event.preventDefault();
    
    const reason = document.getElementById('rejectReason').value;
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Mostrar carga
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>Rechazando...';
    
    try {
        const response = await fetch(`/ai/training/reject/${currentDocumentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Mostrar éxito brevemente
            submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>¡Rechazado!';
            submitBtn.className = 'px-4 py-2 bg-red-500 text-white rounded-lg';
            
            setTimeout(() => {
                showAlert(result.message, 'success');
                removeDocumentCard(currentDocumentId);
                closeRejectModal();
            }, 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al rechazar el documento', 'error');
    } finally {
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            submitBtn.className = 'px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors';
        }, result?.success ? 1000 : 0);
    }
}

// Aprobar documentos seleccionados
async function approveSelected() {
    if (selectedDocuments.length === 0) return;
    
    const btn = document.getElementById('approveSelectedBtn');
    const spinner = document.getElementById('massApproveSpinner');
    const text = document.getElementById('massApproveText');
    const originalText = text.textContent;
    
    // Mostrar carga
    btn.disabled = true;
    spinner.classList.remove('hidden');
    text.textContent = `Aprobando ${selectedDocuments.length} documentos...`;
    
    try {
        let successful = 0;
        let failed = 0;
        const total = selectedDocuments.length;
        
        // Procesar documentos uno por uno para mejor UX
        for (let i = 0; i < selectedDocuments.length; i++) {
            const documentId = selectedDocuments[i];
            text.textContent = `Aprobando ${i + 1} de ${total}...`;
            
            try {
                const response = await fetch(`/ai/training/approve/${documentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ notes: 'Aprobación masiva' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    removeDocumentCard(documentId);
                    successful++;
                } else {
                    failed++;
                }
                
                // Pequeña pausa para mejor UX
                await new Promise(resolve => setTimeout(resolve, 200));
                
            } catch (error) {
                failed++;
            }
        }
        
        // Mostrar éxito
        spinner.classList.add('hidden');
        text.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>¡Completado!';
        btn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg';
        
        setTimeout(() => {
            if (successful > 0) {
                showAlert(`${successful} documentos aprobados exitosamente`, 'success');
            }
            if (failed > 0) {
                showAlert(`${failed} documentos no pudieron ser aprobados`, 'error');
            }
            
            selectedDocuments = [];
            updateSelectionCount();
            
            // Restaurar botón
            btn.className = 'px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50';
            text.textContent = originalText;
            btn.disabled = true; // Se habilitará cuando se seleccionen documentos
        }, 1500);
        
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error en la aprobación masiva', 'error');
        
        // Restaurar botón en caso de error
        spinner.classList.add('hidden');
        text.textContent = originalText;
        btn.disabled = false;
    }
}

// Remover tarjeta de documento del DOM
function removeDocumentCard(documentId) {
    const checkbox = document.querySelector(`input[value="${documentId}"]`);
    if (checkbox) {
        const card = checkbox.closest('.document-card');
        if (card) {
            card.style.transition = 'opacity 0.3s ease';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
        }
    }
}

// Función de alerta
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

// Cerrar modales al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.id === 'previewModal') closePreview();
    if (e.target.id === 'approveModal') closeApproveModal();
    if (e.target.id === 'rejectModal') closeRejectModal();
});

// Configurar PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
</script>
@endpush 