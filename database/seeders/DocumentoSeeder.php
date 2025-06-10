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
                'nombre' => 'Acta Constitutiva',
                'tipo_persona' => 'Moral',
                'descripcion' => 'Documento que acredita la constitución legal de la empresa. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Poder Notarial',
                'tipo_persona' => 'Moral',
                'descripcion' => 'Documento que acredita las facultades del representante legal. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Identificación Oficial',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'INE, Pasaporte o Cédula Profesional vigente. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Comprobante de Domicilio',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'No mayor a 3 meses de antigüedad. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'Constancia de Situación Fiscal',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Actualizada, no mayor a 3 meses. PDF, máximo 10MB.',
                'es_visible' => true
            ],
            [
                'nombre' => 'CURP',
                'tipo_persona' => 'Física',
                'descripcion' => 'Clave Única de Registro de Población actualizada. PDF, máximo 10MB.',
                'es_visible' => true
            ]
        ];

        foreach ($documentos as $documento) {
            Documento::create($documento);
        }
    }
}