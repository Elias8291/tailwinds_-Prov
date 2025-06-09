<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Gobierno de Oaxaca</title>
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
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .logo-text {
            text-align: left;
        }
        .logo-title {
            font-size: 18px;
            font-weight: bold;
            line-height: 1.2;
            margin: 0;
        }
        .logo-subtitle {
            font-size: 12px;
            opacity: 0.8;
            margin: 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            margin-bottom: 30px;
            color: #6b7280;
            line-height: 1.7;
        }
        .reset-button {
            display: block;
            width: fit-content;
            margin: 30px auto;
            padding: 15px 40px;
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }
        .security-notice {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 6px;
        }
        .security-notice h3 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .security-notice p {
            color: #92400e;
            margin: 0;
            font-size: 14px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0;
        }
        .footer .government-info {
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .link-fallback {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            word-break: break-all;
            font-size: 14px;
            color: #6b7280;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .reset-button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0L3 7v10c0 5.55 3.84 9.739 9 9.739s9-4.189 9-9.739V7L12 0z"/>
                    </svg>
                </div>
                <div class="logo-text">
                    <div class="logo-title">ADMINISTRACIÓN</div>
                    <div class="logo-subtitle">Gobierno de Oaxaca</div>
                </div>
            </div>
            <h1>Restablecer Contraseña</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">¡Hola!</div>
            
            <div class="message">
                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en el <strong>Padrón de Proveedores de Oaxaca</strong>.</p>
                
                <p>Si solicitaste restablecer tu contraseña, haz clic en el siguiente botón:</p>
            </div>

            <a href="{{ $resetUrl }}" class="reset-button">
                🔑 RESTABLECER CONTRASEÑA
            </a>

            <div class="security-notice">
                <h3>⚠️ Información de Seguridad</h3>
                <p><strong>Este enlace expirará en 60 minutos</strong> por seguridad. Si no solicitaste restablecer tu contraseña, puedes ignorar este correo de forma segura.</p>
            </div>

            <div class="message">
                <p><strong>Para tu seguridad:</strong></p>
                <ul>
                    <li>Este enlace solo puede ser usado una vez</li>
                    <li>Nunca compartas este enlace con otras personas</li>
                    <li>Si no fuiste tú quien solicitó el cambio, ignora este correo</li>
                </ul>
            </div>

            <div class="link-fallback">
                <strong>¿Problemas con el botón?</strong><br>
                Copia y pega este enlace en tu navegador:<br>
                <a href="{{ $resetUrl }}" style="color: #1e40af;">{{ $resetUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="government-info">Gobierno del Estado de Oaxaca</p>
            <p>Padrón de Proveedores - Sistema Oficial</p>
            <p>Este es un correo automático, por favor no responder.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
                Si tienes problemas técnicos, contacta al administrador del sistema.
            </p>
        </div>
    </div>
</body>
</html> 