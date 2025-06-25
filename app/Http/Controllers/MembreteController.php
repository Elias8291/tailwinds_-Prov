<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class MembreteController extends Controller
{
    /**
     * Muestra el índice de membretes disponibles
     */
    public function index()
    {
        return view('membretes.index');
    }

    /**
     * Genera un PDF de ejemplo de inscripción
     */
    public function ejemploInscripcion()
    {
        $datos = [
            'origen' => 'Dirección de Recursos Materiales',
            'numero_oficio' => 'SA/DRM/DMRA/143/01/2025',
            'asunto' => 'Registro en el Padrón de Proveedores de la Administración Pública Estatal',
            'lugar' => 'Tlalixtac de Cabrera, Oax.',
            'fecha' => $this->obtenerFechaTexto(),
            
            'tipo_persona' => 'MORAL',
            'representante' => 'ING. CARLOS EDUARDO MARTÍNEZ LÓPEZ',
            'facultad' => 'ADMINISTRADOR ÚNICO',
            'razon_social' => 'SOLUCIONES TECNOLÓGICAS INNOVADORAS S.A. DE C.V.',
            'domicilio' => 'BOULEVARD EDUARDO VASCONCELOS NÚMERO EXTERIOR 1532, COLONIA SANTA ROSA, OAXACA DE JUÁREZ, OAXACA, C.P. 68050',
            'rfc' => 'STI190815ABC',
            
            'fecha_solicitud' => '22 de enero de 2025',
            'fecha_recepcion' => '25 de enero de 2025',
            'tipo_juridica' => 'moral',
            'nombre_proveedor' => 'SOLUCIONES TECNOLÓGICAS INNOVADORAS S.A. DE C.V.',
            'giro' => 'Desarrollo de software, consultoría en tecnologías de la información, servicios de soporte técnico',
            'cedula' => 'PV-2025-0143',
            'tipo_proveedor' => 'Estatal',
            'fecha_inicio' => '25 DE ENERO DE 2025',
            'fecha_vigencia' => '24 DE ENERO DE 2026',
            'fraccion' => 'XIV',
            
            'cargo_firmante' => 'DIRECTORA DE RECURSOS MATERIALES',
            'firmante' => 'LIC. SARA ZÁRATE SANTIAGO',
            'ccp' => 'Expediente y Minutario.',
            'iniciales_firma' => 'SZS/CEMG',
        ];

        $pdf = $this->generarPDF('membretes.inscripcion.documento', $datos);
        return $pdf->download('ejemplo_oficio_inscripcion.pdf');
    }

    /**
     * Genera un PDF de ejemplo de renovación
     */
    public function ejemploRenovacion()
    {
        $datos = [
            'origen' => 'Dirección de Recursos Materiales',
            'numero_oficio' => 'SA/DRM/DMRA/201/01/2025',
            'asunto' => 'Renovación de Registro en el Padrón de Proveedores',
            'lugar' => 'Tlalixtac de Cabrera, Oax.',
            'fecha' => $this->obtenerFechaTexto(),
            
            'tipo_persona' => 'FISICA',
            'destinatario_completo' => 'LIC. MARÍA ELENA RODRÍGUEZ TORRES',
            'domicilio' => 'CALLE INDEPENDENCIA NÚMERO 456, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000',
            'rfc' => 'ROTM850315ABC',
            
            'fecha_solicitud' => '15 de enero de 2025',
            'fecha_recepcion' => '18 de enero de 2025',
            'cedula_anterior' => 'PV-2024-0089',
            'cedula_nueva' => 'PV-2025-0201',
            'fecha_vencimiento' => '20 DE ENERO DE 2025',
            'fecha_inicio' => '21 DE ENERO DE 2025',
            'fecha_vigencia' => '20 DE ENERO DE 2026',
            
            'cargo_firmante' => 'DIRECTORA DE RECURSOS MATERIALES',
            'firmante' => 'LIC. SARA ZÁRATE SANTIAGO',
            'ccp' => 'Expediente y Minutario.',
            'iniciales_firma' => 'SZS/LMGR',
        ];

        $pdf = $this->generarPDF('membretes.renovacion.documento', $datos);
        return $pdf->download('ejemplo_oficio_renovacion.pdf');
    }

    /**
     * Genera un PDF de ejemplo de actualización
     */
    public function ejemploActualizacion()
    {
        $datos = [
            'origen' => 'Dirección de Recursos Materiales',
            'numero_oficio' => 'SA/DRM/DMRA/301/01/2025',
            'asunto' => 'Actualización de Datos en el Padrón de Proveedores',
            'lugar' => 'Tlalixtac de Cabrera, Oax.',
            'fecha' => $this->obtenerFechaTexto(),
            
            'tipo_persona' => 'MORAL',
            'representante' => 'LIC. FERNANDO GONZÁLEZ MARTÍNEZ',
            'facultad' => 'REPRESENTANTE LEGAL',
            'razon_social' => 'CONSTRUCTORA DEL ISTMO S.A. DE C.V.',
            'domicilio' => 'CARRETERA PANAMERICANA KM 5.5, COL. INDUSTRIAL, OAXACA DE JUÁREZ, OAXACA, C.P. 68020',
            'rfc' => 'CIS150920DEF',
            
            'fecha_solicitud' => '10 de enero de 2025',
            'fecha_recepcion' => '12 de enero de 2025',
            'cedula' => 'PV-2024-0156',
            'cambios_realizados' => 'Actualización de domicilio fiscal y ampliación de giro comercial',
            
            'cargo_firmante' => 'DIRECTORA DE RECURSOS MATERIALES',
            'firmante' => 'LIC. SARA ZÁRATE SANTIAGO',
            'ccp' => 'Expediente y Minutario.',
            'iniciales_firma' => 'SZS/JHLM',
        ];

        $pdf = $this->generarPDF('membretes.actualizacion.documento', $datos);
        return $pdf->download('ejemplo_oficio_actualizacion.pdf');
    }

    private function generarPDF($vista, $datos)
    {
        $pdf = PDF::loadView($vista, $datos);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'enable_remote' => true,
            'chroot' => realpath(base_path()),
        ]);

        return $pdf;
    }

    private function obtenerFechaTexto()
    {
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $dia = date('d');
        $mes = $meses[(int)date('m')];
        $año = date('Y');
        
        return "{$dia} de {$mes} de {$año}";
    }
}