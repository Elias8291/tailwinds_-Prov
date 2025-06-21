/**
 * Sistema de Validación de Formularios
 * Padrón de Proveedores del Estado de Oaxaca
 */

class FormValidator {
    constructor() {
        this.rules = {
            // Reglas de validación comunes
            required: (value) => value && value.toString().trim().length > 0,
            email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
            rfc: (value) => /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/.test(value),
            curp: (value) => /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z\d]\d$/.test(value),
            phone: (value) => /^\d{10}$/.test(value.replace(/\D/g, '')),
            cp: (value) => /^\d{4,5}$/.test(value),
            minLength: (value, length) => value && value.toString().length >= length,
            maxLength: (value, length) => value && value.toString().length <= length,
            numeric: (value) => /^\d+$/.test(value),
            alphanumeric: (value) => /^[a-zA-Z0-9\s]+$/.test(value),
        };

        this.messages = {
            required: 'Este campo es obligatorio',
            email: 'Ingrese un email válido',
            rfc: 'El RFC debe tener el formato correcto (ej: XAXX010101000)',
            curp: 'La CURP debe tener 18 caracteres y formato válido',
            phone: 'El teléfono debe tener 10 dígitos',
            cp: 'El código postal debe tener 4 o 5 dígitos',
            minLength: 'Debe tener al menos {length} caracteres',
            maxLength: 'No debe exceder {length} caracteres',
            numeric: 'Solo se permiten números',
            alphanumeric: 'Solo se permiten letras, números y espacios'
        };

        this.init();
    }

    init() {
        // Inicializar validaciones cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeValidation());
        } else {
            this.initializeValidation();
        }
    }

    initializeValidation() {
        // Buscar todos los formularios con validación
        const forms = document.querySelectorAll('form[data-validate="true"], form.validate');
        forms.forEach(form => this.setupFormValidation(form));

        // Agregar validación a campos individuales
        this.setupFieldValidation();
    }

    setupFormValidation(form) {
        // Prevenir envío si hay errores
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form)) {
                e.preventDefault();
                e.stopPropagation();
                this.showFormErrors(form);
            }
        });

        // Validación en tiempo real al perder el foco
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });
    }

    setupFieldValidation() {
        // RFC en tiempo real
        document.querySelectorAll('input[name="rfc"], input[id*="rfc"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.toUpperCase();
                this.validateField(e.target);
            });
        });

        // CURP en tiempo real
        document.querySelectorAll('input[name="curp"], input[id*="curp"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.toUpperCase();
                this.validateField(e.target);
            });
        });

        // Teléfono - solo números
        document.querySelectorAll('input[name*="telefono"], input[id*="telefono"], input[type="tel"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '');
                if (e.target.value.length > 10) {
                    e.target.value = e.target.value.substring(0, 10);
                }
                this.validateField(e.target);
            });
        });

        // Código postal
        document.querySelectorAll('input[name="codigo_postal"], input[id="codigo_postal"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '');
                if (e.target.value.length > 5) {
                    e.target.value = e.target.value.substring(0, 5);
                }
                this.validateField(e.target);
            });
        });

        // Email
        document.querySelectorAll('input[type="email"], input[name*="email"], input[name*="correo"]').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
        });
    }

    validateField(field) {
        const value = field.value;
        const validationRules = this.getFieldValidationRules(field);
        let isValid = true;
        let errorMessage = '';

        // Verificar cada regla
        for (const rule of validationRules) {
            const result = this.applyRule(value, rule);
            if (!result.valid) {
                isValid = false;
                errorMessage = result.message;
                break;
            }
        }

        // Mostrar/ocultar error
        if (isValid) {
            this.clearFieldError(field);
            this.showFieldSuccess(field);
        } else {
            this.showFieldError(field, errorMessage);
        }

        return isValid;
    }

    validateForm(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let isFormValid = true;

        fields.forEach(field => {
            const isFieldValid = this.validateField(field);
            if (!isFieldValid) {
                isFormValid = false;
            }
        });

        return isFormValid;
    }

    getFieldValidationRules(field) {
        const rules = [];
        
        // Required
        if (field.hasAttribute('required') || field.dataset.required === 'true') {
            rules.push({ type: 'required' });
        }

        // Tipo específico de campo
        if (field.type === 'email' || field.name.includes('email') || field.name.includes('correo')) {
            rules.push({ type: 'email' });
        }

        if (field.name === 'rfc' || field.id === 'rfc') {
            rules.push({ type: 'rfc' });
        }

        if (field.name === 'curp' || field.id === 'curp') {
            rules.push({ type: 'curp' });
        }

        if (field.name.includes('telefono') || field.type === 'tel') {
            rules.push({ type: 'phone' });
        }

        if (field.name === 'codigo_postal') {
            rules.push({ type: 'cp' });
        }

        // Min/Max length
        if (field.minLength && field.minLength > 0) {
            rules.push({ type: 'minLength', value: field.minLength });
        }

        if (field.maxLength && field.maxLength > 0) {
            rules.push({ type: 'maxLength', value: field.maxLength });
        }

        // Reglas personalizadas
        if (field.dataset.validation) {
            const customRules = field.dataset.validation.split('|');
            customRules.forEach(rule => {
                const [ruleType, ruleValue] = rule.split(':');
                rules.push({ type: ruleType, value: ruleValue });
            });
        }

        return rules;
    }

    applyRule(value, rule) {
        const ruleFunction = this.rules[rule.type];
        
        if (!ruleFunction) {
            return { valid: true };
        }

        let valid;
        if (rule.value !== undefined) {
            valid = ruleFunction(value, rule.value);
        } else {
            valid = ruleFunction(value);
        }

        let message = this.messages[rule.type] || 'Valor inválido';
        if (rule.value && message.includes('{length}')) {
            message = message.replace('{length}', rule.value);
        }

        return { valid, message };
    }

    showFieldError(field, message) {
        this.clearFieldError(field);

        // Agregar clase de error al campo
        field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.remove('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');

        // Crear mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-600 text-sm mt-1 flex items-center';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${message}`;

        // Insertar después del campo o su contenedor
        const container = field.closest('.form-group') || field.parentNode;
        container.appendChild(errorDiv);

        // Agregar ícono de error al campo si tiene contenedor relativo
        if (field.parentNode.classList.contains('relative')) {
            const errorIcon = document.createElement('div');
            errorIcon.className = 'field-error-icon absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none';
            errorIcon.innerHTML = '<i class="fas fa-exclamation-circle text-red-500"></i>';
            field.parentNode.appendChild(errorIcon);
        }
    }

    showFieldSuccess(field) {
        // Solo mostrar éxito si el campo tiene valor
        if (!field.value) return;

        field.classList.add('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

        // Agregar ícono de éxito
        if (field.parentNode.classList.contains('relative')) {
            const existingIcon = field.parentNode.querySelector('.field-error-icon, .field-success-icon');
            if (existingIcon) existingIcon.remove();

            const successIcon = document.createElement('div');
            successIcon.className = 'field-success-icon absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none';
            successIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            field.parentNode.appendChild(successIcon);
        }
    }

    clearFieldError(field) {
        // Remover clases de error
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

        // Remover mensajes de error
        const container = field.closest('.form-group') || field.parentNode;
        const errorMessages = container.querySelectorAll('.field-error');
        errorMessages.forEach(msg => msg.remove());

        // Remover íconos de error
        const errorIcons = field.parentNode.querySelectorAll('.field-error-icon');
        errorIcons.forEach(icon => icon.remove());
    }

    showFormErrors(form) {
        const firstErrorField = form.querySelector('.border-red-500');
        if (firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Mostrar toast de error general
        this.showToast('Por favor, corrija los errores marcados en el formulario', 'error');
    }

    showToast(message, type = 'info') {
        // Crear toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full`;
        
        const bgClass = type === 'error' ? 'bg-red-600' : type === 'success' ? 'bg-green-600' : 'bg-blue-600';
        toast.classList.add(bgClass);

        const icon = type === 'error' ? 'fa-exclamation-circle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icon} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remover después de 5 segundos
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Método público para validar formulario específico
    validateSpecificForm(formSelector) {
        const form = document.querySelector(formSelector);
        if (form) {
            return this.validateForm(form);
        }
        return false;
    }

    // Método público para limpiar errores de un formulario
    clearFormErrors(formSelector) {
        const form = document.querySelector(formSelector);
        if (form) {
            const fields = form.querySelectorAll('input, textarea, select');
            fields.forEach(field => this.clearFieldError(field));
        }
    }
}

// Inicializar validador global
window.FormValidator = FormValidator;
window.formValidator = new FormValidator();

// Funciones de utilidad globales
window.validateForm = (formSelector) => {
    return window.formValidator.validateSpecificForm(formSelector);
};

window.clearFormErrors = (formSelector) => {
    window.formValidator.clearFormErrors(formSelector);
};

window.showValidationToast = (message, type) => {
    window.formValidator.showToast(message, type);
}; 