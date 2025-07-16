<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, File};

class CatalogoMunicipiosSeeder extends Seeder
{
    public function run()
    {
        if (!$data = json_decode(File::get(public_path('json/municipios.json')), true)) {
            return $this->command->error('Error al cargar municipios.json');
        }

        DB::table('municipios_catalogo')->insert(
            collect($data['Sheet1'] ?? [])->map(fn($item) => [
                'estado_id' => $item['state_id'],
                'nombre' => $item['name'],
                'created_at' => now(),
                'updated_at' => now()
            ])->toArray()
        );
    }
}
