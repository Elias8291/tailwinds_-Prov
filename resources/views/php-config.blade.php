<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración PHP - Verificación</title>
    <style>
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            max-width: 800px; 
            margin: 2rem auto; 
            padding: 1rem;
            background: #f8fafc;
        }
        .container { 
            background: white; 
            padding: 2rem; 
            border-radius: 0.5rem; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #1f2937; 
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .config-grid { 
            display: grid; 
            gap: 1rem; 
            margin-bottom: 2rem;
        }
        .config-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 1rem; 
            background: #f9fafb; 
            border-radius: 0.375rem;
            border-left: 4px solid #10b981;
        }
        .config-item.warning { border-left-color: #f59e0b; }
        .config-item.error { border-left-color: #ef4444; }
        .config-key { 
            font-weight: 600; 
            color: #374151;
        }
        .config-value { 
            font-family: Monaco, monospace; 
            background: #e5e7eb; 
            padding: 0.25rem 0.5rem; 
            border-radius: 0.25rem;
            color: #1f2937;
        }
        .status { 
            padding: 0.5rem 1rem; 
            border-radius: 0.375rem; 
            margin-bottom: 1rem;
        }
        .status.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status.warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status.error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn:hover { opacity: 0.9; }
        .timestamp {
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            🔧 Configuración PHP - Verificación de Límites
        </h1>

        @php
            $uploadMB = (int) filter_var($config['upload_max_filesize'], FILTER_SANITIZE_NUMBER_INT);
            $postMB = (int) filter_var($config['post_max_size'], FILTER_SANITIZE_NUMBER_INT);
            $memoryMB = (int) filter_var($config['memory_limit'], FILTER_SANITIZE_NUMBER_INT);
            
            $allGood = $uploadMB >= 100 && $postMB >= 100 && $memoryMB >= 512;
        @endphp

        @if($allGood)
            <div class="status success">
                ✅ <strong>¡Excelente!</strong> Todos los límites están configurados correctamente para archivos de 100MB.
            </div>
        @else
            <div class="status error">
                ❌ <strong>Problema detectado:</strong> Los límites no son suficientes para archivos de 100MB.
            </div>
        @endif

        <div class="config-grid">
            @foreach($config as $key => $value)
                @php
                    $itemClass = 'config-item';
                    if ($key === 'upload_max_filesize' && $uploadMB < 100) $itemClass .= ' error';
                    elseif ($key === 'post_max_size' && $postMB < 100) $itemClass .= ' error';
                    elseif ($key === 'memory_limit' && $memoryMB < 512) $itemClass .= ' warning';
                @endphp
                
                <div class="{{ $itemClass }}">
                    <span class="config-key">{{ $key }}</span>
                    <span class="config-value">{{ $value }}</span>
                </div>
            @endforeach
        </div>

        <div class="status warning">
            <strong>💡 Recomendaciones para archivos de 100MB:</strong><br>
            • upload_max_filesize: ≥ 100M<br>
            • post_max_size: ≥ 110M (10% más que upload_max_filesize)<br>
            • memory_limit: ≥ 512M<br>
            • max_execution_time: ≥ 300 segundos
        </div>

        <div class="actions">
            <a href="{{ route('tramites.solicitante.index') }}" class="btn btn-primary">
                🚀 Ir a Trámites
            </a>
            <button onclick="window.location.reload()" class="btn btn-secondary">
                🔄 Recargar
            </button>
        </div>

        <div class="timestamp">
            Verificado el {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html> 