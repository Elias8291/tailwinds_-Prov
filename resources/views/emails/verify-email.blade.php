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
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 8px 24px rgba(149, 26, 29, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #800000 0%, #5c0000 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #b30000 0%, #800000 50%, #b30000 100%);
        }
        .logo {
            width: 80px;
            height: 80px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .content {
            padding: 40px 30px;
            background: linear-gradient(180deg, #ffffff 0%, #f9f9f9 100%);
        }
        .welcome-text {
            font-size: 28px;
            font-weight: bold;
            color: #800000;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #800000 0%, #5c0000 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            margin: 25px 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(128, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 0, 0, 0.3);
            background: linear-gradient(135deg, #990000 0%, #800000 100%);
        }
        .info-box {
            background-color: #fff;
            border-left: 4px solid #800000;
            padding: 25px;
            margin: 25px 0;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }
        .footer {
            background-color: #2a0000;
            color: #e6c9c9;
            padding: 35px;
            text-align: center;
            font-size: 14px;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        .warning {
            background-color: #fff5f5;
            border-left: 4px solid #cc0000;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            color: #800000;
            box-shadow: 0 2px 12px rgba(204, 0, 0, 0.1);
        }
        .verification-link {
            word-break: break-all;
            color: #800000;
            background-color: #fff5f5;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            border: 1px dashed #cc0000;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <svg width="40" height="40" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0L3 7v10c0 5.55 3.84 9.739 9 9.739s9-4.189 9-9.739V7L12 0z"/>
                </svg>
            </div>
            <h1 style="margin: 0; font-size: 32px; font-weight: 600;">Padrón de Proveedores</h1>
            <p style="margin: 15px 0 0 0; opacity: 0.9; font-size: 18px;">Gobierno del Estado de Oaxaca</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-text">¡Bienvenido {{ $user->nombre }}!</div>
            
            <p style="font-size: 16px; color: #444;">Gracias por registrarte en el Padrón de Proveedores del Gobierno del Estado de Oaxaca. Nos complace tenerte como parte de nuestra comunidad de proveedores.</p>
            
            <p style="font-size: 16px; color: #444;">Para activar tu cuenta y comenzar a disfrutar de todos nuestros servicios, por favor verifica tu dirección de correo electrónico:</p>

            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Verificar mi cuenta
                </a>
            </div>

            <div style="background: linear-gradient(135deg, #800000 0%, #5c0000 100%); border-radius: 15px; padding: 30px; color: white; margin: 30px 0; box-shadow: 0 8px 24px rgba(149, 26, 29, 0.15);">
                <h3 style="margin: 0 0 20px 0; font-size: 20px; text-align: center; border-bottom: 2px solid rgba(255,255,255,0.2); padding-bottom: 15px;">
                    <svg style="width: 24px; height: 24px; margin-right: 8px; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.477.859h4z"/>
                    </svg>
                    Información Importante
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
                    <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <svg style="width: 30px; height: 30px; margin-bottom: 10px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Enlace activo por<br><strong>{{ $expirationHours }} horas</strong></p>
                    </div>
                    
                    <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <svg style="width: 30px; height: 30px; margin-bottom: 10px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Un solo clic<br><strong>para verificar</strong></p>
                    </div>
                    
                    <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <svg style="width: 30px; height: 30px; margin-bottom: 10px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Acceso completo<br><strong>a la plataforma</strong></p>
                    </div>
                </div>

                <div style="background: rgba(255, 255, 255, 0.95); padding: 20px; border-radius: 10px; color: #800000; margin-top: 20px; text-align: center; border-left: 5px solid #ff9999;">
                    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <svg style="width: 24px; height: 24px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <strong style="font-size: 16px; color: #800000;">Recordatorio Importante</strong>
                    </div>
                    <p style="margin: 0; color: #666;">Si no verificas tu cuenta en las próximas {{ $expirationHours }} horas,<br>deberás iniciar el proceso de registro nuevamente.</p>
                </div>
            </div>

            <p style="color: #444;">Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
            <div class="verification-link">
                {{ $verificationUrl }}
            </div>

            <p style="color: #666; font-style: italic;">Si no has solicitado este registro, puedes ignorar este mensaje de forma segura.</p>
        </div>

        <!-- Footer -->
        <div style="background: linear-gradient(135deg, #800000 0%, #5c0000 100%); color: white; padding: 35px; text-align: center; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; position: relative;">
            <!-- Línea decorativa superior -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, rgba(255,255,255,0.1), rgba(255,255,255,0.5), rgba(255,255,255,0.1));"></div>
            
            <!-- Logo pequeño -->
            <div style="margin-bottom: 20px;">
                <svg width="40" height="40" fill="rgba(255,255,255,0.9)" viewBox="0 0 24 24" style="margin: 0 auto;">
                    <path d="M12 0L3 7v10c0 5.55 3.84 9.739 9 9.739s9-4.189 9-9.739V7L12 0z"/>
                </svg>
            </div>

            <h2 style="color: white; font-size: 20px; font-weight: 600; margin: 0 0 15px 0; text-transform: uppercase; letter-spacing: 1px;">
                Padrón de Proveedores
            </h2>
            <p style="color: white; font-size: 16px; margin: 0 0 5px 0; font-weight: 500;">
                Gobierno del Estado de Oaxaca
            </p>
            
            <!-- Línea divisoria -->
            <div style="width: 60px; height: 2px; background: rgba(255,255,255,0.3); margin: 20px auto;"></div>
            
            <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin: 15px 0;">
                Este es un correo automático. Por favor, no responda a este mensaje.
            </p>
            
            <p style="color: rgba(255,255,255,0.7); font-size: 12px; margin: 0;">
                © {{ date('Y') }} Gobierno del Estado de Oaxaca. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html> 