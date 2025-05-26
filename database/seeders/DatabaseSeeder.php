<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create([
            'name' => 'Usuario',
            'email' => 'usuario@example.com',
        ]);

        User::factory(1)->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
        ]);

        $this->call([
            TipoDelitoSeeder::class,
            DelitoSeeder::class,
        ]);
    }
}
