<?php

namespace Database\Seeders;

use App\Models\Delito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DelitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Delito::factory(1)
            ->create([
                'latitud' => '-22.726274360099218',
                'longitud' => '-64.32563695806367'
            ]);

        Delito::factory(1)
            ->create([
                'latitud' => '-22.727243257247956',
                'longitud' => '-64.32675075608424',
                'deleted_at' =>now()
            ]);

        Delito::factory(1)
            ->create([
                'latitud' => '-22.727243257247956',
                'longitud' => '-64.32675075608424'
            ]);
    }
}
