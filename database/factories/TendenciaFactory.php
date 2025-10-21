<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tendencia>
 */
class TendenciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hashtag' => 'hashtag',
            'slug' => 'slug',
            'contador_uso' => 'contador_uso',
            'ultimo_uso' => 'ultimo_uso',
        ];
    }
}
