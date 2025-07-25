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
            'nome' => fake()->name(),
            'user' => fake()->name(),
            'apelido' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            //'email_verified_at' => now(),
            'senha' => bcrypt('senha'), // password
            //'remember_token' => Str::random(10),

            'cpf' => fake()->numerify('###.###.###-##'),
            'genero' => fake()->randomElement(['masculino', 'feminino', 'outro']),
            'data_nascimento' => fake()->dateTimeBetween('-67 years', '-18')->format('Y-m-d'),

            'cep'  =>  null,
            'logradouro'  =>  null,
            'endereco'  =>  null,
            'rua'  =>  null,
            'bairro'  =>  null,
            'numero'  =>  null,
            'cidade'  =>  null,
            'estado'  =>  null,
            'complemento' =>  null,

            'tipo_usuario' => 3, // 3 = comunidade (o user padrão)
            'status_conta' => 1, // 1 = ativo, 0 = inativo (para caso alguém seja banido ou apague sua conta)
        ];
    }
}
