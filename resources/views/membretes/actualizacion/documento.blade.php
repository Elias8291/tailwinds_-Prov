<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento Oficial - Gobierno de Oaxaca</title>
    <style>
        @page {
            margin: 0.5cm 0.7cm 1cm 0.7cm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            width: 100%;
            height: 120px;
            position: relative;
            margin-bottom: 20px;
        }
        
        .logo-izquierdo {
            position: absolute;
            left: 10px;
            top: 5px;
            max-height: 50px;
            max-width: 200px;
            width: auto;
            height: auto;
        }
        
        .logo-derecho {
            position: absolute;
            right: -10px;
            top: -5px;
            max-height: 280px;
            max-width: 150px;
            width: auto;
            height: auto;
            z-index: 10;
        }
        
        .lema {
            position: absolute;
            left: 50px;
            top: 65px;
            font-size: 7pt;
            font-style: italic;
            width: 350px;
            text-align: center;
            color: #666;
        }
        
        .datos-oficio {
            margin-top: 10px;
            margin-left: 320px;
            margin-right: 160px;
            font-size: 7pt;
            line-height: 1.1;
        }
        
        .origen {
            margin-bottom: 3px;
        }
        
        .numero-oficio {
            margin-bottom: 3px;
        }
        
        .asunto {
            margin-bottom: 8px;
            width: 180px;
        }
        
        .fecha-lugar {
            margin-top: 5px;
            margin-bottom: 15px;
            text-align: right;
            margin-right: 30px;
            font-size: 7pt;
        }
        
        .destinatario {
            margin: 20px 0;
            font-weight: bold;
            font-size: 8pt;
            margin-left: 20px;
            line-height: 1.2;
        }
        
        .contenido {
            margin: 20px;
            text-align: justify;
            font-size: 8pt;
            line-height: 1.3;
        }
        
        .cierre {
            margin: 30px 20px 20px 20px;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            line-height: 1.2;
        }
        
        .firma {
            margin-top: 40px;
        }
        
        .ccp {
            margin: 30px 20px 0 20px;
            font-size: 6pt;
            line-height: 1.1;
        }
        
        .footer {
            position: fixed;
            bottom: 10px;
            left: 20px;
            right: 20px;
            font-size: 6pt;
            text-align: left;
            color: #666;
            line-height: 1.1;
        }
        
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logoColor.png') }}" alt="Logo Gobierno de Oaxaca" class="logo-izquierdo">
        <img src="{{ public_path('images/membretep.png') }}" alt="Membrete Derecho" class="logo-derecho">
        
        <div class="lema">
            "2025, BICENTENARIO DE LA PRIMERA CONSTITUCIÓN POLÍTICA DEL ESTADO LIBRE Y SOBERANO DE OAXACA"
        </div>
    </div>

    <div class="datos-oficio">
        <div class="origen">
            <span class="bold">ORIGEN:</span> {{ $origen ?? 'Dirección de Recursos Materiales' }}
        </div>
        <div class="numero-oficio">
            <span class="bold">OFICIO No.:</span> {{ $numero_oficio ?? 'SA/DRM/DMRA/001/01/2025' }}
        </div>
        <div class="asunto">
            <span class="bold">ASUNTO:</span> {{ $asunto ?? 'Registro en el Padrón de Proveedores de la Administración Pública Estatal' }}
        </div>
    </div>

    <div class="fecha-lugar">
        {{ $lugar ?? 'Tlalixtac de Cabrera, Oax.' }}, {{ $fecha ?? date('d \d\e F \d\e Y') }}.
    </div>

    <div class="destinatario">
        @if(isset($tipo_persona) && $tipo_persona == 'MORAL')
            {{ $representante ?? 'LIC. JUAN PÉREZ GONZÁLEZ' }}<br>
            {{ $facultad ?? 'REPRESENTANTE LEGAL' }} DE {{ $razon_social ?? 'EMPRESA EJEMPLO S.A. DE C.V.' }}<br>
            {{ $domicilio ?? 'CALLE EJEMPLO NÚMERO EXTERIOR 123, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000' }}<br>
            RFC: {{ $rfc ?? 'EJE123456ABC' }}<br>
            P R E S E N T E
        @else
            {{ $destinatario_completo ?? 'LIC. MARÍA ELENA RODRÍGUEZ TORRES' }}<br>
            {{ $domicilio ?? 'CALLE EJEMPLO NÚMERO EXTERIOR 123, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000' }}<br>
            RFC: {{ $rfc ?? 'ROTM850315ABC' }}<br>
            P R E S E N T E
        @endif
    </div>

    <div class="contenido">
        @if(isset($contenido_personalizado))
            {!! nl2br(e($contenido_personalizado)) !!}
        @else
            Se hace referencia a su solicitud de actualización de datos en el Padrón de Proveedores de la Administración Pública Estatal fechada el {{ $fecha_solicitud ?? '15 de enero de 2025' }}, recibida en esta Dirección de Recursos Materiales el {{ $fecha_recepcion ?? '20 de enero de 2025' }}.
            <br><br>
            Al respecto, se informa que una vez revisada y analizada su solicitud, así como verificados los documentos presentados en original, se procedió a realizar la <strong>ACTUALIZACIÓN</strong> de sus datos en el Padrón de Proveedores de la Administración Pública Estatal.
            <br><br>
            Los cambios realizados en su registro con cédula {{ $cedula ?? 'PV-2025-001' }} incluyen: <strong>{{ $cambios_realizados ?? 'Actualización de datos generales' }}</strong>, quedando debidamente registrados en el expediente correspondiente.
            <br><br>
            Se le recuerda que es su responsabilidad mantener actualizada la información registrada en este Padrón de Proveedores. Cualquier modificación futura en sus datos legales, de capacidad técnica, económica o productiva, deberá ser comunicada oportunamente a esta Secretaría a través de esta Dirección.
            <br><br>
            Su registro mantiene su vigencia y estatus como Proveedor en el sistema, por lo que podrá continuar participando en los procesos de contratación que realicen las Dependencias y Entidades de la Administración Pública Estatal.
            <br><br>
            Se exhorta a que en todos los trámites, procedimientos y contratos que celebre con las Dependencias o Entidades de la Administración Pública Estatal, se abstenga de adoptar conductas que vayan en contravención de la normatividad aplicable y mantenga la veracidad de la información proporcionada.
            <br><br>
            Lo anterior con fundamento en los artículos 1, 3 fracción XIV, 6, 11, 48, 49, 50, 51, 92, 93 y 94 de la Ley de Adquisiciones, Enajenaciones, Arrendamientos, Prestación de Servicios y Administración de Bienes Muebles e Inmuebles del Estado de Oaxaca, 46, 47, 48 y 49 de su Reglamento.
            <br><br>
            Sin otro particular, le reitero la seguridad de mi consideración distinguida.
        @endif
    </div>

    <div class="cierre">
        <div class="bold">A T E N T A M E N T E.</div>
        <div class="bold">SUFRAGIO EFECTIVO, NO REELECCIÓN.</div>
        <div class="bold">"EL RESPETO AL DERECHO AJENO ES LA PAZ"</div>
        <div class="bold">{{ $cargo_firmante ?? 'DIRECTORA DE RECURSOS MATERIALES' }}</div>
        
        <div class="firma">
            <div class="bold">{{ $firmante ?? 'LIC. SARA ZÁRATE SANTIAGO' }}</div>
        </div>
    </div>

    <div class="ccp">
        C.c.p.- {{ $ccp ?? 'Expediente y Minutario.' }}<br>
        {{ $iniciales_firma ?? 'SZS/ERAG' }}
    </div>

    <div class="footer">
        Carretera Internacional Oaxaca-Istmo Km. 11.5, Ciudad Administrativa Benemérito de las Américas Edificio 2, Planta Baja, Tlalixtac de Cabrera, Oaxaca. C.P. 68270 Tel. Conmutador 01(951)5015000 Ext. 10004 y 10031.
    </div>
</body>
</html> 