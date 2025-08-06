<?php

namespace Database\Factories;
use App\Models\Postagem;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Postagem>
 */
class PostagemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo_postagem' => 'titulo',
            'texto_postagem' => 'texto',
            'data_postagem' => fake()->dateTime()->format('Y-m-d'),
            'usuario_id' => 1,
        ];
    }
}
