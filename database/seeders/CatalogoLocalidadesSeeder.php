<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, File};

class CatalogoLocalidadesSeeder extends Seeder
{
    public function run()
    {
        if (!$data = json_decode(File::get(public_path('json/localidades.json')), true)) {
            return $this->command->error('Error al cargar localidades.json');
        }

        DB::table('localidades_catalogo')->insert(
            collect($data['Sheet1'] ?? [])->map(fn($item) => [
                'municipio_id' => $item['municipality_id'],
                'nombre' => $item['name'],
                'created_at' => now(),
                'updated_at' => now()
            ])->toArray()
        );
    }
}