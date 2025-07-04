export class DocumentReviewHandler {
    constructor() {
        this.documentModal = document.getElementById('documento-modal');
        this.documentIframe = document.getElementById('documento-iframe');
        this.documentTitle = document.getElementById('documento-modal-title');
        this.documentName = document.getElementById('documento-nombre-revision');
        this.documentStatus = document.getElementById('documento-estado-actual');
        this.documentVersion = document.getElementById('documento-version');
        this.commentInput = document.getElementById('comentario-documento');
        this.historialRevisiones = document.getElementById('historial-revisiones');
        this.listaHistorial = document.getElementById('lista-historial');
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Botones de acción
        document.getElementById('btn-aprobar-documento')?.addEventListener('click', () => this.aplicarRevisionDocumento('aprobado'));
        document.getElementById('btn-rechazar-documento')?.addEventListener('click', () => this.aplicarRevisionDocumento('rechazado'));
        document.getElementById('btn-correccion-documento')?.addEventListener('click', () => this.aplicarRevisionDocumento('correccion'));
        document.getElementById('btn-guardar-comentario-doc')?.addEventListener('click', () => this.guardarComentarioDocumento());
    }

    abrirModalDocumento(documentoUrl, documentoInfo) {
        if (!this.documentModal || !this.documentIframe) return;

        this.documentIframe.src = documentoUrl;
        if (documentoInfo) {
            this.documentName.textContent = documentoInfo.nombre || '-';
            this.documentVersion.textContent = documentoInfo.version || 'v1';
            this.documentStatus.innerHTML = this.getStatusBadge(documentoInfo.estado || 'pendiente');
        }

        this.documentModal.classList.remove('hidden');
        this.cargarHistorialRevisiones(documentoInfo?.id);
    }

    cerrarModalDocumento() {
        if (!this.documentModal) return;
        this.documentModal.classList.add('hidden');
        this.documentIframe.src = '';
        this.resetForm();
    }

    async aplicarRevisionDocumento(estado) {
        try {
            const comentario = this.commentInput.value.trim();
            const prioridad = document.getElementById('prioridad-documento').value;
            
            if (estado !== 'pendiente' && !comentario) {
                alert('Por favor, agregue un comentario antes de cambiar el estado del documento.');
                return;
            }

            const response = await fetch('/api/documentos/revision', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    documento_id: this.documentoActualId,
                    estado,
                    comentario,
                    prioridad
                })
            });

            if (!response.ok) throw new Error('Error al aplicar la revisión');

            const result = await response.json();
            this.actualizarEstadoDocumento(estado);
            this.cargarHistorialRevisiones(this.documentoActualId);
            this.resetForm();

            alert(result.message || 'Revisión aplicada correctamente');
        } catch (error) {
            console.error('Error:', error);
            alert('Error al aplicar la revisión. Por favor, intente nuevamente.');
        }
    }

    async guardarComentarioDocumento() {
        const comentario = this.commentInput.value.trim();
        if (!comentario) {
            alert('Por favor, escriba un comentario antes de guardarlo.');
            return;
        }

        try {
            const response = await fetch('/api/documentos/comentario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    documento_id: this.documentoActualId,
                    comentario,
                    prioridad: document.getElementById('prioridad-documento').value
                })
            });

            if (!response.ok) throw new Error('Error al guardar el comentario');

            const result = await response.json();
            this.cargarHistorialRevisiones(this.documentoActualId);
            this.resetForm();

            alert(result.message || 'Comentario guardado correctamente');
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar el comentario. Por favor, intente nuevamente.');
        }
    }

    async cargarHistorialRevisiones(documentoId) {
        if (!this.historialRevisiones || !this.listaHistorial || !documentoId) return;

        try {
            const response = await fetch(`/api/documentos/${documentoId}/historial`);
            if (!response.ok) throw new Error('Error al cargar el historial');

            const historial = await response.json();
            
            if (historial.length > 0) {
                this.historialRevisiones.classList.remove('hidden');
                this.listaHistorial.innerHTML = historial.map(revision => `
                    <div class="border-l-4 border-gray-300 pl-2">
                        <p class="font-medium">${this.getStatusBadge(revision.estado)} ${revision.fecha}</p>
                        <p class="text-gray-600">${revision.comentario}</p>
                        <p class="text-gray-500 text-xs">Por: ${revision.usuario}</p>
                    </div>
                `).join('');
            } else {
                this.historialRevisiones.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            this.historialRevisiones.classList.add('hidden');
        }
    }

    getStatusBadge(estado) {
        const badges = {
            pendiente: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⏳ Pendiente</span>',
            aprobado: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Aprobado</span>',
            rechazado: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">❌ Rechazado</span>',
            correccion: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">📝 Corrección</span>'
        };
        return badges[estado] || badges.pendiente;
    }

    resetForm() {
        if (this.commentInput) this.commentInput.value = '';
        const prioridadSelect = document.getElementById('prioridad-documento');
        if (prioridadSelect) prioridadSelect.value = 'media';
    }

    actualizarEstadoDocumento(estado) {
        if (this.documentStatus) {
            this.documentStatus.innerHTML = this.getStatusBadge(estado);
        }
    }
} 