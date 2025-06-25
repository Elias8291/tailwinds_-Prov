# 🤖 Módulo de Inteligencia Artificial para Validación de Documentos

## 📋 Descripción

Este módulo integra capacidades de inteligencia artificial para automatizar la validación y clasificación de documentos en tu aplicación Laravel. El sistema puede:

- ✅ Reconocer automáticamente tipos de documentos
- 📊 Entrenar modelos personalizados
- 🔍 Validar documentos con diferentes niveles de confianza
- 📈 Proporcionar estadísticas y retroalimentación
- 🚀 Integrarse automáticamente con el sistema existente

## 🛠️ Instalación

### 1. Dependencias

Las siguientes dependencias ya fueron agregadas al `composer.json`:
```bash
composer install
```

### 2. Ejecutar Migraciones

```bash
php artisan migrate
```

Esto creará las tablas:
- `ai_document_models` - Modelos de IA entrenados
- `ai_training_data` - Datos de entrenamiento
- `ai_validation_results` - Resultados de validaciones

### 3. Configuración (Opcional)

Agregar al archivo `.env`:
```env
# Habilitar análisis automático de documentos
AI_AUTO_ANALYSIS=true

# Configuración de almacenamiento para modelos de IA
AI_MODELS_DISK=private
```

### 4. Registrar Middleware (Opcional)

Para análisis automático al subir documentos, agregar al `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ... otros middlewares
    \App\Http\Middleware\AutoAIDocumentAnalysis::class,
];
```

## 🚀 Uso del Sistema

### Panel Web

1. **Dashboard Principal**: `/ai/dashboard`
   - Vista general del estado del sistema
   - Estadísticas de modelos y validaciones

2. **Entrenamiento**: `/ai/training`
   - Subir documentos de entrenamiento
   - Validar datos manualmente
   - Procesar documentos existentes

3. **Modelos**: `/ai/models`
   - Ver modelos entrenados
   - Activar/desactivar modelos
   - Ver estadísticas de precisión

4. **Validaciones**: `/ai/validation`
   - Revisar resultados de IA
   - Aprobar/rechazar validaciones
   - Supervisión humana

### Comandos Artisan

#### Entrenar un modelo
```bash
# Entrenamiento interactivo
php artisan ai:train-model "Mi Modelo v1.0"

# Especificar tipos de documentos
php artisan ai:train-model "Modelo Fiscal" --types="Constancia de Situación Fiscal,Acta de Nacimiento"

# Configurar algoritmo y muestras mínimas
php artisan ai:train-model "Modelo Avanzado" --algorithm=enhanced --min-samples=15
```

#### Analizar documentos
```bash
# Analizar documentos pendientes
php artisan ai:analyze-documents

# Analizar lote específico
php artisan ai:analyze-documents --batch-size=50

# Forzar re-análisis
php artisan ai:analyze-documents --force

# Analizar todos los estados
php artisan ai:analyze-documents --status=all
```

## 📚 Flujo de Trabajo

### 1. Preparar Datos de Entrenamiento

1. Ve a `/ai/training`
2. Sube documentos PDF de ejemplo
3. Clasifica cada documento manualmente
4. Valida la información extraída

### 2. Entrenar Modelo

**Vía Web:**
1. Ve a `/ai/training`
2. Haz clic en "Entrenar Modelo"
3. Selecciona tipos de documentos
4. Configura parámetros

**Vía Comandos:**
```bash
php artisan ai:train-model "Mi Primer Modelo"
```

### 3. Activar Modelo

1. Ve a `/ai/models`
2. Selecciona el modelo entrenado
3. Haz clic en "Activar"

### 4. Análisis Automático

Una vez activado el modelo:
- Los documentos se analizan automáticamente al subirlos
- Los resultados se muestran en `/ai/validation`
- Puedes revisar y corregir manualmente

## 🔧 Características Técnicas

### Extracción de Características

El sistema extrae:
- **Texto**: Contenido completo del PDF
- **Metadatos**: Autor, fecha de creación, propiedades
- **Estructura**: Número de páginas, densidad de texto
- **Patrones**: Palabras clave, formatos específicos

### Algoritmos de Clasificación

**Actual: Basado en Reglas**
- Análisis de palabras clave
- Patrones de texto específicos
- Metadatos del documento
- Heurísticas configurables

**Futuro: Machine Learning**
- Clasificación con redes neuronales
- Procesamiento de lenguaje natural
- Análisis de imágenes OCR
- Aprendizaje continuo

### Niveles de Confianza

- 🟢 **Alta (≥90%)**: Aprobación automática
- 🟡 **Media (70-89%)**: Revisión recomendada
- 🔴 **Baja (<70%)**: Revisión obligatoria

## 📊 API Endpoints

### Analizar Documento
```javascript
POST /ai/analyze
{
    "documento_solicitante_id": 123
}
```

### Obtener Estadísticas
```javascript
GET /ai/stats
```

### Ejemplo de Integración JavaScript
```javascript
// Analizar documento automáticamente
async function analyzeDocument(documentId) {
    const response = await fetch('/ai/analyze', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            documento_solicitante_id: documentId
        })
    });
    
    const result = await response.json();
    
    if (result.success) {
        console.log('Tipo predicho:', result.result.predicted_type);
        console.log('Confianza:', result.result.confidence);
    }
}
```

## 🛡️ Seguridad y Privacidad

- ✅ Los documentos se almacenan de forma segura
- ✅ Los datos de entrenamiento están encriptados
- ✅ Solo usuarios autenticados pueden acceder al módulo
- ✅ Registro detallado de todas las operaciones
- ✅ Los modelos se almacenan localmente

## 📈 Monitoreo y Mejora

### Métricas Clave
- Precisión del modelo
- Tiempo de análisis
- Tasa de aprobación manual
- Distribución de confianza

### Mejora Continua
1. Revisar validaciones rechazadas
2. Agregar más datos de entrenamiento
3. Re-entrenar modelos periódicamente
4. Ajustar umbrales de confianza

## 🔄 Mantenimiento

### Limpieza Periódica
```bash
# Limpiar datos de entrenamiento no validados (30+ días)
php artisan ai:cleanup-training-data --days=30

# Archivar modelos antiguos
php artisan ai:archive-old-models --keep=3
```

### Respaldos
- Respaldar tablas `ai_*` regularmente
- Incluir archivos de modelos en `/storage/app/ai_models/`
- Considerar exportar configuraciones de modelos

## 🆘 Solución de Problemas

### Problemas Comunes

**Error: "No hay modelo activo"**
- Verifica que hay un modelo entrenado y activado
- Ejecuta: `php artisan ai:train-model "Modelo Inicial"`

**Baja precisión del modelo**
- Aumenta datos de entrenamiento
- Valida manualmente más documentos
- Re-entrena con más tipos de documentos

**Documentos no se analizan automáticamente**
- Verifica que `AI_AUTO_ANALYSIS=true` en `.env`
- Confirma que el middleware está registrado
- Revisa logs en `storage/logs/laravel.log`

### Logs de Depuración
```bash
# Ver logs de IA específicamente
tail -f storage/logs/laravel.log | grep "AI:"

# Habilitar logs detallados
php artisan config:set logging.level debug
```

## 🔮 Roadmap Futuro

### Versión 2.0
- [ ] Integración con servicios de IA externos (AWS Textract, Google Vision)
- [ ] Análisis de imágenes y documentos escaneados
- [ ] Procesamiento en tiempo real con colas
- [ ] API REST completa para integraciones externas

### Versión 3.0
- [ ] Machine Learning real con TensorFlow/PyTorch
- [ ] Análisis de firmas y sellos
- [ ] Detección de documentos fraudulentos
- [ ] Integración con blockchain para trazabilidad

## 📞 Soporte

Para soporte técnico:
1. Revisa los logs en `/storage/logs/`
2. Consulta la documentación en `/ai/dashboard`
3. Ejecuta diagnósticos: `php artisan ai:diagnose`

---

**¡El módulo de IA está listo para revolucionar tu validación de documentos! 🚀** 