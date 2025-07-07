<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita - Padrón de Proveedores</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: letter;
            margin: 0;
        }
        
        body {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .container {
            padding: 40px;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #9d2449;
            padding-bottom: 20px;
        }

        .logo {
            max-width: 300px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            color: #9d2449;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .location-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .location-title {
            font-size: 16px;
            font-weight: 600;
            color: #9d2449;
            margin-bottom: 10px;
        }

        .documents-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #9d2449;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .documents-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .documents-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .documents-list li:last-child {
            border-bottom: none;
        }

        .document-icon {
            margin-right: 10px;
            color: #9d2449;
        }

        .important-note {
            background: #fff3f3;
            border: 1px solid #ffd7d7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .important-title {
            font-size: 16px;
            font-weight: 600;
            color: #9d2449;
            margin-bottom: 10px;
        }

        .important-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .important-list li {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .important-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #9d2449;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .qr-code {
            position: absolute;
            bottom: 100px;
            right: 40px;
            text-align: center;
        }

        .qr-code img {
            width: 100px;
            height: 100px;
        }

        .qr-text {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo_encabezado2022.jpg') }}" alt="Logo" class="logo">
            <div class="title">Confirmación de Cita</div>
            <div class="subtitle">Cotejo de Documentos - Padrón de Proveedores</div>
        </div>

        <div class="info-section">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Solicitante</div>
                    <div class="info-value">
                        {{ strtoupper($tramite->solicitante->tipo_persona === 'Moral' ? 
                            $tramite->solicitante->razon_social : 
                            $tramite->solicitante->nombre) }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">RFC</div>
                    <div class="info-value">{{ $tramite->solicitante->rfc }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha</div>
                    <div class="info-value">{{ $tramite->cita->fecha_hora->translatedFormat('j \d\e F \d\e Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Hora</div>
                    <div class="info-value">{{ $tramite->cita->fecha_hora->format('H:i') }} hrs</div>
                </div>
            </div>

            <div class="location-box">
                <div class="location-title">Ubicación de la Cita</div>
                <div class="info-value">
                    Ciudad Administrativa<br>
                    Edificio 1, Nivel 1, Módulo de Proveedores<br>
                    Internacional 8, San Miguel 2da Secc,<br>
                    68270 Tlalixtac de Cabrera, Oax.
                </div>
            </div>
        </div>

        <div class="documents-section">
            <div class="section-title">Documentos a Presentar</div>
            <ul class="documents-list">
                @foreach($documentosRequeridos as $doc)
                    <li>
                        <span class="document-icon">📄</span>
                        {{ $doc->nombre }}
                    </li>
                @endforeach
                <li>
                    <span class="document-icon">🪪</span>
                    Identificación oficial vigente 
                    @if($tramite->solicitante->tipo_persona === 'Moral')
                        del representante legal
                    @endif
                </li>
            </ul>
        </div>

        <div class="important-note">
            <div class="important-title">Información Importante</div>
            <ul class="important-list">
                <li>Favor de presentarse 15 minutos antes de su cita</li>
                <li>Todos los documentos deben presentarse en original</li>
                @if($tramite->solicitante->tipo_persona === 'Moral')
                    <li>Solo el representante legal {{ $nombreRepresentante ? '(' . $nombreRepresentante . ')' : '' }} puede realizar el cotejo</li>
                @endif
                <li>En caso de no asistir, deberá agendar una nueva cita</li>
            </ul>
        </div>

        <div class="footer">
            Ciudad Administrativa • Edificio 1, Nivel 1 • Tel: (951) 501-5000 ext. 10004 y 10031
        </div>

        <div class="qr-code">
            {!! QrCode::size(100)->generate(url('/citas/validar/' . $tramite->cita->id)) !!}
            <div class="qr-text">Escanea para validar</div>
        </div>
    </div>
</body>
</html>
