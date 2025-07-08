<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiasInhabiles;
use Carbon\Carbon;

class DiasInhabilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando días inhábiles...');

        $diasInhabiles = [
            // Días festivos oficiales (establecidos por ley)
            [
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2025-01-01',
                'descripcion' => 'Año Nuevo',
            ],
            [
                'fecha_inicio' => '2025-02-03',
                'fecha_fin' => '2025-02-03',
                'descripcion' => 'Día de la Constitución',
            ],
            [
                'fecha_inicio' => '2025-03-17',
                'fecha_fin' => '2025-03-17',
                'descripcion' => 'Natalicio de Benito Juárez',
            ],
            [
                'fecha_inicio' => '2025-05-01',
                'fecha_fin' => '2025-05-01',
                'descripcion' => 'Día del Trabajo',
            ],
            [
                'fecha_inicio' => '2025-09-16',
                'fecha_fin' => '2025-09-16',
                'descripcion' => 'Día de la Independencia',
            ],
            [
                'fecha_inicio' => '2025-11-17',
                'fecha_fin' => '2025-11-17',
                'descripcion' => 'Día de la Revolución Mexicana',
            ],
            [
                'fecha_inicio' => '2025-12-25',
                'fecha_fin' => '2025-12-25',
                'descripcion' => 'Navidad',
            ],
            
            [
                'fecha_inicio' => '2025-04-14',
                'fecha_fin' => '2025-04-25',
                'descripcion' => 'Vacaciones de Semana Santa',
            ],

            [
                'fecha_inicio' => '2025-07-07',
                'fecha_fin' => '2025-08-29',
                'descripcion' => 'Vacaciones de Verano',
            ],

            [
                'fecha_inicio' => '2025-12-20',
                'fecha_fin' => '2026-01-06',
                'descripcion' => 'Vacaciones de Invierno',
            ],

            [
                'fecha_inicio' => '2025-05-05',
                'fecha_fin' => '2025-05-05',
                'descripcion' => 'Batalla de Puebla',
            ],
            [
                'fecha_inicio' => '2025-11-02',
                'fecha_fin' => '2025-11-02',
                'descripcion' => 'Día de Muertos',
            ],
            [
                'fecha_inicio' => '2025-12-12',
                'fecha_fin' => '2025-12-12',
                'descripcion' => 'Día de la Virgen de Guadalupe',
            ],
        ];

        foreach ($diasInhabiles as $dia) {
            DiasInhabiles::create($dia);
        }

        $this->command->info('✅ Días inhábiles creados exitosamente');
    }
}