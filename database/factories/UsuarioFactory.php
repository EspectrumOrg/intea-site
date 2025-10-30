<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user' => fake()->name(),
            'apelido' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            //'email_verified_at' => now(),
            'senha' => bcrypt('123456'), // todas as senha são 123456
            //'remember_token' => Str::random(10),

            'cpf' => fake()->numerify('###########'),
            'genero' => fake()->randomElement(['1', '2', '3', '4']),
            'data_nascimento' => fake()->dateTimeBetween('-67 years', '-18')->format('Y-m-d'),

            'tipo_usuario' => 3, // 3 = comunidade (o user padrão)
            'status_conta' => 1, // 1 = ativo, 0 = inativo (para caso alguém seja banido ou apague sua conta)
        ];
    }
}
