/**
 * Validaciones del lado del cliente para formularios
 * Trabajan en conjunto con las validaciones del servidor
 */

// Validaciones para Datos Generales
const ValidacionesDatosGenerales = {
    
    // Validar giro
    validateGiro(giro) {
        const errors = [];
        
        if (!giro || giro.trim().length < 10) {
            errors.push('El giro debe tener al menos 10 caracteres');
        }
        
        if (giro && giro.length > 500) {
            errors.push('El giro no puede exceder 500 caracteres');
        }
        
        if (giro && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.,;:\-\(\)\/]+$/.test(giro)) {
            errors.push('El giro contiene caracteres no permitidos');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar página web
    validatePaginaWeb(paginaWeb) {
        const errors = [];
        
        if (paginaWeb && paginaWeb.trim()) {
            const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/i;
            if (!urlRegex.test(paginaWeb)) {
                errors.push('El formato de la URL no es válido');
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar nombre de contacto
    validateContactoNombre(nombre) {
        const errors = [];
        
        if (!nombre || nombre.trim().length < 2) {
            errors.push('El nombre debe tener al menos 2 caracteres');
        }
        
        if (nombre && nombre.length > 100) {
            errors.push('El nombre no puede exceder 100 caracteres');
        }
        
        if (nombre && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/.test(nombre)) {
            errors.push('El nombre solo puede contener letras, espacios, apostrofes y puntos');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar cargo de contacto
    validateContactoCargo(cargo) {
        const errors = [];
        
        if (!cargo || cargo.trim().length < 3) {
            errors.push('El cargo debe tener al menos 3 caracteres');
        }
        
        if (cargo && cargo.length > 50) {
            errors.push('El cargo no puede exceder 50 caracteres');
        }
        
        if (cargo && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/.test(cargo)) {
            errors.push('El cargo solo puede contener letras, espacios, apostrofes y puntos');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar correo de contacto
    validateContactoCorreo(correo) {
        const errors = [];
        
        if (!correo || !correo.trim()) {
            errors.push('El correo electrónico es obligatorio');
        }
        
        if (correo) {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(correo)) {
                errors.push('El formato del correo electrónico no es válido');
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar teléfono de contacto
    validateContactoTelefono(telefono) {
        const errors = [];
        
        if (!telefono || !telefono.trim()) {
            errors.push('El teléfono es obligatorio');
        }
        
        if (telefono) {
            // Limpiar solo números
            const numeroLimpio = telefono.replace(/\D/g, '');
            
            if (numeroLimpio.length !== 10) {
                errors.push('El teléfono debe tener exactamente 10 dígitos');
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors,
            cleanValue: telefono ? telefono.replace(/\D/g, '') : ''
        };
    },
    
    // Validar actividades seleccionadas
    validateActividades(actividades) {
        const errors = [];
        
        if (!actividades || actividades.length === 0) {
            errors.push('Debe seleccionar al menos una actividad económica');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
};

// Validaciones para Apoderado Legal
const ValidacionesApoderado = {
    
    // Validar nombre del apoderado
    validateNombreApoderado(nombre) {
        const errors = [];
        
        if (!nombre || nombre.trim().length < 2) {
            errors.push('El nombre del apoderado debe tener al menos 2 caracteres');
        }
        
        if (nombre && nombre.length > 100) {
            errors.push('El nombre del apoderado no puede exceder 100 caracteres');
        }
        
        if (nombre && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/.test(nombre)) {
            errors.push('El nombre del apoderado solo puede contener letras, espacios, apostrofes y puntos');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar número de escritura
    validateNumeroEscritura(numero) {
        const errors = [];
        
        if (!numero || numero.trim().length < 1) {
            errors.push('El número de escritura pública es obligatorio');
        }
        
        if (numero && numero.length > 20) {
            errors.push('El número de escritura no puede exceder 20 caracteres');
        }
        
        if (numero && !/^[0-9\-\/A-Z]+$/.test(numero)) {
            errors.push('El número de escritura solo puede contener números, letras mayúsculas, guiones y diagonales');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar nombre del notario
    validateNombreNotario(nombre) {
        const errors = [];
        
        if (!nombre || nombre.trim().length < 2) {
            errors.push('El nombre del notario debe tener al menos 2 caracteres');
        }
        
        if (nombre && nombre.length > 100) {
            errors.push('El nombre del notario no puede exceder 100 caracteres');
        }
        
        if (nombre && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\'\.]+$/.test(nombre)) {
            errors.push('El nombre del notario solo puede contener letras, espacios, apostrofes y puntos');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar número del notario
    validateNumeroNotario(numero) {
        const errors = [];
        
        if (!numero || numero.trim().length < 1) {
            errors.push('El número del notario público es obligatorio');
        }
        
        if (numero && numero.length > 10) {
            errors.push('El número del notario no puede exceder 10 dígitos');
        }
        
        if (numero && !/^[0-9]+$/.test(numero)) {
            errors.push('El número del notario solo puede contener dígitos numéricos');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar fecha de escritura
    validateFechaEscritura(fecha) {
        const errors = [];
        
        if (!fecha) {
            errors.push('La fecha de la escritura pública es obligatoria');
        }
        
        if (fecha) {
            const fechaObj = new Date(fecha);
            const hoy = new Date();
            const fecha1900 = new Date('1900-01-01');
            
            if (isNaN(fechaObj.getTime())) {
                errors.push('La fecha de escritura debe ser una fecha válida');
            } else {
                if (fechaObj > hoy) {
                    errors.push('La fecha de escritura no puede ser posterior a hoy');
                }
                
                if (fechaObj < fecha1900) {
                    errors.push('La fecha de escritura debe ser posterior al año 1900');
                }
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar fecha de inscripción
    validateFechaInscripcion(fechaInscripcion, fechaEscritura) {
        const errors = [];
        
        if (!fechaInscripcion) {
            errors.push('La fecha de inscripción en el registro público es obligatoria');
        }
        
        if (fechaInscripcion) {
            const fechaInscObj = new Date(fechaInscripcion);
            const hoy = new Date();
            
            if (isNaN(fechaInscObj.getTime())) {
                errors.push('La fecha de inscripción debe ser una fecha válida');
            } else {
                if (fechaInscObj > hoy) {
                    errors.push('La fecha de inscripción no puede ser posterior a hoy');
                }
                
                if (fechaEscritura) {
                    const fechaEscObj = new Date(fechaEscritura);
                    if (!isNaN(fechaEscObj.getTime()) && fechaInscObj < fechaEscObj) {
                        errors.push('La fecha de inscripción no puede ser anterior a la fecha de escritura');
                    }
                }
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar número de registro
    validateNumeroRegistro(numero) {
        const errors = [];
        
        if (!numero || numero.trim().length < 1) {
            errors.push('El número de registro público mercantil es obligatorio');
        }
        
        if (numero && numero.length > 30) {
            errors.push('El número de registro no puede exceder 30 caracteres');
        }
        
        if (numero && !/^[0-9A-Z\-\/\s]+$/.test(numero)) {
            errors.push('El número de registro solo puede contener números, letras mayúsculas, guiones, diagonales y espacios');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
};

// Validaciones para Documentos
const ValidacionesDocumentos = {
    
    // Validar archivo PDF
    validateArchivoPDF(archivo) {
        const errors = [];
        
        if (!archivo) {
            errors.push('Debe seleccionar un archivo para subir');
        } else {
            // Verificar tipo de archivo
            if (archivo.type !== 'application/pdf') {
                errors.push('Solo se permiten archivos en formato PDF');
            }
            
            // Verificar tamaño (máximo 10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB en bytes
            if (archivo.size > maxSize) {
                errors.push('El archivo no puede ser mayor a 10 MB');
            }
            
            // Verificar tamaño mínimo (al menos 1KB)
            if (archivo.size < 1024) {
                errors.push('El archivo es demasiado pequeño. Debe tener al menos 1 KB');
            }
            
            // Verificar nombre del archivo
            if (archivo.name.length > 255) {
                errors.push('El nombre del archivo es demasiado largo. Máximo 255 caracteres');
            }
            
            // Verificar caracteres del nombre
            if (!/^[a-zA-Z0-9\s\.\-_\(\)]+\.pdf$/i.test(archivo.name)) {
                errors.push('El nombre del archivo contiene caracteres no permitidos. Use solo letras, números, espacios, guiones y paréntesis');
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    },
    
    // Validar comentario de rechazo
    validateComentarioRechazo(comentario) {
        const errors = [];
        
        if (!comentario || comentario.trim().length < 10) {
            errors.push('El comentario debe tener al menos 10 caracteres');
        }
        
        if (comentario && comentario.length > 1000) {
            errors.push('El comentario no puede exceder 1000 caracteres');
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
};

// Utilidades para mostrar errores
const ValidationUtils = {
    
    // Mostrar error en un campo específico
    showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        // Remover errores anteriores
        this.clearFieldError(fieldId);
        
        // Agregar clase de error al campo
        field.classList.add('border-red-500', 'bg-red-50');
        
        // Crear contenedor de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg field-error';
        errorDiv.innerHTML = `
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mr-2 text-red-500 mt-0.5 flex-shrink-0"></i>
                <span class="text-sm text-red-700">${message}</span>
            </div>
        `;
        
        // Insertar después del campo
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    },
    
    // Limpiar error de un campo
    clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        // Remover clases de error
        field.classList.remove('border-red-500', 'bg-red-50');
        
        // Remover contenedor de error
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    },
    
    // Limpiar todos los errores de un formulario
    clearAllErrors(formElement) {
        const errorDivs = formElement.querySelectorAll('.field-error');
        errorDivs.forEach(div => div.remove());
        
        const errorFields = formElement.querySelectorAll('.border-red-500');
        errorFields.forEach(field => {
            field.classList.remove('border-red-500', 'bg-red-50');
        });
    },
    
    // Mostrar mensaje de éxito general
    showSuccess(message, containerId = 'success-container') {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = `
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700 text-sm">${message}</p>
                </div>
            </div>
        `;
        
        container.style.display = 'block';
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            container.style.display = 'none';
        }, 5000);
    },
    
    // Mostrar mensaje de error general
    showError(message, containerId = 'error-container') {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = `
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    <p class="text-red-700 text-sm">${message}</p>
                </div>
            </div>
        `;
        
        container.style.display = 'block';
        
        // Ocultar después de 8 segundos
        setTimeout(() => {
            container.style.display = 'none';
        }, 8000);
    }
};

// Exportar para uso global
window.ValidacionesDatosGenerales = ValidacionesDatosGenerales;
window.ValidacionesApoderado = ValidacionesApoderado;
window.ValidacionesDocumentos = ValidacionesDocumentos;
window.ValidationUtils = ValidationUtils; 
 