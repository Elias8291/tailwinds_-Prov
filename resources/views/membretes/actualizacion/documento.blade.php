<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio de Actualización - Padrón de Proveedores</title>
    <style>
        @page {
            size: 8.5in 11in; /* 216mm x 279mm */
            margin: 0;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            width: 216mm;
            height: 279mm;
            position: relative;
            background: white;
        }

        /* Header Section */
        .header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
        }

        .logo-encabezado {
            position: absolute;
            top: 10mm;
            left: 18mm;
            width: 85mm;
            height: 18mm;
        }

        .logo-lateral {
            position: absolute;
            top: 4mm;
            right: 4mm;
            width: 35mm;
            height: 292mm;
        }

        .lema-constitucional {
            position: absolute;
            top: 30mm;
            left: 28mm;
            width: 150mm;
            text-align: center;
            font-style: italic;
            font-size: 10pt;
        }

        /* Document Info Section */
        .document-info {
            position: absolute;
            top: 38mm;
            right: 20mm;
            font-size: 8pt;
        }

        .document-info .label {
            font-weight: bold;
        }

        .origen {
            position: absolute;
            top: 38mm;
            left: 131mm;
            font-size: 8pt;
        }

        .oficio-no {
            position: absolute;
            top: 42mm;
            left: 134mm;
            font-size: 8pt;
        }

        .asunto {
            position: absolute;
            top: 46mm;
            left: 120mm;
            font-size: 8pt;
            width: 60mm;
        }

        .fecha-lugar {
            position: absolute;
            top: 56mm;
            left: 128mm;
            font-size: 8pt;
        }

        /* Recipient Section */
        .destinatario {
            position: absolute;
            top: 65mm;
            left: 19mm;
            width: 130mm;
            font-size: 8pt;
            font-weight: bold;
        }

        .destinatario-persona-moral {
            line-height: 1.2;
        }

        .destinatario-persona-fisica {
            line-height: 1.2;
        }

        /* Main Content */
        .contenido-principal {
            position: absolute;
            top: 96mm; /* Para persona moral */
            left: 19mm;
            width: 172mm;
            font-size: 8pt;
            text-align: justify;
            line-height: 1.4;
        }

        .contenido-principal-fisica {
            top: 91mm;
        }

        /* Signature Section */
        .firma {
            position: absolute;
            top: 214mm;
            left: 25mm;
            width: 160mm;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.3;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 17mm;
            left: 19mm;
            width: 180mm;
            font-size: 6pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .copias {
            position: absolute;
            bottom: 27mm;
            left: 19mm;
            font-size: 6pt;
            line-height: 1.2;
        }

        /* QR Code */
        .qr-code {
            position: absolute;
            bottom: 30mm;
            right: 25mm;
            width: 20mm;
            height: 20mm;
        }

        .qr-code svg {
            width: 100%;
            height: 100%;
        }

        .qr-text {
            position: absolute;
            bottom: 28mm;
            right: 25mm;
            width: 20mm;
            text-align: center;
            font-size: 5pt;
            color: #666;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .font-bold { font-weight: bold; }
        .font-italic { font-style: italic; }
        
        /* Print styles */
        @media print {
            body { print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <!-- Logo Principal -->
        <img src="{{ asset('images/logo_encabezado2022.jpg') }}" alt="Logo Encabezado" class="logo-encabezado">
        
        <!-- Logo Lateral -->
        <img src="{{ asset('images/logo_lateral2022.jpg') }}" alt="Logo Lateral" class="logo-lateral">
        
        <!-- Lema Constitucional -->
        <div class="lema-constitucional">
            "2025, BICENTENARIO DE LA PRIMERA CONSTITUCIÓN POLÍTICA DEL ESTADO LIBRE Y SOBERANO DE OAXACA"
        </div>
    </div>

    <!-- Document Information -->
    <div class="origen">
        <span class="label">ORIGEN:</span> Dirección de Recursos Materiales
    </div>

    <div class="oficio-no">
        <span class="label">OFICIO No.:</span> SA/DRM/DMRA/003/01/2025
    </div>

    <div class="asunto">
        <span class="label">ASUNTO:</span> Actualización de Datos en el Padrón de Proveedores de la Administración Pública Estatal
    </div>

    <div class="fecha-lugar">
        Tlalixtac de Cabrera, Oax., {{ date('d') }} de {{ ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'][date('n')] }} de {{ date('Y') }}.
    </div>

    <!-- Recipient Information -->
    <div class="destinatario">
        {{-- Para Persona Moral (este ejemplo) --}}
        <div class="destinatario-persona-moral">
            LIC. FERNANDO GONZÁLEZ MARTÍNEZ<br>
            REPRESENTANTE LEGAL DE CONSTRUCTORA DEL ISTMO S.A. DE C.V.<br>
            CARRETERA PANAMERICANA KM 5.5, COL. INDUSTRIAL, OAXACA DE JUÁREZ, OAXACA, C.P. 68020<br>
            RFC: CIS150920DEF<br>
            P R E S E N T E
        </div>
        
        {{-- Para Persona Física (comentado para este ejemplo)
        <div class="destinatario-persona-fisica" style="display: none;">
            LIC. FERNANDO GONZÁLEZ MARTÍNEZ<br>
            CARRETERA PANAMERICANA KM 5.5, COL. INDUSTRIAL, OAXACA DE JUÁREZ, OAXACA, C.P. 68020<br>
            RFC: GOMF750620ABC<br>
            P R E S E N T E
        </div>
        --}}
    </div>

    <!-- Main Content -->
    <div class="contenido-principal">
        Se hace referencia a su solicitud de actualización de datos ante el Padrón de Proveedores de la Administración Pública Estatal fechada el 08 de enero de 2025, recibida en esta Dirección de Recursos Materiales el 10 de enero de 2025.
        <br><br>
        Sobre el particular, y en atención a la misma, una vez revisada y analizada, así como cotejados los documentos presentados en original, se informa que se procedió a la actualización del registro ante el Padrón de Proveedores de la Administración Pública Estatal, de la persona moral "CONSTRUCTORA DEL ISTMO S.A. DE C.V.", con cédula de inscripción 54321 vigente.
        <br><br>
        Las modificaciones realizadas en su expediente son las siguientes:
        <br><br>
        <strong>ACTUALIZACIÓN DE DOMICILIO FISCAL:</strong> Se actualizó su domicilio fiscal de "CALLE 5 DE MAYO NÚMERO 789, COL. REFORMA, OAXACA DE JUÁREZ, OAXACA, C.P. 68050" al domicilio actual "CARRETERA PANAMERICANA KM 5.5, COL. INDUSTRIAL, OAXACA DE JUÁREZ, OAXACA, C.P. 68020".
        <br><br>
        <strong>AMPLIACIÓN DE GIRO COMERCIAL:</strong> Se amplió su giro comercial para incluir "SERVICIOS DE MANTENIMIENTO INDUSTRIAL Y COMERCIAL, INSTALACIONES ELÉCTRICAS Y MECÁNICAS", además de las actividades ya registradas de construcción de obras civiles.
        <br><br>
        Es importante mencionar que estas modificaciones no afectan la vigencia de su registro, el cual se mantiene hasta el 15 DE MARZO DE 2025, fecha en la cual deberá presentar su solicitud de renovación correspondiente.
        <br><br>
        Se le recuerda que en cualquier tiempo siempre que se encuentre vigente su registro, deberá comunicar a esta Secretaría a través de esta Dirección, las modificaciones legales, de capacidad técnica, económica o productiva y aquellas que puedan implicar un cambio en su giro y/o clasificación.
        <br><br>
        Por último, se exhorta a que en todos los trámites, procedimientos y contratos que celebre con las Dependencias o Entidades de la Administración Pública Estatal, se abstenga de adoptar conductas que vayan en contravención de la normatividad aplicable.
        <br><br>
        Lo anterior con fundamento en los artículos 1, 3 fracción XIV, 6, 11, 48, 49, 50, 51, 92, 93 y 94 de la Ley de Adquisiciones, Enajenaciones, Arrendamientos, Prestación de Servicios y Administración de Bienes Muebles e Inmuebles del Estado de Oaxaca, 46, 47, 48 y 49 de su Reglamento.
        <br><br>
        Sin otro particular, le reitero la seguridad de mi consideración distinguida.
    </div>

    <!-- Signature Section -->
    <div class="firma">
        A T E N T A M E N T E.<br>
        SUFRAGIO EFECTIVO, NO REELECCIÓN.<br>
        "EL RESPETO AL DERECHO AJENO ES LA PAZ"<br>
        DIRECTORA DE RECURSOS MATERIALES<br><br><br><br>
        LIC. SARA ZÁRATE SANTIAGO
    </div>

    <!-- Copies Section -->
    <div class="copias">
        C.c.p.- Expediente y Minutario.<br>
        SZS/ACTUA
    </div>

    <!-- QR Code -->
    @if(isset($qrCode))
    <div class="qr-code">
        {!! $qrCode !!}
    </div>
    <div class="qr-text">
        Verificar autenticidad
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Carretera Internacional Oaxaca-Istmo Km. 11.5, Ciudad Administrativa Benemérito de las Américas Edificio 2, Planta Baja, Tlalixtac de Cabrera, Oaxaca. C.P. 68270 Tel. Conmutador 01(951)5015000 Ext. 10004 y 10031.
    </div>

    <!-- Print Button (No Print) -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" 
                style="background: #9d2449; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="window.close()" 
                style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 10px;">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>

    <script>
        // Auto-adjust content for different persona types
        document.addEventListener('DOMContentLoaded', function() {
            // This would be dynamic based on data in real implementation
            const personaType = 'moral'; // 'moral' or 'fisica'
            
            if (personaType === 'fisica') {
                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html> 