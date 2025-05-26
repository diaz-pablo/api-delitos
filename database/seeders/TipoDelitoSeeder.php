<?php

namespace Database\Seeders;

use App\Models\TipoDelito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDelitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDelito::factory(1)
            ->create([
                'descripcion' => 'Homicidio',
            ]);

        TipoDelito::factory(1)
            ->create([
                'descripcion' => 'Robo',
            ]);

        TipoDelito::factory(1)
            ->create([
                'descripcion' => 'Lesion',
            ]);
    }
}
