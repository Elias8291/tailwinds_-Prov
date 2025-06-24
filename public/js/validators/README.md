# Validador de Datos Generales

Este archivo contiene el validador JavaScript para el formulario de datos generales del sistema.

## Características

- **Validación en tiempo real**: Los campos se validan mientras el usuario escribe
- **Estilos con Tailwind CSS**: Utiliza clases de Tailwind para mostrar estados válidos/inválidos
- **Mensajes personalizados**: Cada campo tiene mensajes de error específicos
- **Campos opcionales**: Algunos campos como `pagina_web` son opcionales

## Campos Validados

### Campos Requeridos
- **giro**: Textarea de 10-500 caracteres
- **contacto_nombre**: Nombre del contacto (2-100 caracteres, solo letras y espacios)
- **contacto_cargo**: Cargo del contacto (2-50 caracteres, solo letras y espacios)
- **contacto_correo**: Email válido
- **contacto_telefono**: Exactamente 10 dígitos

### Campos Opcionales
- **pagina_web**: URL válida (puede estar vacía)
- **sector_id**: ID numérico del sector (sin validación visual)

### Validación Especial
- **actividades_seleccionadas**: Se valida el contenedor de actividades seleccionadas, no el select individual

## Estilos de Validación

### Estados Válidos
- **Campos regulares**: Borde verde (`border-green-500`), fondo verde claro (`bg-green-50`), icono de check verde
- **Contenedor de actividades**: Borde verde (`border-green-300`), fondo con gradiente verde, efecto de pulso suave

### Estados Inválidos
- **Campos regulares**: Borde rojo (`border-red-500`), fondo rojo claro (`bg-red-50`), icono de X rojo
- **Contenedor de actividades**: Borde rojo (`border-red-300`), fondo con gradiente rojo, efecto de pulso
- Mensaje de error debajo del campo

## Funciones Públicas

- `window.validarDatosGenerales()`: Valida todo el formulario y retorna true/false
- `window.resetearValidacionesDatosGenerales()`: Resetea todas las validaciones

## Uso

El validador se carga automáticamente y se aplica a todos los campos editables del formulario con ID `datos-generales-form`.

```javascript
// Validar antes de enviar
if (window.validarDatosGenerales()) {
    // Formulario válido, proceder con el envío
} else {
    // Mostrar errores
}
```

## Integración

El validador está integrado con la función `guardarYSiguiente()` del formulario para validar automáticamente antes del envío. 