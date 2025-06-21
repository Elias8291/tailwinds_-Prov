/**
 * Validador específico para formularios de trámites
 * Extiende el FormValidator base con funcionalidades específicas
 */

class TramiteValidator extends FormValidator {
    constructor() {
        super();
        this.initTramiteSpecificValidations();
    }

    initTramiteSpecificValidations() {
        // Validaciones específicas para trámites cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupTramiteValidations());
        } else {
            this.setupTramiteValidations();
        }
    }

    setupTramiteValidations() {
        // Validaciones para el formulario de datos generales
        this.setupDatosGeneralesValidation();
        
        // Validaciones para el formulario de domicilio
        this.setupDomicilioValidation();
        
        // Validaciones para el formulario de constitución
        this.setupConstitucionValidation();
        
        // Validaciones para el formulario de documentos
        this.setupDocumentosValidation();

        // Integración con Alpine.js si está disponible
        this.setupAlpineIntegration();
    }

    setupDatosGeneralesValidation() {
        const form = document.getElementById('datos-generales-form');
        if (!form) return;

        // Validación del campo de giro con contador de caracteres
        const giroField = form.querySelector('#giro');
        if (giroField) {
            giroField.addEventListener('input', (e) => {
                const maxLength = 500;
                const currentLength = e.target.value.length;
                
                // Actualizar contador
                const counter = form.querySelector('.character-counter');
                if (counter) {
                    counter.textContent = `${currentLength}/${maxLength}`;
                    
                    // Cambiar color según proximidad al límite
                    if (currentLength > maxLength * 0.9) {
                        counter.classList.add('text-red-500');
                        counter.classList.remove('text-gray-400', 'text-yellow-500');
                    } else if (currentLength > maxLength * 0.7) {
                        counter.classList.add('text-yellow-500');
                        counter.classList.remove('text-gray-400', 'text-red-500');
                    } else {
                        counter.classList.add('text-gray-400');
                        counter.classList.remove('text-yellow-500', 'text-red-500');
                    }
                }
                
                this.validateField(e.target);
            });
        }

        // Validación de actividades seleccionadas
        const actividadesInput = form.querySelector('input[name="actividades_seleccionadas"]');
        if (actividadesInput) {
            this.setupActividadesValidation(actividadesInput);
        }

        // Interceptar el botón "Guardar y Continuar"
        const saveButton = form.querySelector('button[onclick*="guardarYSiguiente"]');
        if (saveButton) {
            saveButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleTramiteFormSubmission('datos-generales-form');
            });
        }
    }

    setupDomicilioValidation() {
        const form = document.querySelector('form[x-ref="domicilioForm"]');
        if (!form) return;

        // Validación del código postal con búsqueda automática
        const cpField = form.querySelector('#codigo_postal');
        if (cpField) {
            cpField.addEventListener('blur', async (e) => {
                const cp = e.target.value;
                if (this.rules.cp(cp)) {
                    await this.buscarDatosPorCP(cp);
                }
            });
        }

        // Validación de campos de dirección
        const addressFields = ['calle', 'numero_exterior', 'numero_interior'];
        addressFields.forEach(fieldName => {
            const field = form.querySelector(`#${fieldName}`);
            if (field) {
                field.addEventListener('blur', () => this.validateField(field));
            }
        });
    }

    setupConstitucionValidation() {
        const form = document.querySelector('form[data-form="constitucion"]');
        if (!form) return;

        // Validaciones específicas para datos de constitución
        const fechaField = form.querySelector('input[type="date"]');
        if (fechaField) {
            fechaField.addEventListener('change', (e) => {
                this.validateFechaConstitucion(e.target);
            });
        }

        // Validación de número de escritura
        const escrituraField = form.querySelector('input[name*="escritura"]');
        if (escrituraField) {
            escrituraField.addEventListener('input', (e) => {
                // Solo permitir números y algunos caracteres especiales
                e.target.value = e.target.value.replace(/[^0-9\-\/]/g, '');
                this.validateField(e.target);
            });
        }
    }

    setupDocumentosValidation() {
        // Validación de archivos subidos
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.validateFileUpload(e.target);
            });
        });
    }

    setupActividadesValidation(actividadesInput) {
        // Validar que al menos una actividad esté seleccionada
        const validateActividades = () => {
            const actividades = actividadesInput.value;
            try {
                const actividadesArray = JSON.parse(actividades || '[]');
                if (actividadesArray.length === 0) {
                    this.showFieldError(actividadesInput, 'Debe seleccionar al menos una actividad');
                    return false;
                } else {
                    this.clearFieldError(actividadesInput);
                    return true;
                }
            } catch (error) {
                this.showFieldError(actividadesInput, 'Formato de actividades inválido');
                return false;
            }
        };

        // Observar cambios en el campo de actividades
        const observer = new MutationObserver(() => {
            validateActividades();
        });

        observer.observe(actividadesInput, {
            attributes: true,
            attributeFilter: ['value']
        });

        // Validación inicial
        validateActividades();
    }

    setupAlpineIntegration() {
        // Integración con Alpine.js para validaciones reactivas
        if (typeof Alpine !== 'undefined') {
            Alpine.directive('validate', (el, { expression }, { evaluate }) => {
                // Agregar validación automática a elementos con x-validate
                el.addEventListener('blur', () => this.validateField(el));
                el.addEventListener('input', () => this.clearFieldError(el));
            });

            // Magic helper para validación en Alpine
            Alpine.magic('validate', () => {
                return {
                    field: (field) => this.validateField(field),
                    form: (form) => this.validateForm(form),
                    clear: (field) => this.clearFieldError(field)
                };
            });
        }
    }

    async buscarDatosPorCP(cp) {
        try {
            // Simular búsqueda de datos por código postal
            // En una implementación real, esto haría una llamada API
            const response = await fetch(`/api/codigo-postal/${cp}`);
            if (response.ok) {
                const data = await response.json();
                // Llenar automáticamente los campos
                this.fillAddressFields(data);
            }
        } catch (error) {
            console.warn('No se pudieron obtener datos del código postal:', error);
        }
    }

    fillAddressFields(data) {
        if (data.estado) {
            const estadoField = document.querySelector('#estado');
            if (estadoField) estadoField.value = data.estado;
        }
        
        if (data.municipio) {
            const municipioField = document.querySelector('#municipio');
            if (municipioField) municipioField.value = data.municipio;
        }
    }

    validateFechaConstitucion(field) {
        const fecha = new Date(field.value);
        const hoy = new Date();
        
        if (fecha > hoy) {
            this.showFieldError(field, 'La fecha de constitución no puede ser futura');
            return false;
        }
        
        // Validar que no sea muy antigua (más de 100 años)
        const hace100Anos = new Date();
        hace100Anos.setFullYear(hoy.getFullYear() - 100);
        
        if (fecha < hace100Anos) {
            this.showFieldError(field, 'La fecha de constitución es demasiado antigua');
            return false;
        }

        this.clearFieldError(field);
        return true;
    }

    validateFileUpload(fileInput) {
        const file = fileInput.files[0];
        if (!file) return true;

        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];

        // Validar tamaño
        if (file.size > maxSize) {
            this.showFieldError(fileInput, 'El archivo no debe superar los 5MB');
            fileInput.value = '';
            return false;
        }

        // Validar tipo
        if (!allowedTypes.includes(file.type)) {
            this.showFieldError(fileInput, 'Solo se permiten archivos PDF, JPG y PNG');
            fileInput.value = '';
            return false;
        }

        this.clearFieldError(fileInput);
        return true;
    }

    async handleTramiteFormSubmission(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        // Validar formulario completo
        const isValid = this.validateForm(form);
        
        if (!isValid) {
            this.showToast('Por favor, corrija los errores antes de continuar', 'error');
            return;
        }

        // Mostrar indicador de carga
        this.showLoadingState(form);

        try {
            // Intentar guardar el formulario
            if (typeof guardarYSiguiente === 'function') {
                await guardarYSiguiente();
            } else {
                // Enviar formulario manualmente
                form.submit();
            }
        } catch (error) {
            this.hideLoadingState(form);
            this.showToast('Error al guardar el formulario', 'error');
            console.error('Error en envío de formulario:', error);
        }
    }

    showLoadingState(form) {
        const submitButtons = form.querySelectorAll('button[type="submit"], button[onclick*="guardar"]');
        submitButtons.forEach(button => {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
        });
    }

    hideLoadingState(form) {
        const submitButtons = form.querySelectorAll('button[type="submit"], button[onclick*="guardar"]');
        submitButtons.forEach(button => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-save mr-2"></i>Guardar y Continuar';
        });
    }

    // Método para validar formulario completo de trámite
    validateTramiteCompleto(tramiteId) {
        const secciones = [
            'datos-generales',
            'domicilio', 
            'constitucion',
            'accionistas',
            'documentos'
        ];

        let errores = [];

        secciones.forEach(seccion => {
            const form = document.querySelector(`form[data-seccion="${seccion}"]`);
            if (form && !this.validateForm(form)) {
                errores.push(`Sección ${seccion} tiene errores`);
            }
        });

        if (errores.length > 0) {
            this.showToast(`Errores encontrados: ${errores.join(', ')}`, 'error');
            return false;
        }

        return true;
    }
}

// Inicializar validador de trámites
if (typeof window !== 'undefined') {
    window.TramiteValidator = TramiteValidator;
    window.tramiteValidator = new TramiteValidator();
    
    // Función global para validar trámite completo
    window.validateTramiteCompleto = (tramiteId) => {
        return window.tramiteValidator.validateTramiteCompleto(tramiteId);
    };
    
    // Función global para manejar formularios de trámite
    window.handleTramiteSubmission = (formId) => {
        return window.tramiteValidator.handleTramiteFormSubmission(formId);
    };
} 