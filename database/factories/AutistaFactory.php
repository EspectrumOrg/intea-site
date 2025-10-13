<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autista>
 */
class AutistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cipteia_autista' => fake()->numerify('##.###.###-#'),
            'status_cipteia_autista' => 'Ativo',
            'responsavel_id' => null,
            'usuario_id' => 2,
        ];
    }
}
