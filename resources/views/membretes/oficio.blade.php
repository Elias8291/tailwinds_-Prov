<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $numero_oficio }}</title>
    <style>
        @page {
            margin: 3cm 2.5cm 3cm 3cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        .header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            text-align: center;
        }
        .header img {
            height: 1.8cm;
        }
        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 2cm;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        .content {
            margin-top: 1cm;
        }
        .fecha {
            text-align: right;
            margin-bottom: 2cm;
        }
        .numero-oficio {
            margin-bottom: 1cm;
        }
        .destinatario {
            margin-bottom: 2cm;
        }
        .asunto {
            margin-bottom: 1cm;
            font-weight: bold;
        }
        .cuerpo {
            text-align: justify;
            margin-bottom: 2cm;
        }
        .firma {
            text-align: center;
            margin-top: 3cm;
        }
        .firma-nombre {
            font-weight: bold;
            margin-bottom: 0.5cm;
        }
        .firma-cargo {
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo-oaxaca.png') }}" alt="Logo Oaxaca">
    </div>

    <div class="footer">
        <p>
            Ciudad Administrativa, Edificio 4 "Juchitán", Nivel 2.<br>
            Carretera Oaxaca-Istmo Km. 11.5, Tlalixtac de Cabrera, Oaxaca C.P. 68270<br>
            Tel. 951 501 5000 | www.oaxaca.gob.mx
        </p>
    </div>

    <div class="content">
        <div class="fecha">
            Tlalixtac de Cabrera, Oaxaca, {{ $fecha }}
        </div>

        <div class="numero-oficio">
            <strong>OFICIO:</strong> {{ $numero_oficio }}
        </div>

        <div class="destinatario">
            <strong>{{ $tramite->solicitante->razon_social ?? $tramite->solicitante->nombre_completo }}</strong><br>
            {{ $tramite->solicitante->rfc }}<br>
            <strong>PRESENTE</strong>
        </div>

        <div class="asunto">
            ASUNTO: {{ $this->getAsunto($tipo_oficio) }}
        </div>

        <div class="cuerpo">
            {!! $this->getCuerpo($tipo_oficio, $tramite) !!}
        </div>

        <div class="firma">
            <div class="firma-nombre">
                MTRO. JOSÉ DE JESÚS SILVA PINEDA
            </div>
            <div class="firma-cargo">
                DIRECTOR DE PADRÓN DE PROVEEDORES
            </div>
        </div>
    </div>

    @php
        function getAsunto($tipo) {
            $asuntos = [
                'inscripcion' => 'INSCRIPCIÓN AL PADRÓN DE PROVEEDORES',
                'renovacion' => 'RENOVACIÓN DE REGISTRO EN EL PADRÓN DE PROVEEDORES',
                'actualizacion' => 'ACTUALIZACIÓN DE DATOS EN EL PADRÓN DE PROVEEDORES',
                'cancelacion' => 'CANCELACIÓN DE REGISTRO EN EL PADRÓN DE PROVEEDORES'
            ];
            return $asuntos[$tipo] ?? 'NOTIFICACIÓN DEL PADRÓN DE PROVEEDORES';
        }

        function getCuerpo($tipo, $tramite) {
            $proveedor = $tramite->solicitante;
            $nombre = $proveedor->razon_social ?? $proveedor->nombre_completo;
            
            switch ($tipo) {
                case 'inscripcion':
                    return "Por este medio me permito informarle que una vez revisada y validada la documentación presentada para su inscripción al Padrón de Proveedores de la Administración Pública Estatal, le comunico que ha quedado debidamente registrado con el número <strong>PV-{$tramite->proveedor->pv}</strong>.<br><br>
                    Su registro tendrá vigencia hasta el " . now()->addYear()->format('d/m/Y') . ", por lo que deberá realizar su renovación dentro de los 30 días naturales anteriores a su vencimiento.<br><br>
                    Sin otro particular, reciba un cordial saludo.";

                case 'renovacion':
                    return "En atención a su solicitud de renovación en el Padrón de Proveedores de la Administración Pública Estatal, le informo que una vez revisada y validada la documentación presentada, su registro <strong>PV-{$tramite->proveedor->pv}</strong> ha sido renovado.<br><br>
                    La vigencia de su renovación será hasta el " . now()->addYear()->format('d/m/Y') . ".<br><br>
                    Sin otro particular, reciba un cordial saludo.";

                case 'actualizacion':
                    return "En seguimiento a su solicitud de actualización de datos en el Padrón de Proveedores de la Administración Pública Estatal, le informo que los cambios solicitados han sido aplicados satisfactoriamente a su registro <strong>PV-{$tramite->proveedor->pv}</strong>.<br><br>
                    Esta actualización no modifica la vigencia de su registro actual.<br><br>
                    Sin otro particular, reciba un cordial saludo.";

                case 'cancelacion':
                    return "En atención a su solicitud, le informo que su registro <strong>PV-{$tramite->proveedor->pv}</strong> en el Padrón de Proveedores de la Administración Pública Estatal ha sido cancelado.<br><br>
                    Agradecemos su participación y le recordamos que puede solicitar una nueva inscripción cuando lo considere conveniente.<br><br>
                    Sin otro particular, reciba un cordial saludo.";

                default:
                    return "Por este medio me permito informarle sobre el estado de su trámite en el Padrón de Proveedores de la Administración Pública Estatal.<br><br>
                    Sin otro particular, reciba un cordial saludo.";
            }
        }
    @endphp
</body>
</html> 