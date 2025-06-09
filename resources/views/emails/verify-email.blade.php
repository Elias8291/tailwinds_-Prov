<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .logo {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background-color: #2d3748;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        .warning {
            background-color: #fed7d7;
            border-left: 4px solid #e53e3e;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #742a2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <svg width="30" height="30" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0L3 7v10c0 5.55 3.84 9.739 9 9.739s9-4.189 9-9.739V7L12 0z"/>
                </svg>
            </div>
            <h1 style="margin: 0; font-size: 28px;">Padrón de Proveedores</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Gobierno del Estado de Oaxaca</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-text">¡Bienvenido {{ $user->nombre }}!</div>
            
            <p>Gracias por registrarte en el Padrón de Proveedores del Gobierno del Estado de Oaxaca.</p>
            
            <p>Para completar tu registro y activar tu cuenta, necesitas verificar tu dirección de correo electrónico haciendo clic en el siguiente botón:</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Verificar mi cuenta
                </a>
            </div>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #2d3748;">Información importante:</h3>
                <ul style="margin-bottom: 0;">
                    <li>Este enlace expirará en <strong>{{ $expirationHours }} horas</strong></li>
                    <li>Solo necesitas hacer clic una vez para verificar tu cuenta</li>
                    <li>Una vez verificada, podrás acceder a todos los servicios</li>
                </ul>
            </div>

            <div class="warning">
                <strong>⚠️ Atención:</strong> Si no verificas tu cuenta en {{ $expirationHours }} horas, será eliminada automáticamente y tendrás que registrarte nuevamente.
            </div>

            <p>Si no puedes hacer clic en el botón, copia y pega este enlace en tu navegador:</p>
            <p style="word-break: break-all; color: #667eea; background-color: #f7fafc; padding: 10px; border-radius: 4px; font-family: monospace;">
                {{ $verificationUrl }}
            </p>

            <p>Si no solicitaste este registro, puedes ignorar este correo.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Padrón de Proveedores - Gobierno del Estado de Oaxaca</strong></p>
            <p>Este es un correo automático, por favor no respondas a esta dirección.</p>
            <p style="margin-bottom: 0;">© {{ date('Y') }} Gobierno del Estado de Oaxaca. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> 