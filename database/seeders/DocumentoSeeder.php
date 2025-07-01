<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Documento;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentos = [
            [
                'nombre' => 'Identificación Oficial',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo de identificación oficial vigente con fotografía (para personas físicas o representante legal en caso de personas morales). PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Currículum Actualizado',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo del currículum actualizado con firma autógrafa (incluye giro, experiencia, clientes, recursos materiales y humanos). PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Comprobante de Domicilio Fiscal',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo del comprobante de domicilio fiscal, no mayor a 3 meses de antigüedad. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Croquis y Fotografías',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo del formato de croquis de localización y fotografías (exterior e interior) del domicilio fiscal. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Constancia de Situación Fiscal',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo de la constancia de situación fiscal (SHCP, no mayor a 3 meses de antigüedad). PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Carta Poder Simple',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo de carta poder simple (si aplica), con escaneo de identificación del aceptante, poderdante y dos testigos, dirigida al titular de la Secretaría de Administración. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Declaraciones de Impuestos',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Escaneo de acuse de última declaración anual y tres últimas declaraciones provisionales (o escrito explicativo si no aplica). PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Acta de Nacimiento',
                'tipo_persona' => 'Física',
                'descripcion' => 'Escaneo del acta de nacimiento (legible). PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'CURP',
                'tipo_persona' => 'Física',
                'descripcion' => 'Escaneo de Clave Única de Registro de Población actualizada. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Acta Constitutiva',
                'tipo_persona' => 'Moral',
                'descripcion' => 'Escaneo del acta constitutiva notariada, inscrita en el Registro Público de la Propiedad (y modificaciones, si las hay). PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Poder Notarial',
                'tipo_persona' => 'Moral',
                'descripcion' => 'Escaneo del poder general notariado para actos de administración. PDF, máximo 10MB.',
                'es_visible' => true
            ]
        ];

        foreach ($documentos as $documento) {
            Documento::create($documento);
        }
    }
}