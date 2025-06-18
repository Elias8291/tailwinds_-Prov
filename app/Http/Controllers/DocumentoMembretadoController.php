<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DocumentoMembretadoController extends Controller
{
    /**
     * Genera un documento oficial en PDF con formato del Gobierno de Oaxaca
     *
     * @param Request $request
     * @return Response
     */
    public function generarPDF(Request $request)
    {
        // Datos del encabezado del oficio
        $datos = [
            'origen' => $request->input('origen', 'Dirección de Recursos Materiales'),
            'numero_oficio' => $request->input('numero_oficio', 'SA/DRM/DMRA/001/01/2025'),
            'asunto' => $request->input('asunto', 'Registro en el Padrón de Proveedores de la Administración Pública Estatal'),
            'lugar' => $request->input('lugar', 'Tlalixtac de Cabrera, Oax.'),
            'fecha' => $request->input('fecha', $this->obtenerFechaTexto()),
            
            // Datos del destinatario
            'tipo_persona' => $request->input('tipo_persona', 'FISICA'),
            'representante' => $request->input('representante', 'LIC. JUAN PÉREZ GONZÁLEZ'),
            'facultad' => $request->input('facultad', 'REPRESENTANTE LEGAL'),
            'razon_social' => $request->input('razon_social', 'EMPRESA EJEMPLO S.A. DE C.V.'),
            'destinatario_completo' => $request->input('destinatario_completo', 'LIC. MARÍA ELENA RODRÍGUEZ TORRES'),
            'domicilio' => $request->input('domicilio', 'CALLE EJEMPLO NÚMERO EXTERIOR 123, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000'),
            'rfc' => $request->input('rfc', 'ROTM850315ABC'),
            
            // Datos del contenido (específicos para padrón de proveedores)
            'fecha_solicitud' => $request->input('fecha_solicitud', '15 de enero de 2025'),
            'fecha_recepcion' => $request->input('fecha_recepcion', '20 de enero de 2025'),
            'tipo_juridica' => $request->input('tipo_juridica', 'física'),
            'nombre_proveedor' => $request->input('nombre_proveedor', 'EMPRESA EJEMPLO S.A. DE C.V.'),
            'giro' => $request->input('giro', 'Servicios profesionales de consultoría en tecnologías de la información y comunicación'),
            'cedula' => $request->input('cedula', 'PV-2025-001'),
            'tipo_proveedor' => $request->input('tipo_proveedor', 'Estatal'),
            'fecha_inicio' => $request->input('fecha_inicio', '20 DE ENERO DE 2025'),
            'fecha_vigencia' => $request->input('fecha_vigencia', '19 DE ENERO DE 2026'),
            'fraccion' => $request->input('fraccion', 'XIV'),
            
            // Datos de la firma
            'cargo_firmante' => $request->input('cargo_firmante', 'DIRECTORA DE RECURSOS MATERIALES'),
            'firmante' => $request->input('firmante', 'LIC. SARA ZÁRATE SANTIAGO'),
            'ccp' => $request->input('ccp', 'Expediente y Minutario.'),
            'iniciales_firma' => $request->input('iniciales_firma', 'SZS/ERAG'),
            
            // Contenido personalizado (opcional)
            'contenido_personalizado' => $request->input('contenido_personalizado'),
        ];

        // Generar el PDF
        $pdf = PDF::loadView('documentos.membrete', $datos);
        
        // Configurar el PDF con opciones mejoradas para imágenes
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'enable_remote' => true,
            'chroot' => realpath(base_path()),
        ]);

        $nombreArchivo = 'oficio_' . ($datos['numero_oficio'] ?? 'documento') . '_' . date('Y-m-d') . '.pdf';
        $nombreArchivo = str_replace(['/', '\\'], '_', $nombreArchivo);

        return $pdf->download($nombreArchivo);
    }

    /**
     * Muestra la vista previa del documento en el navegador
     *
     * @param Request $request
     * @return Response
     */
    public function vista(Request $request)
    {
        // Datos del encabezado del oficio
        $datos = [
            'origen' => $request->input('origen', 'Dirección de Recursos Materiales'),
            'numero_oficio' => $request->input('numero_oficio', 'SA/DRM/DMRA/001/01/2025'),
            'asunto' => $request->input('asunto', 'Registro en el Padrón de Proveedores de la Administración Pública Estatal'),
            'lugar' => $request->input('lugar', 'Tlalixtac de Cabrera, Oax.'),
            'fecha' => $request->input('fecha', $this->obtenerFechaTexto()),
            
            // Datos del destinatario
            'tipo_persona' => $request->input('tipo_persona', 'FISICA'),
            'representante' => $request->input('representante', 'LIC. JUAN PÉREZ GONZÁLEZ'),
            'facultad' => $request->input('facultad', 'REPRESENTANTE LEGAL'),
            'razon_social' => $request->input('razon_social', 'EMPRESA EJEMPLO S.A. DE C.V.'),
            'destinatario_completo' => $request->input('destinatario_completo', 'LIC. MARÍA ELENA RODRÍGUEZ TORRES'),
            'domicilio' => $request->input('domicilio', 'CALLE EJEMPLO NÚMERO EXTERIOR 123, COL. CENTRO, OAXACA DE JUÁREZ, OAXACA, C.P. 68000'),
            'rfc' => $request->input('rfc', 'ROTM850315ABC'),
            
            // Datos del contenido
            'fecha_solicitud' => $request->input('fecha_solicitud', '15 de enero de 2025'),
            'fecha_recepcion' => $request->input('fecha_recepcion', '20 de enero de 2025'),
            'tipo_juridica' => $request->input('tipo_juridica', 'física'),
            'nombre_proveedor' => $request->input('nombre_proveedor', 'EMPRESA EJEMPLO S.A. DE C.V.'),
            'giro' => $request->input('giro', 'Servicios profesionales de consultoría en tecnologías de la información y comunicación'),
            'cedula' => $request->input('cedula', 'PV-2025-001'),
            'tipo_proveedor' => $request->input('tipo_proveedor', 'Estatal'),
            'fecha_inicio' => $request->input('fecha_inicio', '20 DE ENERO DE 2025'),
            'fecha_vigencia' => $request->input('fecha_vigencia', '19 DE ENERO DE 2026'),
            'fraccion' => $request->input('fraccion', 'XIV'),
            
            // Datos de la firma
            'cargo_firmante' => $request->input('cargo_firmante', 'DIRECTORA DE RECURSOS MATERIALES'),
            'firmante' => $request->input('firmante', 'LIC. SARA ZÁRATE SANTIAGO'),
            'ccp' => $request->input('ccp', 'Expediente y Minutario.'),
            'iniciales_firma' => $request->input('iniciales_firma', 'SZS/ERAG'),
            
            // Contenido personalizado (opcional)
            'contenido_personalizado' => $request->input('contenido_personalizado'),
        ];

        return view('documentos.membrete', $datos);
    }

    /**
     * Muestra el formulario para crear un documento oficial
     *
     * @return \Illuminate\View\View
     */
    public function formulario()
    {
        return view('documentos.formulario');
    }

    /**
     * Genera un PDF de ejemplo con formato oficial del Gobierno de Oaxaca
     *
     * @return Response
     */
    public function ejemplo()
    {
        $datos = [
            'origen' => 'Dirección de Recursos Materiales',
            'numero_oficio' => 'SA/DRM/DMRA/143/01/2025',
            'asunto' => 'Registro en el Padrón de Proveedores de la Administración Pública Estatal',
            'lugar' => 'Tlalixtac de Cabrera, Oax.',
            'fecha' => $this->obtenerFechaTexto(),
            
            // Ejemplo de persona moral
            'tipo_persona' => 'MORAL',
            'representante' => 'ING. CARLOS EDUARDO MARTÍNEZ LÓPEZ',
            'facultad' => 'ADMINISTRADOR ÚNICO',
            'razon_social' => 'SOLUCIONES TECNOLÓGICAS INNOVADORAS S.A. DE C.V.',
            'domicilio' => 'BOULEVARD EDUARDO VASCONCELOS NÚMERO EXTERIOR 1532, COLONIA SANTA ROSA, OAXACA DE JUÁREZ, OAXACA, C.P. 68050',
            'rfc' => 'STI190815ABC',
            
            // Datos específicos del trámite
            'fecha_solicitud' => '22 de enero de 2025',
            'fecha_recepcion' => '25 de enero de 2025',
            'tipo_juridica' => 'moral',
            'nombre_proveedor' => 'SOLUCIONES TECNOLÓGICAS INNOVADORAS S.A. DE C.V.',
            'giro' => 'Desarrollo de software, consultoría en tecnologías de la información, servicios de soporte técnico, implementación de sistemas empresariales y capacitación en tecnologías digitales',
            'cedula' => 'PV-2025-0143',
            'tipo_proveedor' => 'Estatal',
            'fecha_inicio' => '25 DE ENERO DE 2025',
            'fecha_vigencia' => '24 DE ENERO DE 2026',
            'fraccion' => 'XIV',
            
            // Firma
            'cargo_firmante' => 'DIRECTORA DE RECURSOS MATERIALES',
            'firmante' => 'LIC. SARA ZÁRATE SANTIAGO',
            'ccp' => 'Expediente y Minutario.',
            'iniciales_firma' => 'SZS/CEMG',
        ];

        $pdf = PDF::loadView('documentos.membrete', $datos);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'enable_remote' => true,
            'chroot' => realpath(base_path()),
        ]);

        return $pdf->download('oficio_ejemplo_padron_proveedores.pdf');
    }

    /**
     * Convierte la fecha actual al formato de texto usado en documentos oficiales
     *
     * @return string
     */
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