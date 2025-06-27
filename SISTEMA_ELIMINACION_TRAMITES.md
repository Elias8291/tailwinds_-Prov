# Sistema de Eliminación Automática de Trámites Vencidos

## Descripción
Sistema que elimina automáticamente los trámites que no se han completado en 48 horas desde su fecha de inicio.

## Funcionamiento

### Criterios de Eliminación
Un trámite es considerado **vencido** y será eliminado si cumple TODAS las siguientes condiciones:

1. **Tiene fecha_inicio establecida** (`fecha_inicio IS NOT NULL`)
2. **NO tiene fecha_finalizacion** (`fecha_finalizacion IS NULL`) - No se completó
3. **Han pasado más de 48 horas** desde `fecha_inicio` (configurable)

### Eliminación en Cascada
Cuando se elimina un trámite vencido, se eliminan automáticamente:

1. **Progreso del trámite** (`progreso_tramite`)
2. **Secciones de revisión** (`seccion_revision`)
3. **Relaciones many-to-many**:
   - Actividades del trámite (`actividad_solicitante`)
   - Accionistas del trámite (`accionista_solicitante`)
4. **Documentos del solicitante** (`documento_solicitante`)
5. **Detalle del trámite** (`detalle_tramite`)
6. **El trámite principal** (`tramite`)
7. **Solicitante sin trámites** (si no tiene más trámites asociados)

## Comando Artisan

### Comando Principal
```bash
php artisan tramites:eliminar-vencidos
```

### Opciones Disponibles

#### `--force`
Ejecuta la eliminación sin pedir confirmación del usuario
```bash
php artisan tramites:eliminar-vencidos --force
```

#### `--hours=N`
Especifica el número de horas para considerar un trámite como vencido (default: 48)
```bash
php artisan tramites:eliminar-vencidos --hours=24
```

#### `--dry-run`
Muestra qué trámites serían eliminados sin eliminarlos realmente
```bash
php artisan tramites:eliminar-vencidos --dry-run
```

### Ejemplos de Uso

```bash
# Eliminar trámites vencidos después de 24 horas (con confirmación)
php artisan tramites:eliminar-vencidos --hours=24

# Ver qué trámites serían eliminados después de 72 horas
php artisan tramites:eliminar-vencidos --hours=72 --dry-run

# Eliminación automática (usada por el scheduler)
php artisan tramites:eliminar-vencidos --force --hours=48
```

## Programación Automática

### Configuración en `routes/console.php`
```php
// Programar la eliminación automática de trámites vencidos (48 horas)
Schedule::command('tramites:eliminar-vencidos --force')
    ->hourly() // Se ejecuta cada hora
    ->name('eliminar-tramites-vencidos')
    ->description('Elimina automáticamente trámites que han pasado 48 horas sin completarse')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();
```

### Ejecución del Scheduler
Para que el sistema funcione automáticamente, asegúrate de que el cron job de Laravel esté configurado:

```bash
# Agregar a crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Verificar Programación
```bash
# Ver comandos programados
php artisan schedule:list

# Ejecutar scheduler manualmente (para pruebas)
php artisan schedule:run
```

## Logging y Monitoreo

### Logs Generados
El sistema genera logs detallados en `storage/logs/laravel.log`:

```
🗑️ Eliminando trámite vencido
🗑️ Eliminando solicitante sin trámites
🏁 Proceso de eliminación de trámites vencidos completado
```

### Información Registrada
- ID del trámite eliminado
- Tipo de trámite (Inscripción, Renovación, Actualización)
- Fecha de inicio del trámite
- ID del solicitante
- Progreso alcanzado
- Resumen de la operación (eliminados, errores)
- Configuración utilizada (horas límite, modo automático)

## Seguridad y Precauciones

### Transacciones de Base de Datos
- Cada eliminación se ejecuta dentro de una transacción
- Si ocurre un error, se hace rollback automático
- Solo se confirman eliminaciones exitosas

### Prevención de Solapamiento
- `withoutOverlapping()`: Evita que se ejecuten múltiples instancias simultáneamente
- `onOneServer()`: En entornos multi-servidor, solo se ejecuta en uno

### Confirmación de Usuario
- Por defecto, pide confirmación antes de eliminar
- Modo `--force` omite la confirmación (para automático)
- Modo `--dry-run` permite simular sin eliminar

## Casos de Uso

### 1. Limpieza Automática (Recomendado)
El sistema ejecuta automáticamente cada hora y elimina trámites vencidos después de 48 horas.

### 2. Limpieza Manual Periódica
```bash
# Revisar trámites que serían eliminados
php artisan tramites:eliminar-vencidos --dry-run

# Eliminar tras confirmación
php artisan tramites:eliminar-vencidos
```

### 3. Limpieza de Emergencia
```bash
# Eliminar trámites muy antiguos (7 días)
php artisan tramites:eliminar-vencidos --hours=168 --force
```

### 4. Mantenimiento Preventivo
```bash
# Verificar trámites próximos a vencer (36 horas)
php artisan tramites:eliminar-vencidos --hours=36 --dry-run
```

## Beneficios

1. **Limpieza Automática**: Mantiene la base de datos limpia sin intervención manual
2. **Mejora del Rendimiento**: Reduce el tamaño de la base de datos
3. **Experiencia de Usuario**: Elimina trámites abandonados que podrían confundir a los usuarios
4. **Gestión de Recursos**: Libera espacio de almacenamiento y mejora consultas
5. **Auditoría Completa**: Registra todas las operaciones para seguimiento

## Monitoreo Recomendado

1. **Verificar logs diariamente** para detectar errores
2. **Revisar métricas** de trámites eliminados vs. completados
3. **Monitorear rendimiento** de la base de datos
4. **Alertas** si se eliminan muchos trámites de una vez (posible problema)

Este sistema asegura que los trámites abandonados no acumulen datos innecesarios en el sistema, manteniendo un ambiente limpio y eficiente. 