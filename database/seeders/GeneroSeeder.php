<?php

namespace Database\Seeders;

use App\Models\Genero;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Criar gêneros padrão no site
        Genero::factory()->create([
            'titulo' => 'masculino',
        ]);

        Genero::factory()->create([
            'titulo' => 'feminino',
        ]);

        Genero::factory()->create([
            'titulo' => 'não-binário',
        ]);

        Genero::factory()->create([
            'titulo' => 'homem trans',
        ]);

        Genero::factory()->create([
            'titulo' => 'mulher trans',
        ]);

        Genero::factory()->create([
            'titulo' => 'agênero',
        ]);

        Genero::factory()->create([
            'titulo' => 'gênero fluido',
        ]);

        Genero::factory()->create([
            'titulo' => 'prefere não informar',
        ]);
    }
}
