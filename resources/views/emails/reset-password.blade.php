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
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(128, 0, 0, 0.15);
        }
        .header {
            background: linear-gradient(135deg, #800000 0%, #4a0000 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        .logo {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .logo::after {
            content: '🔒';
            font-size: 32px;
            position: absolute;
            bottom: -10px;
            right: -10px;
            background: #990000;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .content {
            padding: 40px 30px;
            background: linear-gradient(180deg, #ffffff 0%, #fff5f5 100%);
        }
        .greeting {
            font-size: 28px;
            color: #800000;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
        }
        .message {
            color: #4a4a4a;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #800000 0%, #4a0000 100%);
            color: white;
            padding: 18px 45px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(128, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(128, 0, 0, 0.25);
        }
        .reset-button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0) 100%);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        .security-box {
            background: linear-gradient(135deg, #800000 0%, #4a0000 100%);
            border-radius: 16px;
            padding: 30px;
            color: white;
            margin: 35px 0;
            position: relative;
            overflow: hidden;
        }
        .security-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.1;
        }
        .security-box h3 {
            display: flex;
            align-items: center;
            font-size: 20px;
            margin: 0 0 20px 0;
        }
        .security-box h3 svg {
            margin-right: 10px;
            width: 24px;
            height: 24px;
        }
        .security-points {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 25px;
        }
        .security-point {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }
        .security-point svg {
            width: 24px;
            height: 24px;
            margin-bottom: 10px;
        }
        .link-fallback {
            background: #fff5f5;
            border: 1px dashed #cc0000;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
            word-break: break-all;
            font-size: 14px;
            color: #800000;
        }
        .link-fallback strong {
            display: block;
            margin-bottom: 10px;
            color: #4a0000;
        }
        .footer {
            background: linear-gradient(135deg, #800000 0%, #4a0000 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, rgba(255,255,255,0.1), rgba(255,255,255,0.5), rgba(255,255,255,0.1));
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
            <h1 style="font-size: 32px; font-weight: 600; margin: 0 0 10px 0;">Restablecer Contraseña</h1>
            <p style="margin: 0; opacity: 0.9; font-size: 18px;">Padrón de Proveedores - Gobierno de Oaxaca</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">¡Hola!</div>
            
            <div class="message">
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el <strong>Padrón de Proveedores de Oaxaca</strong>. Tu seguridad es importante para nosotros, por lo que hemos generado un enlace seguro para que puedas crear una nueva contraseña.</p>
            </div>

            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Restablecer Contraseña
                </a>
            </div>

            <div class="security-box">
                <h3>
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Información de Seguridad
                </h3>
                <p style="margin: 0 0 20px 0; font-size: 16px;">Por tu seguridad, este enlace expirará en <strong>60 minutos</strong>. Si no solicitaste este cambio, puedes ignorar este mensaje de forma segura.</p>
                
                <div class="security-points">
                    <div class="security-point">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Uso único</p>
                    </div>
                    <div class="security-point">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Enlace personal</p>
                    </div>
                    <div class="security-point">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px;">Expira pronto</p>
                    </div>
                </div>
            </div>

            <div class="link-fallback">
                <strong>¿Problemas con el botón?</strong>
                Copia y pega este enlace en tu navegador:
                <br>
                <a href="{{ $resetUrl }}" style="color: #800000; word-break: break-all;">{{ $resetUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
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