<?php

namespace Database\Factories;

use App\Models\TipoDelito;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delito>
 */
class DelitoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'tipo_delito_id' => TipoDelito::inRandomOrder()->value('id'),
            'fecha_ocurrencia' => fake()->dateTime($max = 'now'),
            'latitud' => null,
            'longitud' => null,
            'deleted_at' => null
        ];
    }
}
