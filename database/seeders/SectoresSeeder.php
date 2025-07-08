<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SectoresSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = public_path('json/sectores.json');
        
        if (!File::exists($jsonPath) || 
            !($data = json_decode(File::get($jsonPath), true)) || 
            !isset($data['Hoja1'])) {
            $this->command->error('Error: Archivo de sectores no encontrado o inválido');
            return;
        }

        DB::table('sector')->insert(
            collect($data['Hoja1'])->map(function ($item) {
                return [
                    'nombre' => $item['sector'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );
    }
}