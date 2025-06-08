<?php

namespace Database\Seeders;

use App\Models\Documento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'nombre' => 'Constancia de Situación Fiscal',
                'descripcion' => 'Original, vigente, emitido por el SAT, no mayor a 3 meses',
                'tipo_persona' => 'Moral',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Identificación Oficial',
                'descripcion' => 'Original vigente (INE, pasaporte o cédula profesional)',
                'tipo_persona' => 'Física',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Currículum Empresarial',
                'descripcion' => 'Original, con giro, experiencia, clientes y recursos',
                'tipo_persona' => 'Física',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Comprobante de Domicilio',
                'descripcion' => 'Copia simple, no mayor a 3 meses',
                'tipo_persona' => 'Ambas',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Acta Constitutiva',
                'descripcion' => 'Original, del domicilio del proveedor',
                'tipo_persona' => 'Moral',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Carta Poder',
                'descripcion' => 'Original, con identificación del aceptante, si aplica',
                'tipo_persona' => 'Ambas',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Declaraciones Fiscales',
                'descripcion' => 'Copia simple, última declaración anual y provisionales',
                'tipo_persona' => 'Moral',
                'es_visible' => true,
            ],
            [
                'nombre' => 'Acta de Asamblea',
                'descripcion' => 'Copia simple, notariada',
                'tipo_persona' => 'Moral',
                'es_visible' => true,
            ],
        ];

        foreach ($documentos as $documento) {
            Documento::create($documento);
        }
    }
}