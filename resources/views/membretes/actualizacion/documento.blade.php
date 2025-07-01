<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio de Actualización - Padrón de Proveedores</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page { size: 8.5in 11in; margin: 0; }
        body {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            width: 216mm;
            height: 279mm;
            position: relative;
            background: white;
            font-size: 8pt;
        }
        .header { position: absolute; top: 0; left: 0; width: 100%; height: auto; }
        .logo-encabezado {
            position: absolute; top: 10mm; left: 18mm;
            width: 85mm; height: 18mm;
        }
        .logo-lateral {
            position: absolute; top: 4mm; right: 4mm;
            width: 35mm; height: 292mm;
        }
        .lema-constitucional {
            position: absolute; top: 30mm; left: 28mm;
            width: 150mm; text-align: center;
            font-style: italic; font-size: 8pt;
        }
        .origen-oficio-asunto-fecha {
            position: absolute; top: 38mm; left: 50mm;
            width: 127mm; font-size: 8pt;
            text-align: right; line-height: 1.2;
            font-weight: bold;
        }
        .destinatario {
            position: absolute; top: 60mm; left: 19mm;
            width: 130mm; font-size: 8pt;
            font-weight: bold; line-height: 1.2;
        }
        .contenido-principal {
            position: absolute; top: 90mm; left: 19mm;
            width: 172mm; font-size: 8pt;
            text-align: justify; line-height: 1.4;
        }
        .firma {
            position: absolute; top: 210mm; left: 25mm;
            width: 160mm; text-align: center;
            font-size: 8pt; font-weight: bold;
            line-height: 1.5;
        }
        .signature-space { height: 10mm; }
        .footer {
            position: absolute; bottom: 17mm; left: 19mm;
            width: 180mm; font-size: 5pt;
            font-weight: bold; line-height: 1.2;
        }
        .copias {
            position: absolute; top: 228mm; left: 19mm;
            font-size: 5pt; line-height: 1.2;
        }
        .qr-code {
            position: absolute; bottom: 30mm; right: 25mm;
            width: 20mm; height: 20mm;
        }
        .qr-code svg { width: 100%; height: 100%; }
        .qr-text {
            position: absolute; bottom: 28mm; right: 25mm;
            width: 20mm; text-align: center;
            font-size: 3pt; color: #666;
        }
        @media print {
            body { print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo_encabezado2022.jpg') }}" alt="Logo Encabezado" class="logo-encabezado">
        <img src="{{ asset('images/logo_lateral2022.jpg') }}" alt="Logo Lateral" class="logo-lateral">
        <div class="lema-constitucional">
            "2025, BICENTENARIO DE LA PRIMERA CONSTITUCIÓN POLÍTICA DEL ESTADO LIBRE Y SOBERANO DE OAXACA"
        </div>
    </div>

    <div class="origen-oficio-asunto-fecha">
        ORIGEN: Dirección de Recursos Materiales<br>
        OFICIO No.: {{ $oficio->numero_oficio ?? 'SA/DRM/DMRA/ACT/003/2025' }}<br>
        ASUNTO: Actualización en el Padrón de Proveedores de la Administración Pública Estatal<br>
        Tlalixtac de Cabrera, Oax., {{ $fechaTexto ?? '1 de julio de 2025' }}
    </div>

    <div class="destinatario">
        <div class="destinatario-persona-moral">
            LIC. JUAN CARLOS PÉREZ GONZÁLEZ<br>
            REPRESENTANTE LEGAL DE EMPRESAS CONSTRUCTORAS DEL SUR S.A. DE C.V.<br>
            CALLE BENITO JUÁREZ NÚMERO EXTERIOR 123, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000<br>
            RFC: ECS850315ABC<br>
            P R E S E N T E
        </div>
    </div>

    <div class="contenido-principal">
        Se hace referencia a su solicitud de actualización ante el Padrón de Proveedores de la Administración Pública Estatal y anexos que acompaña fechada el 15 de enero de 2025, recibida en esta Dirección de Recursos Materiales el 16 de enero de 2025.
        <br><br>
        Sobre el particular, y en atención a la misma, una vez revisada y analizada, así como cotejados los documentos presentados en original, se informa que se procedió a la actualización ante el Padrón de Proveedores de la Administración Pública Estatal, de la persona moral "EMPRESAS CONSTRUCTORAS DEL SUR S.A. DE C.V.", cuyo giro y/o clasificación se establece de manera enunciativa mas no limitativa como a continuación se describe "CONSTRUCCIÓN, REMODELACIÓN Y MANTENIMIENTO DE OBRAS CIVILES, INDUSTRIALES Y COMERCIALES", y demás actividades comerciales, profesionales, mercantiles o de negocios de conformidad con sus actividades económicas y su objeto social registrado y autorizado, con cédula de inscripción 12345 asignada, que lo acredita como Proveedor Estatal, cuya vigencia será anual a partir del 16 DE ENERO DE 2025 hasta el 15 DE ENERO DE 2026, dejando constancia de ello en el expediente respectivo.
        <br><br>
        Así mismo, se informa que, para renovar o actualizar nuevamente este registro, deberá presentar su solicitud dentro de los siete días hábiles previos a su vencimiento. En caso de omitir la presentación en el plazo indicado, se cancelará el registro a su vencimiento. No obstante, podrá formular una nueva solicitud de inscripción. Es importante puntualizar que en cualquier momento, siempre que se encuentre vigente su registro, deberá comunicar a esta Secretaría a través de esta Dirección, las modificaciones legales, de capacidad técnica, económica o productiva y aquellas que puedan implicar un cambio en su giro y/o clasificación.
        <br><br>
        Por último, se exhorta a que en todos los trámites, procedimientos y contratos que celebre con las Dependencias o Entidades de la Administración Pública Estatal, se abstenga de adoptar conductas que vayan en contravención de la normatividad aplicable.
        <br><br>
        Lo anterior con fundamento en los artículos 1, 3 fracción XIV, 6, 11, 48, 49, 50, 51, 92, 93 y 94 de la Ley de Adquisiciones, Enajenaciones, Arrendamientos, Prestación de Servicios y Administración de Bienes Muebles e Inmuebles del Estado de Oaxaca, 46, 47, 48 y 49 de su Reglamento.
        <br><br>
        Sin otro particular, le reitero la seguridad de mi consideración distinguida.
    </div>

    <div class="firma">
        A T E N T A M E N T E.<br>
        SUFRAGIO EFECTIVO, NO REELECCIÓN.<br>
        "EL RESPETO AL DERECHO AJENO ES LA PAZ"<br>
        DIRECTORA DE RECURSOS MATERIALES<br>
        LIC. SARA ZÁRATE SANTIAGO<br>
        <div class="signature-space"></div>
    </div>

    <div class="copias">
        C.c.p.- Expediente y Minutario.<br>
        SZS/TEST
    </div>

    <div class="footer">
        Carretera Internacional Oaxaca-Istmo Km. 11.5, Ciudad Administrativa Benemérito de las Américas Edificio 2, Planta Baja, Tlalixtac de Cabrera, Oaxaca. C.P. 68270 Tel. Conmutador 01(951)5015000 Ext. 10004 y 10031.
    </div>

    <!-- Código QR para validación -->
    <div class="qr-code">
        {!! $qrCode !!}
    </div>
    <div class="qr-text">
        Validar documento
    </div>

    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" 
                style="background: #9d2449; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            Imprimir
        </button>
        <button onclick="window.close()" 
                style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 10px;">
            Cerrar
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const personaType = 'moral';
            if (personaType === 'fisica') {
                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html>

                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html>

                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html>

                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html>

                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html>

                const contenido = document.querySelector('.contenido-principal');
                contenido.classList.add('contenido-principal-fisica');
            }
        });
    </script>
</body>
</html>
